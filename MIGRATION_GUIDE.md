# Guide de Migration - Base de Données KANZEYCO v2

Ce guide vous explique comment migrer vers la nouvelle structure de base de données adaptée à la version actuelle du site.

## 📋 Fichiers Créés

1. **`kanzey-site-v2.sql`** - Script SQL complet avec toutes les tables adaptées
2. **`includes/database.php`** - Fichier de connexion à la base de données
3. **`includes/articles-db.php`** - Fonctions pour gérer les articles depuis la BDD
4. **`admin/includes/config.php`** - Mise à jour pour utiliser la base de données

## 🔄 Changements Principaux

### 1. Table `blog` → `articles`
- ✅ Ajout du champ `slug` (unique) pour les URLs
- ✅ Ajout de `category`, `excerpt`, `read_time`, `author`
- ✅ Ajout de `tags` (JSON) et `content` (JSON structuré)
- ✅ Ajout de `status` (draft/published/archived)
- ✅ Ajout du compteur `views`
- ✅ Renommage de `titre` → `title`, `descriptions` → `excerpt`

### 2. Table `comment` → `comments`
- ✅ Ajout du champ `email`
- ✅ Ajout du champ `status` (pending/approved/rejected)
- ✅ Renommage de `messages` → `message`

### 3. Nouvelle Table `contacts`
- ✅ Pour gérer les formulaires de contact du site

### 4. Table `rdv` (Améliorée)
- ✅ Ajout du champ `status` (pending/confirmed/cancelled/completed)

### 5. Table `realisation` → `realisations`
- ✅ Ajout du champ `slug` (unique)
- ✅ Ajout de `client_name` et `category`
- ✅ Ajout du champ `status`
- ✅ Renommage de `descriptions` → `description`

### 6. Table `user` → `users`
- ✅ Ajout du champ `username` (unique)
- ✅ Ajout du champ `role` (admin/editor/author)
- ✅ Ajout du champ `last_login`
- ✅ Amélioration de la structure pour l'authentification

### 7. Nouvelles Tables
- ✅ `article_views` - Statistiques détaillées des vues d'articles
- ✅ `newsletter` - Gestion des abonnés à la newsletter

## 🚀 Installation

### Étape 1 : Créer la Base de Données

```sql
CREATE DATABASE IF NOT EXISTS `kanzey-site` CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
```

Ou utilisez `kanzeyco_db` si vous préférez.

### Étape 2 : Importer le Script SQL

1. Ouvrez phpMyAdmin (http://localhost/phpmyadmin)
2. Sélectionnez la base de données
3. Cliquez sur l'onglet "Importer"
4. Sélectionnez le fichier `kanzey-site-v2.sql`
5. Cliquez sur "Exécuter"

Ou via la ligne de commande :

```bash
mysql -u root -p kanzey-site < kanzey-site-v2.sql
```

### Étape 3 : Configurer la Connexion

Modifiez le fichier `includes/database.php` si nécessaire :

```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'kanzey-site'); // ou 'kanzeyco_db'
define('DB_USER', 'root');
define('DB_PASS', '');
```

### Étape 4 : Migrer les Données Existantes (Optionnel)

Si vous avez des données dans l'ancienne base de données, vous pouvez les migrer avec un script SQL :

```sql
-- Migrer les articles (blog → articles)
INSERT INTO articles (title, slug, category, excerpt, content, image, author, date_published, status)
SELECT 
    titre as title,
    LOWER(REPLACE(REPLACE(REPLACE(titre, ' ', '-'), '''', ''), ',', '')) as slug,
    'Blog' as category,
    LEFT(descriptions, 200) as excerpt,
    JSON_ARRAY(JSON_OBJECT('type', 'paragraph', 'text', descriptions)) as content,
    photo as image,
    'Equipe Kanzey.co' as author,
    date as date_published,
    'published' as status
FROM blog;
```

## 🔐 Authentification Admin

### Compte Admin par Défaut

- **Username :** `admin`
- **Email :** `contact@kanzey.co`
- **Password :** `admin123`

⚠️ **IMPORTANT** : Changez le mot de passe en production !

### Changer le Mot de Passe

Pour générer un nouveau hash de mot de passe :

```php
<?php
$password = 'votre_nouveau_mot_de_passe';
$hash = password_hash($password, PASSWORD_BCRYPT);
echo $hash;
?>
```

Puis mettez à jour dans la base de données :

```sql
UPDATE users 
SET password = '$2y$10$VOTRE_NOUVEAU_HASH' 
WHERE username = 'admin';
```

## 📝 Utilisation

### Utiliser les Articles depuis la Base de Données

Les fichiers suivants utilisent automatiquement la base de données si disponible :

- `articles.php` - Liste des articles
- `article-detail.php` - Détail d'un article
- `admin/articles.php` - Gestion des articles dans le dashboard

### Fallback Automatique

Si la base de données n'est pas disponible, le système utilise automatiquement le fichier `includes/articles-data.php` comme fallback. Cela permet une transition en douceur.

## 🛠️ Fonctions Disponibles

### Dans `includes/articles-db.php` :

- `getArticles($category = null, $limit = null)` - Récupère les articles
- `getArticleBySlug($slug)` - Récupère un article par son slug
- `incrementArticleViews($articleId)` - Incrémente les vues
- `getArticleComments($articleId)` - Récupère les commentaires
- `addComment($articleId, $nom, $email, $message)` - Ajoute un commentaire

### Dans `admin/includes/config.php` :

- `getDashboardStats()` - Récupère les statistiques du dashboard
- `adminLogin($username, $password)` - Authentification admin
- `isAdminLoggedIn()` - Vérifie si l'admin est connecté

## ✅ Vérification

Après l'installation, vérifiez que tout fonctionne :

1. ✅ Accédez au site : http://localhost/KANZEYCO/
2. ✅ Vérifiez que les articles s'affichent
3. ✅ Connectez-vous au dashboard admin : http://localhost/KANZEYCO/admin/
4. ✅ Vérifiez les statistiques dans le dashboard

## 🔧 Support

En cas de problème :

1. Vérifiez les logs PHP (erreurs de connexion BDD)
2. Vérifiez que la base de données existe
3. Vérifiez les identifiants dans `includes/database.php`
4. Vérifiez que les tables ont été créées correctement

## 📚 Structure des Tables

Pour voir la structure complète des tables, consultez le fichier `kanzey-site-v2.sql`.

