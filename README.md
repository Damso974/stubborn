# Stubborn

Projet e-commerce réalisé avec **Symfony** dans le cadre d’un exercice de développement web.

Stubborn est une boutique en ligne spécialisée dans la vente de sweat-shirts.  
L’application permet à un utilisateur de créer un compte, confirmer son adresse email, consulter les produits, filtrer la boutique, gérer un panier et effectuer un paiement de test avec Stripe.

Une interface d’administration permet également de gérer les produits et leurs stocks.

## Fonctionnalités

### Utilisateur

- Inscription
- Vérification de l’adresse email
- Connexion / déconnexion
- Consultation de la page d’accueil
- Affichage des produits mis en avant
- Accès à la boutique
- Filtrage des produits par prix
- Consultation d’une fiche produit
- Sélection d’une taille
- Ajout au panier
- Contrôle du stock disponible
- Suppression d’un produit du panier
- Calcul du total
- Paiement avec Stripe en environnement de test
- Mise à jour automatique du stock après paiement

### Administrateur

- Accès sécurisé au back-office avec `ROLE_ADMIN`
- Liste des produits
- Ajout d’un produit
- Modification d’un produit
- Suppression d’un produit
- Modification :
  - du nom
  - du prix
  - de l’image
  - du statut de mise en avant
  - du stock XS
  - du stock S
  - du stock M
  - du stock L
  - du stock XL

## Technologies utilisées

- PHP 8.4
- Symfony 8.1
- MySQL 8.4
- Doctrine ORM
- Twig
- Bootstrap 5
- Symfony Security
- Symfony Mailer
- Symfony Messenger
- Mailpit
- Stripe Checkout
- PHPUnit
- Git
- GitHub

## Installation

Cloner le projet :

```bash
git clone https://github.com/Damso974/stubborn.git
```

Se déplacer dans le dossier :

```bash
cd stubborn
```

Installer les dépendances :

```bash
composer install
```

## Configuration

Créer un fichier :

```text
.env.local
```

Les informations propres à la machine doivent être placées dans ce fichier.

Exemple :
```dotenv
