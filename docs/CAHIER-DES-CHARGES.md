# CAHIER DES CHARGES — KHALILSHOP

## Plateforme e-commerce Mode, Accessoires & Décoration Maison

| Champ | Valeur |
|---|---|
| **Projet** | KhalilShop |
| **Type** | Site e-commerce B2C |
| **Positionnement** | Mode & Lifestyle Premium |
| **Marché cible** | Sénégal et Afrique de l'Ouest |
| **Frontend** | Laravel Blade + Tailwind CSS + Alpine.js |
| **Backend** | Laravel / PHP |
| **Base de données** | PostgreSQL |
| **Images** | Cloudinary |
| **Version** | 1.0 |

> Conversion fidèle en Markdown du document source `CAHIER DES CHARGES — KHALILSHOP.pdf` (37 pages, 67 sections). Le contenu n'a pas été modifié par rapport à l'original — voir [SPEC.md](./SPEC.md) pour les compléments et décisions prises afin de combler les manques identifiés.

---

## 1. Présentation du projet

KhalilShop est une plateforme e-commerce moderne spécialisée dans la vente de produits de **mode, chaussures, accessoires et décoration maison**.

La boutique doit proposer une expérience digitale premium permettant aux clients de découvrir facilement les collections, consulter les produits, choisir leurs variantes, ajouter des articles au panier et effectuer leurs commandes en ligne.

KhalilShop devra se positionner comme une marque : **moderne, élégante, accessible et tendance.**

Le site devra être conçu avec une approche **mobile-first**, car une grande partie des utilisateurs accéderont à la boutique depuis leur smartphone.

L'objectif n'est pas de créer un simple catalogue de produits, mais une véritable **expérience shopping digitale**.

---

## 2. Activités de KhalilShop

Le catalogue sera organisé autour de cinq grands univers.

### 2.1 Femme
Robes, Ensembles, Tops, Chemisiers, T-shirts, Pantalons, Jeans, Jupes, Vestes, Manteaux, Pulls, Tenues tendance.

### 2.2 Homme
Chemises, T-shirts, Polos, Pantalons, Jeans, Shorts, Ensembles, Vestes, Pulls, Sweats, Tenues casual, Tenues élégantes.

### 2.3 Chaussures
Sneakers, Baskets, Sandales, Escarpins, Mocassins, Bottes, Chaussures homme, Chaussures femme, Chaussures casual, Chaussures élégantes.

### 2.4 Accessoires
Sacs, Sacs à main, Sacs à dos, Portefeuilles, Ceintures, Lunettes, Bijoux, Montres, Casquettes, Chapeaux, Foulards.

### 2.5 Maison & Décoration
Décoration murale, Vases, Miroirs, Bougies, Luminaires, Coussins, Tapis, Objets décoratifs, Rangement, Accessoires de table, Décoration chambre, Décoration salon.

---

## 3. Objectifs

### Objectif principal
Créer une plateforme permettant à KhalilShop de vendre ses produits en ligne de manière professionnelle.

### Objectifs secondaires
Le site devra permettre de :
- présenter les collections ;
- augmenter les ventes ;
- améliorer la visibilité de la marque ;
- faciliter les commandes ;
- gérer les stocks ;
- gérer les promotions ;
- fidéliser les clients ;
- automatiser une partie du processus de vente ;
- centraliser les commandes.

---

## 4. Positionnement UX/UI

KhalilShop devra avoir une identité visuelle **fashion + lifestyle + premium**.

L'expérience devra s'inspirer des grands standards des sites de mode modernes :
- grandes photos ;
- espaces blancs généreux ;
- typographie élégante ;
- navigation minimaliste ;
- cartes produits propres ;
- animations discrètes ;
- filtres intuitifs ;
- recherche rapide ;
- checkout simple.

Le site devra éviter l'apparence d'un ancien site e-commerce surchargé.

---

## 5. Direction artistique

### 5.1 Style visuel
Le design devra être :
- minimaliste ;
- élégant ;
- moderne ;
- féminin et masculin à la fois ;
- premium ;
- chaleureux pour la partie décoration ;
- très visuel.

Les photos produits devront occuper une place importante.

---

## 6. Palette de couleurs

Une palette moderne pourra être construite autour de :

| Couleur | Code hex | Utilisation |
|---|---|---|
| Noir profond | `#111111` | navigation, titres, boutons, footer |
| Blanc cassé | `#FAFAF8` | arrière-plans, sections, cartes |
| Beige / Nude | `#E8DED2` | sections lifestyle, décoration, éléments secondaires |
| Accent | `#C9A27E` | détails premium, badges, éléments décoratifs |

> La palette pourra être ajustée lors de la phase UI Design.

---

## 7. Typographie

La typographie devra créer une distinction entre :

- **Titres** : police élégante pouvant être une serif moderne.
- **Interface** : une police sans-serif moderne telle que **Montserrat**, **Inter** ou équivalent.

L'objectif est d'obtenir une combinaison : **Élégance + lisibilité + modernité.**

---

## 8. Architecture du site

Le site devra comporter :

Accueil, Boutique, Femme, Homme, Chaussures, Accessoires, Maison & Décoration, Nouveautés, Promotions, Collections, Produit, Panier, Checkout, Connexion, Inscription, Mon compte, Mes commandes, Favoris, À propos, Contact, FAQ.

---

## 9. Header

Le header sera l'un des éléments les plus importants.

### Desktop
Organisation : `LOGO — Recherche — Compte / Favoris / Panier`

Une deuxième navigation pourra afficher : Femme, Homme, Chaussures, Accessoires, Maison, Nouveautés, Promotions.

---

## 10. Recherche

La barre de recherche devra être très visible.

L'utilisateur pourra rechercher : robe noire, jean homme, basket blanche, sac à main, miroir, vase, etc.

La recherche pourra afficher des suggestions pendant la saisie.

**Exemple** — saisie « robe » → Produits : Robe longue noire, Robe satin beige, Robe portefeuille.

---

## 11. Mega Menu

Le mega menu permettra une navigation rapide.

- **Femme** : Robes, Tops, Pantalons, Jeans, Ensembles, Vestes, Nouveautés.
- **Homme** : T-shirts, Chemises, Jeans, Pantalons, Vestes, Ensembles, Nouveautés.
- **Chaussures** : Sneakers, Sandales, Mocassins, Escarpins, Bottes.
- **Accessoires** : Sacs, Bijoux, Montres, Lunettes, Ceintures.
- **Maison** : Salon, Chambre, Cuisine, Décoration, Luminaires, Rangement.

---

## 12. Page d'accueil

La homepage devra fonctionner comme une vitrine digitale.

### Hero principal
Grande image lifestyle.

- Titre exemple : *« Votre style. Votre intérieur. Votre univers. »*
- Sous-titre : *« Découvrez nos nouvelles collections mode & maison. »*
- Boutons : **Découvrir la collection** / **Voir les nouveautés**

---

## 13. Section catégories

Une section visuelle permettra d'accéder rapidement aux univers : FEMME, HOMME, CHAUSSURES, ACCESSOIRES, MAISON.

Chaque catégorie devra être représentée par une grande image.

---

## 14. Section Nouveautés

- Titre : **Nouveautés**
- Sous-titre : *« Découvrez les dernières pièces ajoutées à notre collection. »*
- Afficher les produits les plus récents.

---

## 15. Section Femme

Grande section éditoriale : **Collection Femme** avec image principale, produits sélectionnés, bouton « Découvrir Femme ».

---

## 16. Section Homme

Même principe : **Collection Homme** avec une présentation visuelle masculine.

---

## 17. Section Chaussures

Présentation des dernières chaussures : sneakers, sandales, mocassins, escarpins.

---

## 18. Section Maison & Décoration

Cette section devra avoir une identité visuelle légèrement différente.

**Objectif** : créer une ambiance **maison / lifestyle / élégance**.

Exemple : *« Transformez votre intérieur. »*

Présenter : vases, miroirs, bougies, coussins, luminaires, objets décoratifs.

---

## 19. Promotions

Une section spécifique permettra de mettre en avant les offres.

Exemple : **SALE — Jusqu'à -50%**

Chaque produit pourra afficher : Ancien prix : 35 000 FCFA / Nouveau prix : 25 000 FCFA / -29%

---

## 20. Catalogue

La page catalogue devra permettre une navigation très fluide.

**Affichage** : grille 2 colonnes mobile, 3 colonnes tablette, 4 colonnes desktop.

---

## 21. Filtres

Les filtres devront dépendre de la catégorie.

- **Filtres généraux** : prix, disponibilité, nouveautés, promotions.
- **Femme / Homme** : taille, couleur, matière, coupe.
- **Chaussures** : pointure, couleur, matière.
- **Maison** : couleur, matière, dimensions, pièce.

---

## 22. Tri

Options : Pertinence, Nouveautés, Prix croissant, Prix décroissant, Meilleures ventes.

---

## 23. Carte produit

Chaque produit devra présenter : photo, badge, nom, prix, ancien prix, réduction, couleur disponible, bouton favori.

Exemple : *Robe longue satinée — 35 000 FCFA*

---

## 24. Effet au survol

**Desktop** — au survol d'un produit : deuxième image, apparition du bouton « Ajouter au panier », animation légère.

---

## 25. Page produit

La page produit devra être très visuelle.

**Galerie** : image principale, miniatures, zoom, plusieurs photos, images lifestyle.

---

## 26. Informations produit

Afficher : nom, référence, prix, ancien prix, réduction, description, disponibilité, matière, entretien, livraison.

---

## 27. Variantes

Les produits vestimentaires devront pouvoir avoir plusieurs variantes.

- **Couleur** : Noir, Blanc, Beige, Rouge (exemples).
- **Taille (vêtements)** : XS, S, M, L, XL, XXL.
- **Taille (chaussures)** : 36 à 45.

---

## 28. Gestion intelligente des variantes

Lorsqu'une taille ou une couleur est indisponible, elle devra être automatiquement désactivée.

Exemple : `S ● M ● L ✕ XL ●` — le client comprendra immédiatement que la taille L n'est plus disponible.

---

## 29. Guide des tailles

Pour les vêtements, une fonctionnalité **Guide des tailles** permettra d'afficher un tableau.

| Taille | Poitrine | Taille | Hanches |
|---|---|---|---|
| S | 88-92 | 68-72 | 92-96 |
| M | 92-96 | 72-76 | 96-100 |
| L | 96-100 | 76-80 | 100-104 |
| XL | 100-106 | 80-86 | 104-110 |

Les valeurs seront configurables depuis l'administration.

---

## 30. Ajouter au panier

Le bouton principal devra être très visible : **AJOUTER AU PANIER**

Après ajout : animation, notification, mise à jour du compteur panier.

---

## 31. Achat rapide

Un bouton **Acheter maintenant** pourra permettre d'envoyer directement vers le checkout.

---

## 32. Favoris

Le client pourra sauvegarder ses produits favoris. Le système devra permettre : ajouter, supprimer, consulter, ajouter au panier.

Pour les utilisateurs non connectés, les favoris pourront être conservés temporairement côté navigateur.

---

## 33. Panier

Le panier devra être accessible depuis le header. Il pourra s'ouvrir sous forme de drawer.

**Afficher** : produit, image, variante, taille, couleur, quantité, prix, suppression.

**Résumé** : Sous-total, Livraison, Réduction, TOTAL.

---

## 34. Checkout

Le checkout devra être très simple, en 5 étapes :

1. **Informations personnelles** : nom, prénom, téléphone, email.
2. **Adresse** : région, ville, quartier, adresse, instructions.
3. **Livraison.**
4. **Paiement.**
5. **Confirmation.**

---

## 35. Livraison

Le système devra gérer la livraison partout au Sénégal, avec possibilité de définir : zone, délai, coût.

Exemples :
- Dakar — Livraison : 2 000 FCFA
- Thiès — Livraison : 3 000 FCFA
- Autres régions — Tarif configurable

Les tarifs seront administrables.

---

## 36. Paiement

Le système devra être conçu pour intégrer les moyens de paiement adaptés au marché sénégalais.

Exemples possibles : Wave, Orange Money, paiement par carte, paiement à la livraison si disponible, autres solutions de paiement.

> Les intégrations exactes seront définies avant le développement.

---

## 37. Compte client

Le client pourra : créer un compte, se connecter, modifier son profil, modifier son mot de passe, gérer ses adresses, consulter ses commandes, consulter ses favoris, suivre ses commandes.

---

## 38. Suivi de commande

Timeline : Commande reçue → Confirmée → En préparation → Expédiée → En livraison → Livrée.

---

## 39. Avis clients

Une évolution importante pourra permettre aux clients de noter les produits.

Système : ★★★★★ et commentaire. L'administrateur pourra modérer les avis.

---

## 40. Back-office

L'administration Laravel devra être conçue avec la même exigence UX que le frontend.

**Dashboard** : chiffre d'affaires, commandes, clients, produits, stock, produits populaires, ventes récentes.

---

## 41. Gestion des produits

L'administrateur pourra : ajouter, modifier, supprimer, désactiver, mettre en promotion, mettre en avant, gérer le stock, gérer les variantes, gérer les images.

---

## 42. Création d'un produit

**Formulaire** : Nom, Catégorie, Sous-catégorie, Description, Prix, Ancien prix, SKU, Stock, Images, Couleurs, Tailles, Matière, Dimensions, Poids, Nouveauté, Promotion, Produit vedette.

Les champs devront être dynamiques selon la catégorie.

- **Pour une robe** : Taille, Couleur, Matière, Coupe.
- **Pour un vase** : Dimensions, Matière, Couleur.

---

## 43. Gestion des catégories

L'administrateur pourra créer une arborescence, ex. :

```
Femme
├── Robes
├── Tops
├── Pantalons
└── Ensembles

Homme
├── T-shirts
├── Chemises
├── Jeans
└── Pantalons

Chaussures
├── Sneakers
├── Sandales
└── Mocassins

Accessoires
├── Bijoux
└── Lunettes

Maison
├── Décoration
├── Salon
├── Chambre
└── Luminaires
```

---

## 44. Gestion des commandes

L'administrateur pourra : consulter les commandes, rechercher une commande, filtrer par statut, voir les détails, modifier le statut, confirmer, annuler, marquer comme expédiée, marquer comme livrée.

---

## 45. Gestion du stock

Le stock devra être géré au niveau des variantes.

Exemple — Robe noire : `S → 5`, `M → 10`, `L → 0`, `XL → 3`.

Le site affichera pour `L` : **Rupture de stock**.

---

## 46. Gestion des promotions

L'administration permettra de créer : réduction en pourcentage, réduction fixe, promotion par catégorie, promotion sur produit, code promo, date de début, date de fin.

---

## 47. Codes promotionnels

Exemple :
- Code : `KHALIL20`
- Réduction : 20%
- Minimum : 50 000 FCFA
- Expiration : 31/12/2026

---

## 48. Gestion de la homepage

L'administrateur devra pouvoir modifier : hero, bannières, produits vedettes, nouveautés, promotions, collections, sections maison.

L'objectif est de permettre à KhalilShop de modifier son contenu commercial **sans intervention du développeur**.

---

## 49. Gestion des images

Les images seront stockées sur Cloudinary.

**Fonctionnalités** : upload, optimisation, compression, transformation, recadrage, plusieurs tailles, suppression.

Le système devra conserver : URL, public ID, ordre, texte alternatif.

---

## 50. Base de données Laravel

**Tables principales** : `users`, `categories`, `subcategories`, `products`, `product_variants`, `product_images`, `colors`, `sizes`, `orders`, `order_items`, `addresses`, `favorites`, `reviews`, `coupons`, `coupon_usages`, `promotions`, `banners`, `payments`, `deliveries`.

---

## 51. Relations Eloquent

```
Category
└── hasMany Products

Product
├── belongsTo Category
├── hasMany Variants
├── hasMany Images
├── hasMany Reviews
└── belongsToMany Favorites

ProductVariant
├── belongsTo Product
├── belongsTo Size
└── belongsTo Color

User
├── hasMany Orders
├── hasMany Addresses
├── hasMany Favorites
└── hasMany Reviews

Order
├── belongsTo User
└── hasMany OrderItems
```

---

## 52. Architecture Laravel

Le projet devra respecter une architecture MVC propre :

```
app/
├── Models
├── Http
│   ├── Controllers
│   ├── Requests
│   └── Middleware
├── Services
├── Policies
└── Notifications
```

Les traitements complexes devront être placés dans des **Services** plutôt que directement dans les contrôleurs.

---

## 53. Frontend Tailwind CSS

Tailwind CSS sera utilisé pour construire l'ensemble de l'interface.

**Composants réutilisables** : Navbar, MegaMenu, Hero, ProductCard, ProductGrid, ProductGallery, FilterSidebar, SearchBar, CartDrawer, Modal, Toast, Badge, Button, Input, Select, Pagination, Breadcrumb, Footer.

---

## 54. Alpine.js

Alpine.js pourra gérer les interactions frontend légères : menu mobile, mega menu, filtres, modal, panier drawer, galerie, sélection variante, notifications, accordéons, recherche instantanée.

Cela permettra d'éviter d'ajouter inutilement un framework frontend lourd.

---

## 55. Responsive Design

Le site devra être parfaitement responsive.

| Écran | Produits / ligne |
|---|---|
| Mobile | 2 |
| Tablette | 3 |
| Desktop | 4 |
| Grand écran | 5 (possibilité) |

---

## 56. Performance

**Objectif** : expérience extrêmement rapide.

**Techniques** : lazy loading, Cloudinary, WebP/AVIF, images responsives, pagination, cache Laravel, eager loading Eloquent, optimisation des requêtes, Vite, minification, CDN.

---

## 57. SEO

Chaque produit devra disposer de : slug, title, meta description, image SEO, URL canonique, Open Graph.

Exemple : `/khalilshop/femme/robes/robe-satin-beige`

---

## 58. Sécurité

Laravel devra assurer : CSRF, validation serveur, hashage des mots de passe, protection XSS, protection SQL Injection, autorisation par Policies, validation des uploads, protection des routes admin, rate limiting, HTTPS.

---

## 59. Gestion des rôles

- **Client** — accès : catalogue, panier, compte, commandes, favoris.
- **Administrateur** — accès complet.
- **Gestionnaire** — accès possible à : produits, stocks, commandes.

---

## 60. Notifications

**Client** : commande reçue, commande confirmée, commande expédiée, commande livrée, paiement confirmé.

**Administrateur** : nouvelle commande, paiement, stock faible.

---

## 61. Emails

Emails automatiques : Bienvenue chez KhalilShop, Confirmation de commande, Paiement confirmé, Commande expédiée, Commande livrée, Réinitialisation mot de passe.

---

## 62. Analytics

Le site pourra intégrer : Google Analytics, Meta Pixel, Google Search Console.

Événements : `view_item`, `search`, `add_to_cart`, `begin_checkout`, `purchase`, `sign_up`.

---

## 63. Architecture technique finale

```
KHALILSHOP
├── FRONTEND
│   ├── Tailwind CSS
│   ├── Blade
│   ├── Alpine.js
│   ├── JavaScript
│   └── Vite
└── BACKEND
    ├── Laravel
    ├── PHP
    ├── Eloquent
    ├── Services
    └── Policies
        │
        ▼
   PostgreSQL
        │
   ┌────┼────────────┐
   ▼    ▼            ▼
Cloudinary  Paiement    Email
Images      Providers   Service
```

---

## 64. Phases de développement

**Phase 1 — Analyse** : validation du catalogue, catégories, variantes, règles commerciales, livraison, paiement.

**Phase 2 — UX/UI** : wireframes, architecture, design system, maquettes desktop, maquettes mobile.

**Phase 3 — Laravel** : installation, migrations, modèles, relations Eloquent, authentification, rôles, sécurité.

**Phase 4 — Frontend** : header, mega menu, homepage, catalogue, filtres, produit, panier, checkout.

**Phase 5 — Administration** : dashboard, produits, catégories, stock, commandes, clients, promotions, homepage.

**Phase 6 — Intégrations** : Cloudinary, paiement, emails, analytics.

**Phase 7 — Tests** : fonctionnels, responsive, sécurité, performance, compatibilité navigateurs.

**Phase 8 — Déploiement** : serveur, domaine, SSL, base de données, Cloudinary, variables d'environnement, sauvegardes.

---

## 65. Critères de réussite

KhalilShop devra être considéré comme réussi lorsque :
- le design est moderne et premium ;
- la navigation est intuitive ;
- le catalogue est facilement exploitable ;
- les variantes sont correctement gérées ;
- le panier fonctionne parfaitement ;
- le checkout est simple ;
- la gestion des stocks est fiable ;
- les commandes sont correctement suivies ;
- l'administration est facile à utiliser ;
- le site est parfaitement responsive ;
- les pages sont rapides ;
- le référencement de base est correctement configuré ;
- le site est sécurisé.

---

## 66. Vision finale

KhalilShop ne devra pas ressembler à une simple boutique en ligne générique. L'objectif est de créer une véritable **marque digitale Mode & Lifestyle**.

L'expérience devra donner l'impression d'entrer dans une boutique moderne où les univers sont clairement séparés : FEMME, HOMME, CHAUSSURES, ACCESSOIRES, MAISON & DÉCORATION — avec une identité visuelle cohérente entre tous les univers.

Le site devra combiner : **Mode + Lifestyle + Décoration + E-commerce + Expérience Premium**, tout en restant rapide, simple et adapté aux habitudes d'achat des clients au Sénégal.

---

## 67. Stack technologique définitive

**Frontend** : Laravel Blade, Tailwind CSS, Alpine.js, JavaScript, Vite.

**Backend** : Laravel, PHP, Eloquent ORM, Laravel Authentication, Policies / Gates, Form Requests, Services.

**Database** : PostgreSQL.

**Images** : Cloudinary.

**Versioning** : Git / GitHub.

**Infrastructure** : Serveur Linux + HTTPS.

> KhalilShop devra donc être développé comme une plateforme e-commerce moderne, évolutive et réellement orientée conversion, avec une architecture Laravel propre et une interface Tailwind CSS premium.
