# MagixFaqMulti

[![Release](https://img.shields.io/github/release/magix-cms/MagixFaqMulti.svg)](https://github.com/magix-cms/MagixFaqMulti/releases/latest)
[![License](https://img.shields.io/github/license/magix-cms/MagixFaqMulti.svg)](LICENSE)
[![PHP Version](https://img.shields.io/badge/php-%3E%3D%208.2-blue.svg)](https://php.net/)
[![Magix CMS](https://img.shields.io/badge/Magix%20CMS-4.x-success.svg)](https://www.magix-cms.com/)

**MagixFaqMulti** permet de déployer des foires aux questions (FAQ) ciblées et multilingues sur vos fiches produits, pages de contenu et catégories. C'est l'outil idéal pour améliorer votre SEO et la réassurance client.

## 🌟 Fonctionnalités principales

* **Tab-In-Tab Integration** : Ajoute un onglet "FAQ" dans les formulaires d'édition des modules Core de Magix CMS.
* **Rendu Accordéon Bootstrap 5** : Le widget frontend utilise le composant `Accordion` natif de Bootstrap pour une expérience mobile parfaite.
* **Multilingue Intelligent** : Gestion des questions/réponses par langue avec sélecteur d'onglets isolé.
* **Gestion AJAX Complète** : Ajout, modification et tri des questions sans rechargement de la page d'administration.
* **SEO Ready** : Structure de données optimisée pour la présentation de réponses claires aux utilisateurs.
* **Tri Intuitif** : Gérez l'ordre des questions via une interface de tri Drag & Drop (`Sortable.js`).

## ⚙️ Installation

1. Téléchargez l'archive du plugin.
2. Déposez le dossier `MagixFaqMulti` dans le répertoire `plugins/`.
3. Activez le plugin via **Extensions > Plugins** dans votre administration Magix CMS.
4. Les tables `mc_faqmulti` et `mc_faqmulti_content` seront générées automatiquement.

## 🚀 Utilisation

### Administration
1. Éditez n'importe quel élément (Produit, Page, News...).
2. Cliquez sur l'onglet **"F.A.Q"**.
3. Remplissez vos questions et réponses pour chaque langue activée sur votre boutique.
4. Activez ou désactivez chaque question individuellement via le switch de statut.

### Frontend
La FAQ s'affiche automatiquement sur les zones de contenu (Hooks) assignées dans le `Boot.php`. Elle est automatiquement stylisée selon les standards Bootstrap 5 de votre thème.

## 🛠️ Architecture Technique

* **Core JS** : Repose sur `MagixAjaxManager.js` pour une communication asynchrone robuste avec le contrôleur backend.
* **UI Isolation** : Utilise le namespace `faq_` pour ses éléments HTML et CSS, garantissant une cohabitation parfaite avec `MagixMultiText`.
* **Data Integrity** : Utilise un `QueryBuilder` optimisé pour la récupération des contenus traduits en une seule jointure performante.

## 📄 Licence

Ce projet est sous licence **GPLv3**. Voir le fichier [LICENSE](LICENSE) pour plus de détails.

Copyright (C) 2008 - 2026 Gerits Aurelien (Magix CMS)