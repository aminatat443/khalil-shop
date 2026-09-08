# KhalilShop

Boutique e-commerce Mode & Lifestyle (vêtements, chaussures, accessoires, décoration maison) pour le marché Sénégal / Afrique de l'Ouest.

Stack : Laravel + Blade + Tailwind CSS + Alpine.js, PostgreSQL, Cloudinary (à intégrer).

Voir [docs/CAHIER-DES-CHARGES.md](docs/CAHIER-DES-CHARGES.md) et [docs/SPEC.md](docs/SPEC.md) pour le cahier des charges complet et l'état d'avancement détaillé.

## Installation

```bash
composer install
npm install

cp .env.example .env
php artisan key:generate

php artisan migrate --seed
php artisan storage:link

npm run build   # ou npm run dev en développement
php artisan serve
```

## Comptes de test

| Rôle | Email | Mot de passe |
|---|---|---|
| Super Administrateur (Khalil) | `khalil@khalilshop.sn` | `changeme-avant-mise-en-prod` |

⚠️ Email et mot de passe **fictifs**, créés par `database/seeders/DatabaseSeeder.php` en attendant les vraies coordonnées de Khalil. **À remplacer avant toute mise en production** (voir docs/SPEC.md §2.5).

Un compte Administrateur ou Gestionnaire peut ensuite être créé depuis le back-office (`/admin/staff`) avec ce compte Super Administrateur. Les comptes Client se créent normalement via l'inscription sur la boutique (ou connexion Google, si les identifiants `GOOGLE_CLIENT_ID`/`GOOGLE_CLIENT_SECRET` sont renseignés dans `.env`).

Un compte Client créé par email doit **vérifier son adresse** (lien envoyé par email via Brevo) avant de pouvoir se connecter — un bouton permet de renvoyer l'email si besoin. Les comptes créés depuis le back-office et les connexions Google sont considérés vérifiés d'office.


## Back-office

Accessible à `/admin` pour les rôles Gestionnaire, Administrateur et Super Administrateur.

## Assistant IA

Un assistant de FAQ boutique (livraison, retours, paiement, compte) est disponible sur toutes les pages de la boutique. Il utilise l'API Google Gemini (gratuite, sans carte bancaire) — créez une clé sur [aistudio.google.com/app/apikey](https://aistudio.google.com/app/apikey) et renseignez `GEMINI_API_KEY` dans `.env` pour l'activer ; sans clé, l'assistant affiche un message d'indisponibilité au lieu de planter.

## Emails (Brevo)

L'envoi d'emails (confirmation de commande avec facture PDF en pièce jointe) passe par le relais SMTP de [Brevo](https://www.brevo.com) (gratuit jusqu'à 300 emails/jour). Pour l'activer :

1. Crée un compte sur [app.brevo.com](https://app.brevo.com), puis va dans **Paramètres → SMTP & API → onglet SMTP**.
2. Renseigne dans `.env` : `MAIL_USERNAME` (ton identifiant SMTP Brevo, visible sur cette page) et `MAIL_PASSWORD` (ta clé SMTP — pas ton mot de passe de compte).
3. Vérifie ton adresse d'expédition (`MAIL_FROM_ADDRESS`) dans Brevo (**Expéditeurs & IP**) pour éviter que les emails partent en spam.

Sans identifiants, l'envoi échoue silencieusement (erreur journalisée) sans jamais bloquer la commande — le client garde toujours sa page de confirmation et peut télécharger sa facture manuellement.
