# Sécurité & notifications temps réel

Documentation interne du back-office KhalilShop : comment le système détecte les tentatives
d'intrusion, bloque les adresses IP suspectes, et fait remonter les alertes/notifications dans
le back-office sans recharger la page.

Reflète l'implémentation en place dans `app/Services/SecurityMonitor.php`,
`app/Http/Middleware/CheckBlockedIp.php` et `resources/views/components/notification-bell.blade.php`.

## 1. Le blocage d'adresses IP

Chaque requête qui arrive sur KhalilShop passe d'abord par un vérificateur global
(`CheckBlockedIp`) : si son adresse IP est marquée comme bloquée, la requête s'arrête
immédiatement avec une erreur 403 — avant même d'atteindre une page ou une route. Une IP peut
se retrouver bloquée de deux façons : **automatiquement**, quand le système détecte une force
brute, ou **manuellement**, quand un membre du staff la bloque depuis le tableau de bord
Sécurité.

### Les seuils de déclenchement

| Seuil | Valeur |
|---|---|
| Échecs de connexion consécutifs déclenchant l'alerte | **5** |
| Fenêtre de temps dans laquelle ces échecs sont comptés | **15 min** |
| Durée du blocage automatique qui en résulte | **30 min** |

### Le parcours d'une tentative de force brute

1. **Tentative de connexion** — Le formulaire de connexion (`/connexion`) est déjà limité en
   amont par Laravel : 5 tentatives par minute et par IP, toutes réponses confondues
   (`throttle:5,1`). C'est la première barrière, indépendante du système décrit ici.
2. **Échec journalisé** — Chaque identifiant/mot de passe incorrect déclenche
   `SecurityMonitor::recordFailedLogin()`, qui enregistre un événement `failed_login`
   (sévérité *warning*) avec l'IP, l'email tenté et l'horodatage.
3. **Seuil dépassé → blocage automatique** — Si la même IP totalise 5 échecs sur les 15
   dernières minutes, elle est ajoutée à la liste des IP bloquées pour 30 minutes, et un
   événement `brute_force` (sévérité *critical*) est journalisé.
4. **Le staff est prévenu** — Tous les comptes Gestionnaire / Admin / Super admin reçoivent
   une notification immédiate (« Force brute détectée depuis `203.0.113.42` — bloquée
   automatiquement 30 min »).

Le blocage manuel suit le même mécanisme de vérification, mais sans passer par ce compteur : un
admin saisit une IP et un motif depuis le dashboard, avec une durée optionnelle — sans durée, le
blocage reste actif jusqu'à un déblocage manuel.

### Ce qui est tracé

| Type d'événement | Déclenché quand… | Sévérité |
|---|---|---|
| `failed_login` | Un identifiant ou mot de passe incorrect est soumis | Avertissement |
| `brute_force` | 5 échecs depuis la même IP en moins de 15 minutes | Critique |
| `unauthorized_access` | Un compte non-staff tente d'atteindre une page du back-office | Avertissement |
| `ip_blocked` | Une IP est bloquée, automatiquement ou manuellement | Info |
| `ip_unblocked` | Un admin débloque une IP depuis le dashboard | Info |

### Ce que ce système n'est pas

Il s'agit d'une surveillance **applicative**, au niveau du code Laravel — pas d'un IDS/IPS
réseau comme Snort ou Suricata. Il voit tout ce qui passe par l'application (connexions, accès
back-office), mais pas le trafic réseau brut, et ne fait pas d'inspection de paquets ni de
détection d'anomalies au niveau TCP/IP.

## 2. Les notifications « temps réel »

Les notifications du back-office (nouvelle commande, retour demandé, alerte de sécurité…)
utilisent le système de notifications intégré à Laravel : chaque notification est un
enregistrement dans une table `notifications`, rattaché à un utilisateur du staff, avec un
contenu structuré (icône, titre, message, lien).

**Technologies utilisées :** Laravel Database Notifications · `fetch()` + `setInterval` ·
Alpine.js

### Comment l'icône cloche reste à jour

Il n'y a pas de connexion permanente entre le navigateur et le serveur (pas de WebSocket). À la
place, un petit composant Alpine.js interroge le serveur **toutes les 15 secondes** via
`fetch()`, récupère les notifications non lues, et met à jour le badge et la liste déroulante —
sans recharger la page. Un survol de la cloche déclenche aussi un rafraîchissement immédiat.

**Utilisé sur KhalilShop — Sondage (polling) :**
- Le navigateur redemande l'état toutes les 15 s
- Aucune infrastructure supplémentaire à héberger ou maintenir
- Simple à déboguer — une requête HTTP classique
- Délai maximum de 15 s avant qu'une notification n'apparaisse

**Alternative plus lourde — WebSocket (Pusher, Soketi…) :**
- Connexion permanente, le serveur pousse l'info instantanément
- Nécessite un serveur/service dédié en plus de Laravel
- Pertinent pour du chat en direct ou du multi-utilisateur intense
- Complexité disproportionnée pour un back-office à usage interne

Pour un back-office utilisé par une poignée de membres du staff, le sondage toutes les 15
secondes offre l'essentiel du bénéfice d'un « temps réel » perçu, sans le coût opérationnel
d'une infrastructure WebSocket séparée.
