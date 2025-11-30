# 📋 Functionalities Restantes à Implémenter

## 🔴 Priorité HAUTE - Fonctionnalités Front-end

### 2. **Formulaire de Contact**

- [ ] **Traitement backend du formulaire de contact**
  - **Fichier** : `index.php` ligne 973
  - **Formulaire** : `contact-form-card`
  - **Champs** : nom, email, entreprise, téléphone, message
  - **À faire** :
    - Créer un fichier `includes/process-contact.php`
    - Sauvegarder dans la table `contacts` de la base de données
    - Envoyer un email de notification
    - Afficher un message de confirmation

### 3. **Formulaire Newsletter**

- [ ] **Traitement backend du formulaire newsletter**
  - **Fichier** : `index.php` ligne 809
  - **Formulaire** : `cta-newsletter-form`
  - **Champ** : email
  - **À faire** :
    - Créer un fichier `includes/process-newsletter.php`
    - Sauvegarder dans la table `newsletter` de la base de données
    - Vérifier les doublons
    - Afficher un message de confirmation

---

## 🟡 Priorité MOYENNE - Dashboard Admin (CRUD)

### 5. **Gestion des Utilisateurs**

- [ ] **`admin/users.php`** utilise des données de démonstration
  - **À faire** : Connecter à la base de données (table `users`)
  - **Fonctionnalités manquantes** :
    - [ ] **Afficher les vrais utilisateurs** depuis la BDD
    - [ ] **Créer un utilisateur** : Formulaire fonctionnel
    - [ ] **Modifier un utilisateur** : Édition des rôles, statut
    - [ ] **Supprimer un utilisateur** : Fonction de suppression
    - [ ] **Gérer les rôles** : Admin, Éditeur, Auteur
    - [ ] **Réinitialiser les mots de passe**

### 6. **Gestion des Contacts**

- [ ] **Page admin pour les contacts** (table `contacts`)
  - **Fichier à créer** : `admin/contacts.php`
  - **Fonctionnalités** :
    - [ ] Liste des messages de contact
    - [ ] Marquer comme lu/non lu
    - [ ] Répondre aux messages
    - [ ] Archiver/supprimer
    - [ ] Recherche et filtres

### 7. **Gestion des Rendez-vous**

- [ ] **Page admin pour les rendez-vous** (table `rdv`)
  - **Fichier à créer** : `admin/rdv.php` ou `admin/rendez-vous.php`
  - **Fonctionnalités** :
    - [ ] Liste des rendez-vous
    - [ ] Confirmer/Annuler un rendez-vous
    - [ ] Voir le calendrier
    - [ ] Modifier la date/heure
    - [ ] Notifications

### 8. **Gestion des Réalisations (Cas Clients)**

- [ ] **Page admin pour les réalisations** (table `realisations`)
  - **Fichier à créer** : `admin/realisations.php`
  - **Fonctionnalités** :
    - [ ] Liste des réalisations
    - [ ] Créer une réalisation
    - [ ] Modifier une réalisation
    - [ ] Supprimer une réalisation
    - [ ] Upload d'images
    - [ ] Gérer le statut (publié/brouillon)

### 9. **Gestion des Commentaires**

- [ ] **Page admin pour les commentaires** (table `comments`)
  - **Fichier à créer** : `admin/comments.php`
  - **Fonctionnalités** :
    - [ ] Liste des commentaires
    - [ ] Approuver/Rejeter un commentaire
    - [ ] Modérer les commentaires
    - [ ] Supprimer un commentaire
    - [ ] Filtrer par article

### 10. **Gestion de la Newsletter**

- [ ] **Page admin pour la newsletter** (table `newsletter`)
  - **Fichier à créer** : `admin/newsletter.php`
  - **Fonctionnalités** :
    - [ ] Liste des abonnés
    - [ ] Exporter les emails
    - [ ] Désabonner un utilisateur
    - [ ] Statistiques d'abonnés

---

## 🟢 Priorité BASSE - Améliorations

### 11. **Statistiques Réelles**

- [ ] **`admin/statistics.php`** : Améliorer avec de vraies statistiques
  - [ ] Graphiques de vues par jour
  - [ ] Statistiques par catégorie d'articles
  - [ ] Statistiques de commentaires
  - [ ] Statistiques d'engagement
  - [ ] Utiliser les données de `article_views`

### 12. **Filtrage des Articles Front-end**

- [ ] **`articles.php`** : Implémenter le filtrage par catégorie
  - **Fichier** : `articles.php` ligne 38-46
  - **Boutons de filtre** : Actuellement non fonctionnels
  - **À faire** : JavaScript pour filtrer les articles ou reload avec paramètre GET

### 13. **Système de Commentaires Front-end**

- [ ] **`article-detail.php`** : Ajouter un formulaire de commentaires
  - **À faire** :
    - Formulaire de commentaire sur la page détail article
    - Utiliser `addComment()` de `articles-db.php`
    - Afficher les commentaires approuvés avec `getArticleComments()`

### 14. **Paramètres du Site**

- [ ] **`admin/settings.php`** : Sauvegarder les paramètres dans la BDD
  - **À faire** :
    - Créer une table `settings` dans la base de données
    - Sauvegarder les paramètres du site
    - Charger les paramètres depuis la BDD

### 15. **Upload d'Images**

- [ ] **Système d'upload d'images** pour :
  - Articles
  - Réalisations
  - Avatars utilisateurs
  - **À créer** : Dossier `uploads/` et fonctions d'upload

### 16. **Email Notifications**

- [ ] **Notifications par email** pour :
  - Nouveau message de contact
  - Nouveau commentaire à modérer
  - Nouveau rendez-vous
  - **À créer** : Fichier `includes/email.php` avec fonction d'envoi

---

## 📊 Résumé par Priorité

### 🔴 **HAUTE (3 fonctionnalités)**

1. Migration articles vers BDD (front-end)
2. Traitement formulaire contact
3. Traitement formulaire newsletter

### 🟡 **MOYENNE (7 fonctionnalités)**

4.CRUD Articles (admin)
5.CRUD Utilisateurs (admin)
6.Gestion Contacts (admin)
7.Gestion Rendez-vous (admin)
8.Gestion Réalisations (admin)
9.Gestion Commentaires (admin)
10.Gestion Newsletter (admin)

### 🟢 **BASSE (6 améliorations)**

11.Statistiques réelles
12.Filtrage articles
13.Commentaires front-end
14.Paramètres BDD
15.Upload images
16.Notifications email

---

## 🛠️ Fichiers à Créer/Modifier

### À Créer

- `includes/process-contact.php`
- `includes/process-newsletter.php`
- `admin/contacts.php`
- `admin/rdv.php`
- `admin/realisations.php`
- `admin/comments.php`
- `admin/newsletter.php`
- `includes/email.php`
- Fonctions d'upload d'images

### À Modifier

- `articles.php` (ligne 2)
- `article-detail.php` (ligne 2)
- `admin/articles.php` (tout le fichier)
- `admin/users.php` (tout le fichier)
- `admin/statistics.php` (améliorations)
- `index.php` (ajouter traitement formulaires)

---

**Total : 16 fonctionnalités/améliorations à implémenter**\
