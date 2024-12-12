# UP ACF Block Generator

Générateur de blocs ACF pour WordPress. Cet outil permet de créer rapidement des blocs Gutenberg personnalisés basés sur ACF.

## Fonctionnalités

- Génération automatique de la structure de dossiers pour les blocs ACF
- Création des fichiers nécessaires (block.json, fields.json, view.php, etc.)
- Support des assets (JS et SCSS)
- Fichier functions.php avec exemple de hooks
- Possibilité de générer les blocs dans le thème ou dans mu-plugins

## Structure des fichiers générés

    nom-du-bloc/
    ├── acf/
    │   └── fields.json
    ├── assets/
    │   ├── js/
    │   │   └── function.js
    │   └── scss/
    │       ├── _mixins.scss
    │       └── style.scss
    ├── block.json
    ├── functions.php
    └── view.php

## Configuration des blocs

Les blocs générés supportent par défaut :
- Couleurs (background, texte, liens)
- Espacement (margin, padding)
- Typographie (taille de police, hauteur de ligne)
- JSX
- Et plus encore selon la configuration choisie

## Utilisation

1. Créez un nouveau bloc via l'interface d'administration WordPress
2. Configurez les options du bloc (nom, titre, description, etc.)
3. Le bloc est automatiquement généré avec tous les fichiers nécessaires
4. Personnalisez les champs ACF dans le fichier fields.json
5. Développez la vue du bloc dans view.php
6. Ajoutez des hooks personnalisés dans functions.php si nécessaire

## Développement

Pour contribuer au développement :
1. Clonez le dépôt
2. Installez les dépendances
3. Suivez les standards de code WordPress
4. Soumettez vos pull requests

## Prérequis

- WordPress 5.8+
- ACF PRO 5.8+
- PHP 7.4+

## Licence

Ce plugin est sous licence GPL v2 ou ultérieure.
