-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : 21 Nov. 2025
-- Version du serveur : 10.4.32-MariaDB
-- Version de PHP : 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `kanzey-site` ou `kanzeyco_db`
--

-- --------------------------------------------------------

--
-- Structure de la table `articles` (Blog amélioré)
--

CREATE TABLE `articles` (
  `article_id` int(11) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `title` varchar(255) NOT NULL,
  `category` varchar(100) NOT NULL,
  `excerpt` text NOT NULL,
  `content` longtext NOT NULL COMMENT 'JSON contenant le contenu structuré',
  `image` varchar(500) NOT NULL,
  `author` varchar(100) NOT NULL DEFAULT 'Equipe Kanzey.co',
  `read_time` varchar(20) NOT NULL DEFAULT '5 min',
  `tags` text DEFAULT NULL COMMENT 'JSON array des tags',
  `status` enum('draft','published','archived') NOT NULL DEFAULT 'published',
  `views` int(11) NOT NULL DEFAULT 0,
  `date_published` datetime NOT NULL DEFAULT current_timestamp(),
  `date_created` datetime NOT NULL DEFAULT current_timestamp(),
  `date_updated` datetime DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `articles`
--

INSERT INTO `articles` (`article_id`, `slug`, `title`, `category`, `excerpt`, `content`, `image`, `author`, `read_time`, `tags`, `status`, `views`) VALUES
(1, 'digitalisation-evenementiel-afrique', 'Comment la digitalisation transforme l\'événementiel en Afrique de l\'Ouest', 'Événementiel', 'Découvrez comment les nouvelles technologies révolutionnent la gestion d\'événements et l\'expérience des participants dans notre région.', '[{"type":"paragraph","text":"L\'événementiel africain s\'accélère grâce aux outils numériques. Billetterie en ligne, contrôle d\'accès sécurisé et automatisation marketing permettent désormais d\'orchestrer des expériences no-limit, même avec des équipes réduites."},{"type":"paragraph","text":"Chez Kanzey.co, nous avons accompagné plus de 150 promoteurs en 24 mois. Les organisateurs peuvent suivre les ventes en temps réel, segmenter leurs participants et lancer des campagnes ciblées en quelques minutes."},{"type":"list","title":"3 leviers concrets à activer","items":["Centraliser les ventes physiques et digitales dans une plateforme unique","Automatiser l\'envoi de QR codes et les rappels avant l\'événement","Analyser les parcours participants pour optimiser les éditions futures"]}]', 'https://images.unsplash.com/photo-1520607162513-77705c0f0d4a?auto=format&fit=crop&w=1200&q=80', 'Equipe Kanzey.co', '5 min', '["Digitalisation","Expérience client","Afrique de l\'Ouest"]', 'published', 2450),
(2, 'proptech-tendances-2025', 'PropTech 2025 : les tendances qui redéfinissent l\'immobilier africain', 'Immobilier', 'Intelligence artificielle, plateformes collaboratives, visites virtuelles : tour d\'horizon des innovations qui transforment la chaîne de valeur immobilière.', '[{"type":"paragraph","text":"La PropTech africaine se structure autour de trois axes : la data, l\'expérience client et l\'automatisation. Les promoteurs peuvent désormais suivre leurs stocks en temps réel et sécuriser les transactions en ligne."},{"type":"paragraph","text":"Les visites virtuelles deviennent un standard, tout comme la signature électronique. Avec E-mobi, nous offrons une expérience bout-en-bout : CRM, gestion locative, reporting et paiements intégrés."}]', 'https://images.unsplash.com/photo-1504384308090-c894fdcc538d?auto=format&fit=crop&w=1000&q=80', 'Equipe Kanzey.co', '7 min', '["PropTech","IA","Immobilier"]', 'published', 1890),
(3, 'automatisation-agents-intelligents-productivite', 'Automatisation et IA : boostez votre productivité avec des agents intelligents', 'Innovation', 'Comment déployer des workflows automatisés pour gagner du temps et offrir une meilleure expérience client dans les entreprises africaines.', '[{"type":"paragraph","text":"L\'automatisation intelligente ne remplace pas les équipes : elle libère du temps pour des tâches à haute valeur. Nous intégrons des agents IA dans les centres de contact, la qualification de leads ou la gestion d\'incidents."},{"type":"paragraph","text":"Résultat : jusqu\'à 40% de productivité en plus et des réponses clients livrées en moins de 2 minutes grâce aux workflows orchestrés."}]', 'https://images.unsplash.com/photo-1526374965328-7f61d4dc18c5?auto=format&fit=crop&w=1000&q=80', 'Equipe Kanzey.co', '6 min', '["IA","Automatisation","Productivité"]', 'published', 1520),
(4, 'success-story-eventpro-jeton', 'Success Story : comment EventPro a multiplié ses ventes par 3 avec Jeton', 'Success Story', 'Étude de cas détaillée sur la transformation digitale d\'un acteur de l\'événementiel grâce à notre solution Jeton.', '[{"type":"paragraph","text":"EventPro a digitalisé 100% de son parcours participant grâce à Jeton. Vente omnicanale, badges dynamiques et reporting live ont permis d\'anticiper la logistique et de personnaliser les offres."},{"type":"list","title":"Résultats clés","items":["+210% de ventes anticipées","-60% de files d\'attente sur site","Satisfaction client 4,8/5"]}]', 'https://images.unsplash.com/photo-1521737604893-d14cc237f11d?auto=format&fit=crop&w=1000&q=80', 'Equipe Kanzey.co', '6 min', '["Jeton","Cas client","Croissance"]', 'published', 1280),
(5, 'strategies-marketing-afrique-ouest', 'Marketing digital : 10 stratégies qui fonctionnent en Afrique de l\'Ouest', 'Marketing', 'Social commerce, influence locale, automation… les leviers marketing à privilégier pour accélérer sa croissance.', '[{"type":"paragraph","text":"La croissance passe par une stratégie marketing cohérente. Nous recommandons de combiner contenus locaux, influence ciblée et nurture automatique pour convertir plus vite."}]', 'assets/images/marqueting.avif', 'Equipe Kanzey.co', '7 min', '["Marketing","Growth","Afrique"]', 'published', 1150),
(6, 'data-analytics-decisions-strategiques', 'Data analytics : transformez vos données en décisions stratégiques', 'Analytics', 'Mettre en place une culture data-driven pour piloter ses opérations et identifier les opportunités de croissance.', '[{"type":"paragraph","text":"Les organisations qui structurent leurs données prennent de meilleures décisions. Dashboards, scénarios et alertes automatisées sécurisent la croissance."}]', 'https://images.unsplash.com/photo-1517430816045-df4b7de11d1d?auto=format&fit=crop&w=1000&q=80', 'Equipe Kanzey.co', '6 min', '["Analytics","Pilotage","Data"]', 'published', 980);

-- --------------------------------------------------------

--
-- Structure de la table `comments`
--

CREATE TABLE `comments` (
  `comment_id` int(11) NOT NULL,
  `article_id` int(11) NOT NULL,
  `nom` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `message` text NOT NULL,
  `status` enum('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `date_created` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `comments`
--

INSERT INTO `comments` (`comment_id`, `article_id`, `nom`, `email`, `message`, `status`) VALUES
(1, 4, 'Mouhamed DIOP', 'mouhamed@example.com', 'avoir la clientele et un bon chiffre d\'affaire sans le suivi de ces recettes c\'est autre chose.', 'approved'),
(2, 3, 'Fatou Fall', 'fatou@example.com', 'C\'est beau tout ce que peut faire l\'intelligence artificielle', 'approved');

-- --------------------------------------------------------

--
-- Structure de la table `contacts`
--

CREATE TABLE `contacts` (
  `contact_id` int(11) NOT NULL,
  `nom` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `telephone` varchar(20) DEFAULT NULL,
  `entreprise` varchar(255) DEFAULT NULL,
  `message` text NOT NULL,
  `status` enum('new','read','replied','archived') NOT NULL DEFAULT 'new',
  `date_created` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `rdv` (Rendez-vous)
--

CREATE TABLE `rdv` (
  `rdv_id` int(11) NOT NULL,
  `nom` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `telephone` varchar(20) NOT NULL,
  `date_heure` datetime NOT NULL,
  `message` text NOT NULL,
  `status` enum('pending','confirmed','cancelled','completed') NOT NULL DEFAULT 'pending',
  `date_created` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `rdv`
--

INSERT INTO `rdv` (`rdv_id`, `nom`, `email`, `telephone`, `date_heure`, `message`, `status`) VALUES
(1, 'Mouhamed DIOP', 'mouhameddiop165@gmail.com', '771314146', '2025-08-14 17:00:00', 'besoins d une campagne publicitaire', 'pending'),
(2, 'Mouhamed DIOP', 'mouhamed.diop36@unchk.edu.sn', '761794087', '2025-08-15 10:30:00', 'besoin d\'un tableau de bord pour la gestion de ma clinique dentaire.', 'pending'),
(3, 'Abdou Mar', 'mouhameddiop165@gmail.com', '771314146', '2025-08-06 09:00:00', 'besoin de discuter et piloter ma campagne publicitaire.', 'pending'),
(4, 'Mouhamed DIOP', 'mouhameddiop165@gmail.com', '771314146', '2025-08-06 10:40:00', 'demande de campagne publicitaire', 'pending');

-- --------------------------------------------------------

--
-- Structure de la table `realisations` (Cas clients / Success stories)
--

CREATE TABLE `realisations` (
  `realisation_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `probleme` text NOT NULL,
  `solution` text NOT NULL,
  `resultat` text NOT NULL,
  `description` text NOT NULL,
  `photo` varchar(500) NOT NULL,
  `client_name` varchar(255) DEFAULT NULL,
  `category` varchar(100) DEFAULT NULL,
  `status` enum('draft','published','archived') NOT NULL DEFAULT 'published',
  `date_published` datetime NOT NULL DEFAULT current_timestamp(),
  `date_created` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `realisations`
--

INSERT INTO `realisations` (`realisation_id`, `title`, `slug`, `probleme`, `solution`, `resultat`, `description`, `photo`, `client_name`, `category`, `status`) VALUES
(1, 'Digitalisation d\'une Résidence Hôtelière au Sénégal', 'digitalisation-residence-hotel-senegal', 'Dépendance au bouche-à-oreille, pas de présence en ligne', 'Création des réseaux sociaux, référencement sur Google Maps, partenariats avec Booking & Airbnb', '✔ +200% d\'engagement sur les réseaux\r\n✔ +65% de réservations en ligne\r\n✔ +20% de réservations directes via TikTok & Facebook', '📍 La Res\'Zac Résidence Hôtelière\r\n🔹 Problème : Dépendance au bouche-à-oreille, pas de présence en ligne\r\n✅ Solution : Création des réseaux sociaux, référencement sur Google Maps, partenariats avec\r\nBooking & Airbnb\r\n📈 Résultats :\r\n✔ +200% d\'engagement sur les réseaux\r\n✔ +65% de réservations en ligne\r\n✔ +20% de réservations directes via TikTok & Facebook\r\n🗣 Témoignage :\r\n"Avant Kanzey.co, nous dépendions uniquement du bouche-à-oreille. Maintenant, nous avons\r\ndes clients qui nous trouvent via Google et TikTok, et nous avons plus que de réservations en\r\nligne et sur site !"', 'photosRealisation/img_688b60124216d.png', 'La Res\'Zac', 'Tourisme', 'published'),
(2, 'Optimisation de la Logistique pour une Société d\'Import-Export', 'optimisation-logistique-import-export', 'Gestion manuelle des commandes et des stocks, manque de visibilité sur la rentabilité des produits', '✔ Automatisation de la gestion des commandes avec Odoo ERP\r\n✔ Création d\'un tableau de bord Power BI pour suivre la rentabilité par produit et par fournisseur\r\n✔ Analyse des données de ventes et prévisions de stock pour éviter les ruptures', '+30% d\'efficacité opérationnelle (moins de temps perdu sur l\'administration)\r\n✔ Réduction de 20% des coûts de stockage grâce à l\'analyse prédictive\r\n✔ +25% d\'augmentation des marges en optimisant l\'importation des produits les plus rentables', '📍 Client : Ivoiro Import-Export (Abidjan)\r\n🔹 Problème : Gestion manuelle des commandes et des stocks, manque de visibilité sur la rentabilité des\r\nproduits\r\n✅ Solution Kanzey.co :\r\n✔ Automatisation de la gestion des commandes avec Odoo ERP\r\n✔ Création d\'un tableau de bord Power BI pour suivre la rentabilité par produit et par fournisseur\r\n✔ Analyse des données de ventes et prévisions de stock pour éviter les ruptures\r\n📈 Résultats après 4 mois :\r\n✔ +30% d\'efficacité opérationnelle (moins de temps perdu sur l\'administration)\r\n✔ Réduction de 20% des coûts de stockage grâce à l\'analyse prédictive\r\n✔ +25% d\'augmentation des marges en optimisant l\'importation des produits les plus rentables\r\n🗣 Témoignage :\r\n"Nous avions des milliers de données mais aucune vision claire. Maintenant, en un clic, nous savons quel\r\nproduit est le plus rentable et comment optimiser notre chaîne d\'approvisionnement !"', 'photosRealisation/img_688b612dceccc.png', 'Ivoiro Import-Export', 'Logistique', 'published'),
(3, 'Stratégie Digitale et BI pour une Société Immobilière à Abidjan', 'strategie-digitale-immobilier-abidjan', 'Peu de leads qualifiés, absence de suivi des performances commerciales', '✔ Mise en place d\'un CRM avec Power BI pour analyser le pipeline de vente\r\n✔ Optimisation du SEO et présence Google My Business pour générer plus de prospects\r\n✔ Campagnes LinkedIn Ads et Facebook Ads ciblées', '✔ +20 de leads qualifiés via le digital\r\n✔ -40% de temps perdu sur des prospects non sérieux grâce aux analyses BI\r\n✔ +35% de conversions grâce au suivi en temps réel des commerciaux', '📍 Client : Ivory Properties (Abidjan, Côte d\'Ivoire)\r\n🔹 Problème : Peu de leads qualifiés, absence de suivi des performances commerciales\r\n✅ Solution Kanzey.co :\r\n✔ Mise en place d\'un CRM avec Power BI pour analyser le pipeline de vente\r\n✔ Optimisation du SEO et présence Google My Business pour générer plus de prospects\r\n✔ Campagnes LinkedIn Ads et Facebook Ads ciblées\r\n📈 Résultats après 3 mois :\r\n✔ +20 de leads qualifiés via le digital\r\n✔ -40% de temps perdu sur des prospects non sérieux grâce aux analyses BI\r\n✔ +35% de conversions grâce au suivi en temps réel des commerciaux\r\n🗣 Témoignage :\r\n"Grâce à Kanzey.co, nous avons transformé nos données en un vrai levier de croissance. Nos commerciaux\r\nsavent exactement quels prospects relancer pour maximiser nos ventes !"', 'photosRealisation/img_688b6188b809a.png', 'Ivory Properties', 'Immobilier', 'published'),
(4, 'Optimisation des Ventes d\'un Restaurant Sénégalais à Villeurbanne', 'optimisation-ventes-restaurant-villeurbanne', 'Présence sur les réseaux mais sans stratégie de vente', 'Optimisation des contenus, campagnes Facebook Ads, offres spéciales', '✔ +250% d\'engagement sur Instagram\r\n✔ +50% de commandes en semaine\r\n✔ +10% de nouveaux clients', '📍 Les petites Bouchées Villeurbanne (Lyon, France)\r\n🔹 Problème : Présence sur les réseaux mais sans stratégie de vente\r\n✅ Solution : Optimisation des contenus, campagnes Facebook Ads, offres spéciales\r\n📈 Résultats :\r\n✔ +250% d\'engagement sur Instagram\r\n✔ +50% de commandes en semaine\r\n✔ +10% de nouveaux clients\r\n🗣 Témoignage :\r\n"Nous avions des réseaux sociaux, mais nous ne savions pas comment les utiliser pour vendre plus. Grâce à\r\nKanzey.co, nos ventes ont explosé et nous avons une vraie communauté engagée !"', 'photosRealisation/img_688b61e52e7f1.JPG', 'Les petites Bouchées', 'Restauration', 'published');

-- --------------------------------------------------------

--
-- Structure de la table `users` (Administrateurs)
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','editor','author') NOT NULL DEFAULT 'admin',
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `last_login` datetime DEFAULT NULL,
  `reset_token` varchar(255) DEFAULT NULL,
  `reset_expire` datetime DEFAULT NULL,
  `date_created` datetime NOT NULL DEFAULT current_timestamp(),
  `date_updated` datetime DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `users`
-- Mot de passe par défaut: admin123 (hash bcrypt)
--

INSERT INTO `users` (`user_id`, `username`, `email`, `password`, `role`, `status`) VALUES
(1, 'admin', 'contact@kanzey.co', '$2y$10$GRyhu/TtTytYr8X7xwNtguQewgFGG/1pBGsII0PKlHJpgOxUDhGUm', 'admin', 'active');

-- --------------------------------------------------------

--
-- Structure de la table `article_views` (Statistiques de vues)
--

CREATE TABLE `article_views` (
  `view_id` int(11) NOT NULL,
  `article_id` int(11) NOT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `date_viewed` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `newsletter` (Abonnés à la newsletter)
--

CREATE TABLE `newsletter` (
  `newsletter_id` int(11) NOT NULL,
  `email` varchar(100) NOT NULL,
  `status` enum('active','unsubscribed') NOT NULL DEFAULT 'active',
  `date_subscribed` datetime NOT NULL DEFAULT current_timestamp(),
  `date_unsubscribed` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Index pour les tables
--

--
-- Index pour la table `articles`
--
ALTER TABLE `articles`
  ADD PRIMARY KEY (`article_id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `category` (`category`),
  ADD KEY `status` (`status`),
  ADD KEY `date_published` (`date_published`);

--
-- Index pour la table `comments`
--
ALTER TABLE `comments`
  ADD PRIMARY KEY (`comment_id`),
  ADD KEY `article_id` (`article_id`),
  ADD KEY `status` (`status`);

--
-- Index pour la table `contacts`
--
ALTER TABLE `contacts`
  ADD PRIMARY KEY (`contact_id`),
  ADD KEY `status` (`status`),
  ADD KEY `date_created` (`date_created`);

--
-- Index pour la table `rdv`
--
ALTER TABLE `rdv`
  ADD PRIMARY KEY (`rdv_id`),
  ADD KEY `status` (`status`),
  ADD KEY `date_heure` (`date_heure`);

--
-- Index pour la table `realisations`
--
ALTER TABLE `realisations`
  ADD PRIMARY KEY (`realisation_id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `status` (`status`);

--
-- Index pour la table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Index pour la table `article_views`
--
ALTER TABLE `article_views`
  ADD PRIMARY KEY (`view_id`),
  ADD KEY `article_id` (`article_id`),
  ADD KEY `date_viewed` (`date_viewed`);

--
-- Index pour la table `newsletter`
--
ALTER TABLE `newsletter`
  ADD PRIMARY KEY (`newsletter_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT pour les tables
--

--
-- AUTO_INCREMENT pour la table `articles`
--
ALTER TABLE `articles`
  MODIFY `article_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT pour la table `comments`
--
ALTER TABLE `comments`
  MODIFY `comment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT pour la table `contacts`
--
ALTER TABLE `contacts`
  MODIFY `contact_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `rdv`
--
ALTER TABLE `rdv`
  MODIFY `rdv_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT pour la table `realisations`
--
ALTER TABLE `realisations`
  MODIFY `realisation_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT pour la table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT pour la table `article_views`
--
ALTER TABLE `article_views`
  MODIFY `view_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `newsletter`
--
ALTER TABLE `newsletter`
  MODIFY `newsletter_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `comments`
--
ALTER TABLE `comments`
  ADD CONSTRAINT `comments_ibfk_1` FOREIGN KEY (`article_id`) REFERENCES `articles` (`article_id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `article_views`
--
ALTER TABLE `article_views`
  ADD CONSTRAINT `article_views_ibfk_1` FOREIGN KEY (`article_id`) REFERENCES `articles` (`article_id`) ON DELETE CASCADE;

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

