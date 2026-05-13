<?php
include 'includes/header.php';
require_once 'includes/articles-db.php';
$pdo = getDBConnection();
$newsCategory = $_GET['news_category'] ?? '';

// Lire dynamiquement les catégories présentes en BDD
$categories = [];
if ($pdo) {
    try {
        $stm = $pdo->query("SELECT DISTINCT category FROM articles WHERE status='published' ORDER BY category ASC");
        $categories = array_column($stm->fetchAll(), 'category');
    } catch (Exception $e) {
        $categories = ['Événementiel', 'Immobilier', 'Innovation', 'Marketing', 'Success Story', 'Analytics'];
    }
} else {
    $categories = ['Événementiel', 'Immobilier', 'Innovation', 'Marketing', 'Success Story', 'Analytics'];
}

// Charger les articles filtrés si besoin, sinon tous
$recentArticles = $newsCategory ? getArticles($newsCategory, 4) : getArticles(null, 4);
$featuredArticle = $recentArticles[0] ?? null;
$otherArticles = array_slice($recentArticles, 1, 3);
?>

<!-- Hero Section -->
<section id="accueil" class="hero-section">
    <div class="hero-overlay"></div>
    <div class="container">
        <div class="row">
            <div class="col-12 text-center hero-content">
                <h1 class="hero-title">Transformons l'Afrique par le digitale</h1>
                <p class="hero-subtitle">Des solutions digitales sectorielles pensées pour l'Afrique de l'Ouest. Nous accompagnons les entreprises dans leur transformation numérique avec des outils performants et accessibles.</p>
                <div class="hero-buttons mt-4">
                    <a href="#solutions" class="btn btn-hero-white">Découvrir nos solutions</a>
                    <a href="#contact" class="btn btn-hero-white">Demander une démo</a>
                </div>
            </div>
        </div>
    </div>
    <div class="hero-scroll-indicator">
        <a href="#solutions" class="scroll-down-icon">
            <i class="fas fa-chevron-down"></i>
        </a>
    </div>
</section>
<br>
<!-- Notre Vision, Mission et ADN -->
<section id="vision-mission" class="py-5">
    <div class="container">
        <div class="row align-items-center mb-5">
            <!-- Image à gauche -->
            <div class="col-md-6 mb-4 mb-md-0">
                <img src="<?php echo BASE_URL; ?>assets/images/meeting.jpeg" alt="Notre équipe" class="img-fluid rounded-4 vision-mission-image">
            </div>

            <!-- Vision & Mission à droite -->
            <div class="col-md-6 vision-mission-content">
                <!-- Vision -->
                <div class="d-flex align-items-start mb-4 vision-mission-item vision-mission-item-1">
                    <div class="icon-box me-4">
                        <i class="bi bi-eye"></i>
                    </div>
                    <div>
                        <h3 class="fw-bold mb-2">Notre vision</h3>
                        <p class="text-muted mb-0">
                            Devenir la référence ouest-africaine de la digitalisation sectorielle,
                            en transformant les métiers traditionnels (événementiel, immobilier, commerce…)
                            en expériences digitales simples, performantes et accessibles.
                        </p>
                    </div>
                </div>

                <!-- Mission -->
                <div class="d-flex align-items-start vision-mission-item vision-mission-item-2">
                    <div class="icon-box me-3">
                        <i class="bi bi-bullseye"></i>
                    </div>
                    <div>
                        <h3 class="fw-bold mb-2">Notre mission</h3>
                        <p class="text-muted mb-0">
                            Offrir à chaque entreprise africaine les outils numériques, la data et l'automatisation
                            pour structurer, vendre et piloter ses activités — avec efficacité, transparence
                            et ancrage local.
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- ADN -->
        <div class="row" style="justify-content: center;">
            <div class="col-md-8">
                <div class="p-4 rounded-4 text-white d-flex align-items-start adn-box" style="background: linear-gradient(90deg, #243B6B, #3C5CA8);">
                    <div class="icon-box-white me-3">
                        <i class="bi bi-stars"></i>
                    </div>
                    <div class="mb-0">
                        <h3 class="fw-bold mb-2">Notre ADN</h3>
                        <p class="mb-2">
                            Made in Africa, pensé pour l'Afrique. Technologie utile, pas gadget.
                            Personnalisation avant standardisation.
                            La data comme boussole, la performance comme résultat.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Nos Solutions Metiers -->
<section class="metiers-section py-5 bg-light" id="solutions">
    <div class="container">
        <div class="row">
            <div class="col-12 text-center mb-5">
                <h2 class="section-title metier-section-title-animate">Nos solutions métiers</h2>
                <p class="section-subtitle metier-section-subtitle-animate">Des plateformes sectorielles conçues pour transformer votre activité</p>
            </div>
        </div>
        <div class="row g-4">
            <!-- Carte Jeton -->
            <div class="col-lg-6 col-md-12">
                <div class="metier-card metier-card-jeton metier-card-animate">
                    <!-- Section supérieure bleue -->
                    <div class="metier-card-header metier-card-header-jeton">
                        <div class="d-flex align-items-start">
                            <div class="metier-icon-wrapper">
                                <i class="bi bi-ticket-perforated"></i>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h3 class="metier-card-title mb-2">Fodium</h3>
                                <span class="metier-tag">Événementiel</span>
                                <p class="metier-description mt-3 mb-0">
                                    La plateforme de billetterie et de gestion d'événements qui connecte le digital à la scène africaine.
                                </p>
                            </div>
                        </div>
                    </div>
                    <!-- Section inférieure blanche -->
                    <div class="metier-card-body">
                        <h4 class="metier-features-title">Fonctionnalités clés :</h4>
                        <ul class="metier-features-list">
                            <li>
                                <i class="bi bi-check-circle-fill"></i>
                                <span>Création d'événements multi-dates</span>
                            </li>
                            <li>
                                <i class="bi bi-check-circle-fill"></i>
                                <span>Vente de billets en ligne sécurisée</span>
                            </li>
                            <li>
                                <i class="bi bi-check-circle-fill"></i>
                                <span>QR Code anti-fraude</span>
                            </li>
                            <li>
                                <i class="bi bi-check-circle-fill"></i>
                                <span>Statistiques en temps réel</span>
                            </li>
                            <li>
                                <i class="bi bi-check-circle-fill"></i>
                                <span>Communication intégrée</span>
                            </li>
                        </ul>
                        <p class="metier-target-audience">
                            <strong>Public cible:</strong> Promoteurs, associations, agences culturelles, organisateurs privés
                        </p>
                        <div class="text-center mt-4">
                            <a href="https://fodium.kanzey.co" class="btn metier-btn metier-btn-jeton">
                                Découvrir Fodium
                                <i class="bi bi-arrow-right ms-2"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Carte E-mobi -->
            <div class="col-lg-6 col-md-12">
                <div class="metier-card metier-card-emobi metier-card-animate">
                    <!-- Section supérieure bleue -->
                    <div class="metier-card-header metier-card-header-emobi">
                        <div class="d-flex align-items-start">
                            <div class="metier-icon-wrapper">
                                <i class="bi bi-building"></i>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h3 class="metier-card-title mb-2">E-mobi</h3>
                                <span class="metier-tag">Immobilier</span>
                                <p class="metier-description mt-3 mb-0">
                                    La solution de gestion immobilière tout-en-un pour digitaliser votre parc et vos ventes.
                                </p>
                            </div>
                        </div>
                    </div>
                    <!-- Section inférieure blanche -->
                    <div class="metier-card-body">
                        <h4 class="metier-features-title">Fonctionnalités clés :</h4>
                        <ul class="metier-features-list">
                            <li>
                                <i class="bi bi-check-circle-fill"></i>
                                <span>CRM intégré</span>
                            </li>
                            <li>
                                <i class="bi bi-check-circle-fill"></i>
                                <span>Gestion locative complète</span>
                            </li>
                            <li>
                                <i class="bi bi-check-circle-fill"></i>
                                <span>Reporting automatisé</span>
                            </li>
                            <li>
                                <i class="bi bi-check-circle-fill"></i>
                                <span>Signature électronique</span>
                            </li>
                            <li>
                                <i class="bi bi-check-circle-fill"></i>
                                <span>Intégration paiements</span>
                            </li>
                        </ul>
                        <p class="metier-target-audience">
                            <strong>Public cible:</strong> Agences, promoteurs, bailleurs, entreprises immobilières
                        </p>
                        <div class="text-center mt-4">
                            <a href="#" class="btn metier-btn metier-btn-emobi">
                                Découvrir E-mobi
                                <i class="bi bi-arrow-right ms-2"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Au-delà des Solutions Sectorielles -->
<section class="transversal-solutions py-5" id="services">
    <div class="container">
        <!-- Header -->
        <div class="row">
            <div class="col-12 text-center mb-5">
                <span class="transversal-tag">Solutions d'accompagnement</span>
                <h2 class="section-title mt-3">Au-delà des solutions sectorielles</h2>
                <p class="section-subtitle">Un écosystème complet de services pour accompagner votre transformation digitale de A à Z</p>
            </div>
        </div>

        <!-- Deux grandes cartes en haut -->
        <div class="row g-4 mb-5">
            <div class="col-lg-6 col-md-12">
                <div class="transversal-feature-card transversal-card-analytics">
                    <img src="<?php echo BASE_URL; ?>assets/images/Reporting.webp" alt="Data Analytics" class="transversal-card-image">
                    <div class="transversal-card-overlay">
                        <h3 class="transversal-card-title">Data Analytics & Reporting</h3>
                        <p class="transversal-card-subtitle">Visualisez vos performances en temps réel.</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 col-md-12">
                <div class="transversal-feature-card transversal-card-analytics">
                    <img src="<?php echo BASE_URL; ?>assets/images/marqueting.avif" alt="Data Analytics" class="transversal-card-image">
                    <div class="transversal-card-overlay">
                        <h3 class="transversal-card-title">Marketing Digital 360°</h3>
                        <p class="transversal-card-subtitle">Maximisez votre présence en ligne.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Six blocs de services en grille 2x3 -->
        <div class="row g-4 mb-5">
            <!-- Marketing Digital -->
            <div class="col-lg-4 col-md-6">
                <div class="transversal-service-card">
                    <div class="service-icon service-icon-purple">
                        <i class="bi bi-megaphone"></i>
                    </div>
                    <h4 class="service-title">Marketing Digital</h4>
                    <p class="service-description">Stratégies digitales sur mesure pour amplifier votre présence en ligne et atteindre vos objectifs commerciaux.</p>
                    <ul class="service-features">
                        <li>SEO & SEA</li>
                        <li>Email Marketing</li>
                        <li>Content Marketing</li>
                        <li>Growth Hacking</li>
                    </ul>
                </div>
            </div>
            <!-- Gestion Réseaux Sociaux -->
            <div class="col-lg-4 col-md-6">
                <div class="transversal-service-card">
                    <div class="service-icon service-icon-blue">
                        <i class="bi bi-share"></i>
                    </div>
                    <h4 class="service-title">Gestion Réseaux Sociaux</h4>
                    <p class="service-description">Animation et optimisation de vos réseaux sociaux pour créer une communauté engagée autour de votre marque.</p>
                    <ul class="service-features">
                        <li>Community Management</li>
                        <li>Publicité Social Media</li>
                        <li>Création de contenu</li>
                        <li>Analytics</li>
                    </ul>
                </div>
            </div>
            <!-- Agents IA & Automatisation -->
            <div class="col-lg-4 col-md-6">
                <div class="transversal-service-card">
                    <div class="service-icon service-icon-green">
                        <i class="bi bi-robot"></i>
                    </div>
                    <h4 class="service-title">Agents IA & Automatisation</h4>
                    <p class="service-description">Déploiement d'agents intelligents et workflows automatisés, pour optimiser vos processus métiers.</p>
                    <ul class="service-features">
                        <li>Chatbots IA</li>
                        <li>Automatisation RPA</li>
                        <li>Agents conversationnels</li>
                        <li>Workflows intelligents</li>
                    </ul>
                </div>
            </div>
            <!-- Data & Analytics -->
            <div class="col-lg-4 col-md-6">
                <div class="transversal-service-card">
                    <div class="service-icon service-icon-orange">
                        <i class="bi bi-bar-chart"></i>
                    </div>
                    <h4 class="service-title">Data & Analytics</h4>
                    <p class="service-description">Transformez vos données en insights actionnables, pour une prise de décision éclairée et un avantage concurrentiel.</p>
                    <ul class="service-features">
                        <li>Dashboard Power BI</li>
                        <li>Reporting automatisé</li>
                        <li>Analyse prédictive</li>
                        <li>Data visualisation</li>
                    </ul>
                </div>
            </div>
            <!-- Design & UX -->
            <div class="col-lg-4 col-md-6">
                <div class="transversal-service-card">
                    <div class="service-icon service-icon-purple">
                        <i class="bi bi-pencil"></i>
                    </div>
                    <h4 class="service-title">Design & UX</h4>
                    <p class="service-description">Création d'interfaces utilisateur modernes et intuitives qui transforment l'expérience de vos clients.</p>
                    <ul class="service-features">
                        <li>UI/UX Design</li>
                        <li>Prototypage</li>
                        <li>Design System</li>
                        <li>Branding digital</li>
                    </ul>
                </div>
            </div>
            <!-- Développement Sur Mesure -->
            <div class="col-lg-4 col-md-6">
                <div class="transversal-service-card">
                    <div class="service-icon service-icon-orange">
                        <i class="bi bi-lightning-charge-fill"></i>
                    </div>
                    <h4 class="service-title">Développement Sur Mesure</h4>
                    <p class="service-description">Applications, services et modules personnalisés, pour répondre à vos besoins métiers spécifiques.</p>
                    <ul class="service-features">
                        <li>Web apps</li>
                        <li>Mobile apps</li>
                        <li>APIs</li>
                        <li>Intégrations</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Section CTA IA -->
        <div class="row g-0 mb-5">
            <div class="col-lg-6">
                <div class="ai-cta-image h-100">
                    <img src="<?php echo BASE_URL; ?>assets/images/ia.jpg" alt="Intelligence Artificielle" class="img-fluid h-100 w-100" style="object-fit:cover; object-position:center; height:100%; width:100%;">
                </div>
            </div>
            <div class="col-lg-6">
                <div class="ai-cta-content">
                    <span class="ai-tag col-md-4">Intelligence artificielle</span>
                    <h3 class="ai-cta-title">Propulsez votre entreprise avec l'IA</h3>
                    <p class="ai-cta-description">Nous développons des agents IA personnalisés et des solutions d'automatisation intelligente pour transformer vos processus métiers et améliorer votre efficacité opérationnelle.</p>
                    <ul class="ai-cta-checklist">
                        <li><i class="bi bi-check-circle-fill"></i> Chatbots intelligents, agents leader secteur</li>
                        <li><i class="bi bi-check-circle-fill"></i> Automatisation des tâches répétitives</li>
                        <li><i class="bi bi-check-circle-fill"></i> Analyse prédictive et recommandations IA</li>
                    </ul>
                    <a href="#" class="btn ai-cta-btn">
                        Découvrir nos solutions IA
                        <i class="bi bi-arrow-right ms-2"></i>
                    </a>
                </div>
            </div>
        </div>

        <!-- Architecture de la Plateforme -->
        <div class="row">
            <div class="col-12 text-center mb-5">
                <h2 class="section-title">Architecture de la plateforme</h2>
                <p class="section-subtitle">Une structure modulaire et évolutive en trois couches complémentaires</p>
            </div>
        </div>
        <div class="row g-4">
            <div class="col-lg-4 col-md-6">
                <div class="architecture-card">
                    <div class="architecture-icon">
                        <i class="bi bi-box"></i>
                    </div>
                    <h4 class="architecture-title">Core Technologique</h4>
                    <p class="architecture-description">Automatisation, Sécurisation, CRM, analytics, IA, AR</p>
                </div>
            </div>
            <div class="col-lg-4 col-md-6">
                <div class="architecture-card">
                    <div class="architecture-icon">
                        <i class="bi bi-stack"></i>
                    </div>
                    <h4 class="architecture-title">Pôles Sectoriels</h4>
                    <p class="architecture-description">Fodium (Événementiel) et E-mobi (Immobilier)</p>
                </div>
            </div>
            <div class="col-lg-4 col-md-6">
                <div class="architecture-card">
                    <div class="architecture-icon">
                        <i class="bi bi-headset"></i>
                    </div>
                    <h4 class="architecture-title">Services d'Accompagnement</h4>
                    <p class="architecture-description">Diagnostic, support, automatisation, formation, communication, data</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Nos Valeurs -->
<section id="valeurs" class="values-section py-5 bg-light">
    <div class="container">
        <div class="row">
            <div class="col-12 text-center mb-5">
                <h2 class="section-title">Nos valeurs</h2>
                <p class="section-subtitle">Les principes qui guident notre action au quotidien</p>
            </div>
        </div>
        <div class="row g-4">
            <div class="col-lg-3 col-md-6">
                <div class="value-card">
                    <div class="value-icon-wrapper">
                        <i class="bi bi-lightbulb"></i>
                    </div>
                    <h4 class="value-card-title">Clarté</h4>
                    <p class="value-card-description">Des solutions simples et transparentes, pensées pour être comprises et maîtrisées par tous.</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="value-card">
                    <div class="value-icon-wrapper">
                        <i class="bi bi-award"></i>
                    </div>
                    <h4 class="value-card-title">Excellence</h4>
                    <p class="value-card-description">Une exigence de qualité à chaque étape du développement à l'accompagnement client.</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="value-card">
                    <div class="value-icon-wrapper">
                        <i class="bi bi-people"></i>
                    </div>
                    <h4 class="value-card-title">Accessibilité</h4>
                    <p class="value-card-description">Des outils digitaux adaptés au contexte africain, accessibles et abordables.</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="value-card">
                    <div class="value-icon-wrapper">
                        <i class="bi bi-lightning-charge"></i>
                    </div>
                    <h4 class="value-card-title">Impact</h4>
                    <p class="value-card-description">Créer une vraie transformation digitale qui génère des résultats mesurables.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Notre Impact en Chiffres -->
<section id="impact" class="impact-section py-5">
    <div class="container">
        <div class="row">
            <div class="col-12 text-center mb-5">
                <h2 class="section-title">Notre Impact en chiffres</h2>
                <p class="section-subtitle">Des résultats concrets au service de la transformation digitale africaine</p>
            </div>
        </div>
        <div class="row g-4">
            <div class="col-lg-3 col-md-6">
                <div class="impact-card-new">
                    <div class="impact-icon">
                        <i class="bi bi-graph-up"></i>
                    </div>
                    <h2 class="impact-number-new">20+</h2>
                    <p class="impact-text-new">Partenariats gérés</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="impact-card-new">
                    <div class="impact-icon">
                        <i class="bi bi-people"></i>
                    </div>
                    <h2 class="impact-number-new">150+</h2>
                    <p class="impact-text-new">Biens immobiliers</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="impact-card-new">
                    <div class="impact-icon">
                        <i class="bi bi-award"></i>
                    </div>
                    <h2 class="impact-number-new">10+</h2>
                    <p class="impact-text-new">Entreprises partenaires</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="impact-card-new">
                    <div class="impact-icon">
                        <i class="bi bi-star"></i>
                    </div>
                    <h2 class="impact-number-new">15K+</h2>
                    <p class="impact-text-new">Utilisateurs actifs</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Ils Nous Ont Fait Confiance -->
<section class="trust-section py-5 bg-light">
    <div class="container">
        <div class="row">
            <div class="col-12 text-center mb-5">
                <h2 class="section-title">Ils nous ont fait confiance</h2>
                <p class="section-subtitle">Des entreprises innovantes de toute l'Afrique de l'Ouest choisissent Kanzey.co pour leur transformation digitale</p>
            </div>
        </div>
        <div class="row justify-content-center">
            <div class="col-12 col-lg-12 col-xl-10">
                <div class="trust-container-wrapper position-relative">
                    <!-- Point décoratif gauche -->
                    <div class="trust-decorative-dot trust-dot-left"></div>
                    <!-- Point décoratif droit -->
                    <div class="trust-decorative-dot trust-dot-right"></div>
                    <!-- Conteneur principal avec les cartes -->
                    <div class="trust-cards-container">
                        <div class="row g-3">
                            <!-- Row 1 -->
                            <div class="col-6 col-md-3">
                                <div class="trust-industry-card">
                                    <div class="trust-industry-icon trust-icon-eventiel">
                                        <img src="<?php echo BASE_URL; ?>assets/images/CICBAD.jpeg" alt="CICBAB" class="trust-industry-logo">
                                    </div>
                                    <p class="trust-industry-label">BTP</p>
                                </div>
                            </div>
                            <div class="col-6 col-md-3">
                                <div class="trust-industry-card">
                                    <div class="trust-industry-icon trust-icon-immobilier">
                                        <img src="<?php echo BASE_URL; ?>assets/images/1.jpg" alt="LA PAIX TRAITEUR" class="trust-industry-logo">
                                    </div>
                                    <p class="trust-industry-label">Restauration</p>
                                </div>
                            </div>
                            <div class="col-6 col-md-3">
                                <div class="trust-industry-card">
                                    <div class="trust-industry-icon">
                                        <img src="<?php echo BASE_URL; ?>assets/images/Boutique paysanne CI.jpeg" alt="Boutique paysanne CI" class="trust-industry-logo">
                                    </div>
                                    <p class="trust-industry-label">E-commerce - Agroalimentaire</p>
                                </div>
                            </div>
                            <div class="col-6 col-md-3">
                                <div class="trust-industry-card">
                                    <div class="trust-industry-icon trust-icon-tech">
                                        <img src="<?php echo BASE_URL; ?>assets/images/By-So'ba - Lyon.JPG" alt="By-So'ba - Lyon" class="trust-industry-logo">
                                    </div>
                                    <p class="trust-industry-label">Restauration</p>
                                </div>
                            </div>
                            <!-- Row 2 -->
                            <div class="col-6 col-md-3">
                                <div class="trust-industry-card">
                                    <div class="trust-industry-icon trust-icon-media">
                                        <img src="<?php echo BASE_URL; ?>assets/images/IMG_3700.jpg" alt="SOGIM-C sarl" class="trust-industry-logo">
                                    </div>
                                    <p class="trust-industry-label">Immobillier - BTP</p>
                                </div>
                            </div>
                            <div class="col-6 col-md-3">
                                <div class="trust-industry-card">
                                    <div class="trust-industry-icon trust-icon-distribution">
                                        <img src="<?php echo BASE_URL; ?>assets/images/LOGO RES'zac-Dakar.png" alt="RES'zac-Dakar" class="trust-industry-logo">
                                    </div>
                                    <p class="trust-industry-label">résidence Hôtelière</p>
                                </div>
                            </div>
                            <div class="col-6 col-md-3">
                                <div class="trust-industry-card">
                                    <div class="trust-industry-icon trust-icon-services">
                                        <img src="<?php echo BASE_URL; ?>assets/images/PLC-CI.jpeg" alt="PLC-CI" class="trust-industry-logo">
                                    </div>
                                    <p class="trust-industry-label">Agroalimentaire</p>
                                </div>
                            </div>
                            <div class="col-6 col-md-3">
                                <div class="trust-industry-card">
                                    <div class="trust-industry-icon trust-icon-publicite">
                                        <img src="<?php echo BASE_URL; ?>assets/images/Walo Up Dagana.png" alt="Walo Up Dagana" class="trust-industry-logo">
                                    </div>
                                    <p class="trust-industry-label">Événementiel</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Témoignages -->
<section class="testimonials-section py-5">
    <div class="container">
        <div class="row g-4">
            <div class="col-lg-4 col-md-6">
                <div class="testimonial-card">
                    <div class="testimonial-stars mb-3">
                        <i class="bi bi-star-fill"></i>
                        <i class="bi bi-star-fill"></i>
                        <i class="bi bi-star-fill"></i>
                        <i class="bi bi-star-fill"></i>
                        <i class="bi bi-star-fill"></i>
                    </div>
                    <p class="testimonial-text">Kanzey.co a su transformer notre façon de gérer nos événements. La plateforme Jeton est intuitive et nous fait gagner un temps précieux dans l'organisation et la vente de billets.</p>
                    <div class="testimonial-author">
                        <div class="testimonial-avatar">A</div>
                        <div class="testimonial-info">
                            <h5 class="testimonial-name">Amadou Diallo</h5>
                            <p class="testimonial-role">Directeur, EventPlus Sénégal</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6">
                <div class="testimonial-card">
                    <div class="testimonial-stars mb-3">
                        <i class="bi bi-star-fill"></i>
                        <i class="bi bi-star-fill"></i>
                        <i class="bi bi-star-fill"></i>
                        <i class="bi bi-star-fill"></i>
                        <i class="bi bi-star-fill"></i>
                    </div>
                    <p class="testimonial-text">E-mobi a révolutionné notre gestion immobilière. L'interface est claire, les fonctionnalités complètes et l'équipe toujours disponible pour nous accompagner.</p>
                    <div class="testimonial-author">
                        <div class="testimonial-avatar">F</div>
                        <div class="testimonial-info">
                            <h5 class="testimonial-name">Fatou Ndiaye</h5>
                            <p class="testimonial-role">CEO, ImmoPlus Côte d'Ivoire</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6">
                <div class="testimonial-card">
                    <div class="testimonial-stars mb-3">
                        <i class="bi bi-star-fill"></i>
                        <i class="bi bi-star-fill"></i>
                        <i class="bi bi-star-fill"></i>
                        <i class="bi bi-star-fill"></i>
                        <i class="bi bi-star-fill"></i>
                    </div>
                    <p class="testimonial-text">L'accompagnement de Kanzey.co dans notre transformation digitale a été exceptionnel. Leur expertise et leur approche sur mesure ont vraiment fait la différence.</p>
                    <div class="testimonial-author">
                        <div class="testimonial-avatar">K</div>
                        <div class="testimonial-info">
                            <h5 class="testimonial-name">Kofi Mensah</h5>
                            <p class="testimonial-role">Fondateur, TechHub Ghana</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Dernières Actualités -->
<section class="news-section py-5" style="padding-top: 6rem;" id="actualites">
    <div class="container">
        <div class="news-header text-center mb-5">
            <span class="transversal-tag">Blog & Actualités</span>
            <h1 class="news-title">Nos insights pour accélérer votre transformation</h1>
            <p class="news-subtitle">Cas clients, tendances sectorielles, retours d'expérience et conseils actionnables.</p>
        </div>
        <?php if ($featuredArticle): ?>
            <?php 
                $featuredImg = $featuredArticle['image'];
                if (!preg_match('/^https?:\/\//', $featuredImg)) {
                    $featuredImg = BASE_URL . $featuredImg;
                }
            ?>
            <div class="news-feature mb-5">
                <div class="news-feature-image">
                    <img src="<?php echo htmlspecialchars($featuredImg); ?>" alt="<?php echo htmlspecialchars($featuredArticle['title']); ?>">
                    <span class="news-tag"><?php echo htmlspecialchars($featuredArticle['category']); ?></span>
                </div>
                <div class="news-feature-content">
                    <div class="news-feature-meta">
                        <span><?php echo htmlspecialchars($featuredArticle['date']); ?></span>
                        <span><?php echo htmlspecialchars($featuredArticle['read_time'] ?? ''); ?></span>
                    </div>
                    <h3 class="news-feature-title"><?php echo htmlspecialchars($featuredArticle['title']); ?></h3>
                    <p class="news-feature-excerpt"><?php echo htmlspecialchars($featuredArticle['excerpt']); ?></p>
                    <div class="news-feature-actions">
                        <a href="<?php echo BASE_URL; ?>article-detail.php?slug=<?php echo urlencode($featuredArticle['slug']); ?>" class="btn-news-primary">Lire l'article</a>
                        <button class="btn-news-icon" aria-label="Enregistrer l'article">
                            <i class="bi bi-bookmark"></i>
                        </button>
                    </div>
                </div>
            </div>
        <?php endif; ?>
        <div class="news-grid">
            <?php foreach ($otherArticles as $article): ?>
                <?php 
                    $articleImg = $article['image'];
                    if (!preg_match('/^https?:\/\//', $articleImg)) {
                        $articleImg = BASE_URL . $articleImg;
                    }
                ?>
                <article class="news-card">
                    <div class="news-card-image">
                        <img src="<?php echo htmlspecialchars($articleImg); ?>" alt="<?php echo htmlspecialchars($article['title']); ?>">
                        <span class="news-tag"><?php echo htmlspecialchars($article['category']); ?></span>
                    </div>
                    <div class="news-card-body">
                        <div class="news-card-meta">
                            <span><?php echo htmlspecialchars($article['date']); ?></span>
                            <span><?php echo htmlspecialchars($article['read_time'] ?? ''); ?></span>
                        </div>
                        <h4 class="news-card-title"><?php echo htmlspecialchars($article['title']); ?></h4>
                        <p class="news-card-excerpt"><?php echo htmlspecialchars($article['excerpt']); ?></p>
                        <a href="<?php echo BASE_URL; ?>article-detail.php?slug=<?php echo urlencode($article['slug']); ?>" class="news-card-link">Lire plus</a>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
        <div class="news-footer">
            <a href="<?php echo BASE_URL; ?>articles.php" class="btn btn-outline-primary mt-4">Voir toutes les actualités</a>
        </div>
    </div>
</section>

<!-- Restez informé de nos actualités -->
<section class="cta-section py-5">
    <div class="container">
        <div class="row">
            <div class="col-12 text-center">
                <h2 class="cta-title text-white mb-3">Restez informé de nos actualités</h2>
                <p class="cta-subtitle text-white mb-4">Recevez notre newsletter mensuelle avec les dernières tendances, conseils et success stories</p>
                <form id="newsletterForm" class="cta-newsletter-form d-flex justify-content-center align-items-center flex-wrap" method="post" style="gap: 10px;">
                    <input type="email" class="form-control" name="cta_newsletter_email" placeholder="Votre email" required style="max-width: 320px;">
                    <button type="submit" class="btn btn-cta-white">S'abonner</button>
                </form>
            </div>
        </div>
    </div>
</section>

<!-- Qui sommes nous ? -->
<section id="apropos" class="about-section py-5">
    <div class="container">
        <div class="about-header text-center">
            <span class="section-badge">À propos</span>
            <h2 class="about-title">Qui sommes-nous&nbsp;?</h2>
            <p class="about-description">Kanzey.co est né de la conviction qu’en Afrique, la transformation digitale doit être accessible, performante et adaptée au contexte local.</p>
        </div>
        <div class="about-hero">
            <div class="about-story">
                <h3>Notre histoire</h3>
                <p>Fondée par des experts passionnés par la technologie et l’Afrique, Kanzey.co s’est donnée pour mission de démocratiser l’accès aux outils digitaux de qualité professionnelle.</p>
                <p>Nous avons constitué une équipe pluridisciplinaire d’experts métiers africains. Chacun apporte sa connaissance terrain pour créer des solutions digitales qui transforment vraiment les entreprises.</p>
                <p>Aujourd’hui, nous accompagnons des acteurs dans l’événementiel, l’immobilier, le commerce et l’éducation – avec un impact mesurable et durable.</p>
            </div>
            <div class="about-hero-image">
                <img src="https://images.unsplash.com/photo-1521737604893-d14cc237f11d?auto=format&fit=crop&w=960&q=80" alt="Équipe Kanzey en réunion">
            </div>
        </div>
    </div>
</section>

<!-- Pourquoi Nous Choisir ? -->
<section class="why-us-section py-5">
    <div class="container">
        <div class="why-us-header text-center">
            <h2 class="why-us-title">Pourquoi nous choisir&nbsp;?</h2>
            <p class="why-us-subtitle">Nos piliers pour accélérer votre transformation digitale.</p>
        </div>
        <div class="why-us-grid">
            <div class="why-us-card">
                <span class="why-us-icon"><i class="bi bi-geo-alt"></i></span>
                <h3>Expertise locale</h3>
                <p>Une équipe ancrée en Afrique de l’Ouest, au plus près des réalités terrain.</p>
            </div>
            <div class="why-us-card">
                <span class="why-us-icon"><i class="bi bi-graph-up-arrow"></i></span>
                <h3>Résultats mesurables</h3>
                <p>Des objectifs clairs et un suivi en temps réel pour prouver l’impact.</p>
            </div>
            <div class="why-us-card">
                <span class="why-us-icon"><i class="bi bi-lightning-charge"></i></span>
                <h3>Innovation continue</h3>
                <p>Des solutions évolutives co-construites avec nos partenaires métiers.</p>
            </div>
            <div class="why-us-card">
                <span class="why-us-icon"><i class="bi bi-people"></i></span>
                <h3>Accompagnement dédié</h3>
                <p>Un expert vous suit à chaque étape, du cadrage au déploiement.</p>
            </div>
        </div>
    </div>
</section>

<!-- Notre Équipe -->
<section class="team-section py-5">
    <div class="container">
        <div class="team-header text-center">
            <h2 class="team-title">Notre équipe</h2>
            <p class="team-subtitle">Une équipe pluridisciplinaire pour porter vos ambitions digitales.</p>
        </div>
        <div class="team-grid">
            <div class="team-chip" style="flex-direction: column; align-items: center; text-align: center;">
                <span class="team-chip-icon" style="margin-bottom: 10px;"><i class="bi bi-cpu"></i></span>
                <div class="team-chip-content">
                    <h3>Équipe Tech</h3>
                    <p>Développement & innivation</p>
                </div>
            </div>
            <div class="team-chip" style="flex-direction: column; align-items: center; text-align: center;">
                <span class="team-chip-icon" style="margin-bottom: 10px;"><i class="bi bi-briefcase"></i></span>
                <div class="team-chip-content">
                    <h3>Équipe Business</h3>
                    <p>Stratégie & croissance</p>
                </div>
            </div>
            <div class="team-chip" style="flex-direction: column; align-items: center; text-align: center;">
                <span class="team-chip-icon" style="margin-bottom: 10px;"><i class="bi bi-headset"></i></span>
                <div class="team-chip-content">
                    <h3>Équipe Support</h3>
                    <p>Accompagnement client</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Nos Partenaires -->
<section class="partners-section py-5">
    <div class="container">
        <div class="partners-header text-center">
            <h2 class="partners-title">Nos outils digitaux</h2>
            <p class="partners-subtitle">Ils co-construisent avec nous l’avenir du digital en Afrique.</p>
        </div>
        <div class="partners-grid">
            <div class="partners-marquee-container">
                <div class="partners-marquee-track">
                    <div class="partner-card">
                        <div class="partner-icon ">
                            <img src="<?php echo BASE_URL; ?>assets/images/figma.png" alt="figma" style="width: 220%; height: 200%;">
                        </div>
                        <span class="partner-label"> </span>
                    </div>
                    <div class="partner-card">
                        <div class="partner-icon">
                            <img src="<?php echo BASE_URL; ?>assets/images/2.png" alt="metricool" style="width: 220%; height: 200%;">
                        </div>
                        <span class="partner-label"> </span>
                    </div>
                    <div class="partner-card">
                        <div class="partner-icon ">
                            <img src="<?php echo BASE_URL; ?>assets/images/12.png" alt="zoom" style="width: 220%; height: 200%;">
                        </div>
                        <span class="partner-label"> </span>
                    </div>
                    <div class="partner-card">
                        <div class="partner-icon">
                            <img src="<?php echo BASE_URL; ?>assets/images/Tally.so-Logo.jpg" alt="Tally" style="width: 220%; height: 200%;">
                        </div>
                        <span class="partner-label"> </span>
                    </div>
                    <div class="partner-card">
                        <div class="partner-icon">
                            <img src="<?php echo BASE_URL; ?>assets/images/18.png" alt="Teams" style="width: 220%; height: 200%;">
                        </div>
                        <span class="partner-label"> </span>
                    </div>
                    <div class="partner-card">
                        <div class="partner-icon">
                            <img src="<?php echo BASE_URL; ?>assets/images/19.png" alt="Meta" style="width: 220%; height: 200%;">
                        </div>
                        <span class="partner-label"> </span>
                    </div>
                    <div class="partner-card">
                        <div class="partner-icon">
                            <img src="<?php echo BASE_URL; ?>assets/images/20.png" alt="Slack" style="width: 220%; height: 200%;">
                        </div>
                        <span class="partner-label"> </span>
                    </div>
                    <div class="partner-card">
                        <div class="partner-icon">
                            <img src="<?php echo BASE_URL; ?>assets/images/21.png" alt="Webhooks" style="width: 220%; height: 200%;">
                        </div>
                        <span class="partner-label"> </span>
                    </div>
                    <div class="partner-card">
                        <div class="partner-icon">
                            <img src="<?php echo BASE_URL; ?>assets/images/22.png" alt="Power Bi " style="width: 220%; height: 200%;">
                        </div>
                        <span class="partner-label"> </span>
                    </div>
                    <div class="partner-card">
                        <div class="partner-icon">
                            <img src="<?php echo BASE_URL; ?>assets/images/23.png" alt="N8N" style="width: 220%; height: 200%;">
                        </div>
                        <span class="partner-label"> </span>
                    </div>
                    <div class="partner-card">
                        <div class="partner-icon">
                            <img src="<?php echo BASE_URL; ?>assets/images/24.png" alt="Notion" style="width: 220%; height: 200%;">
                        </div>
                        <span class="partner-label"> </span>
                    </div>
                    <div class="partner-card">
                        <div class="partner-icon">
                            <img src="<?php echo BASE_URL; ?>assets/images/25.png" alt="Perplexity" style="width: 220%; height: 200%;">
                        </div>
                        <span class="partner-label"> </span>
                    </div>
                    <!-- Repeat cards for seamless looping effect -->
                    <div class="partner-card">
                        <div class="partner-icon">
                            <img src="<?php echo BASE_URL; ?>assets/images/figma.png" alt="figma" style="width: 220%; height: 200%;">
                        </div>
                        <span class="partner-label"> </span>
                    </div>
                    <div class="partner-card">
                        <div class="partner-icon">
                            <img src="<?php echo BASE_URL; ?>assets/images/2.png" alt="metricool" style="width: 220%; height: 200%;">
                        </div>
                        <span class="partner-label"> </span>
                    </div>
                    <div class="partner-card">
                        <div class="partner-icon ">
                            <img src="<?php echo BASE_URL; ?>assets/images/12.png" alt="zoom" style="width: 220%; height: 200%;">
                        </div>
                        <span class="partner-label"> </span>
                    </div>
                    <div class="partner-card">
                        <div class="partner-icon">
                            <img src="<?php echo BASE_URL; ?>assets/images/Tally.so-Logo.jpg" alt="Tally" style="width: 220%; height: 200%;">
                        </div>
                        <span class="partner-label"> </span>
                    </div>
                    <div class="partner-card">
                        <div class="partner-icon">
                            <img src="<?php echo BASE_URL; ?>assets/images/18.png" alt="Teams" style="width: 220%; height: 200%;">
                        </div>
                        <span class="partner-label"> </span>
                    </div>
                    <div class="partner-card">
                        <div class="partner-icon">
                            <img src="<?php echo BASE_URL; ?>assets/images/19.png" alt="Meta" style="width: 220%; height: 200%;">
                        </div>
                        <span class="partner-label"> </span>
                    </div>
                    <div class="partner-card">
                        <div class="partner-icon">
                            <img src="<?php echo BASE_URL; ?>assets/images/20.png" alt="Slack" style="width: 220%; height: 200%;">
                        </div>
                        <span class="partner-label"> </span>
                    </div>
                    <div class="partner-card">
                        <div class="partner-icon">
                            <img src="<?php echo BASE_URL; ?>assets/images/21.png" alt="Webhooks" style="width: 220%; height: 200%;">
                        </div>
                        <span class="partner-label"> </span>
                    </div>
                    <div class="partner-card">
                        <div class="partner-icon">
                            <img src="<?php echo BASE_URL; ?>assets/images/22.png" alt="Power Bi " style="width: 220%; height: 200%;">
                        </div>
                        <span class="partner-label"> </span>
                    </div>
                    <div class="partner-card">
                        <div class="partner-icon">
                            <img src="<?php echo BASE_URL; ?>assets/images/23.png" alt="N8N" style="width: 220%; height: 200%;">
                        </div>
                        <span class="partner-label"> </span>
                    </div>
                    <div class="partner-card">
                        <div class="partner-icon">
                            <img src="<?php echo BASE_URL; ?>assets/images/24.png" alt="Notion" style="width: 220%; height: 200%;">
                        </div>
                        <span class="partner-label"> </span>
                    </div>
                    <div class="partner-card">
                        <div class="partner-icon">
                            <img src="<?php echo BASE_URL; ?>assets/images/25.png" alt="Perplexity" style="width: 220%; height: 200%;">
                        </div>
                        <span class="partner-label"> </span>
                    </div>
                    <script>
                        var marquee = document.querySelector('.partners-marquee-track');
                        if (!marquee) return;
                        marquee.parentElement.addEventListener('mouseenter', function() {
                            marquee.style.animationPlayState = 'paused';
                        });
                        marquee.parentElement.addEventListener('mouseleave', function() {
                            marquee.style.animationPlayState = 'running';
                        });
                    </script>
                </div>
            </div>
</section>

<!-- Prêt à Transformer Votre Entreprise ? -->
<section class="cta-section py-5">
    <div class="container">
        <div class="row">
            <div class="col-12 text-center">
                <h2 class="cta-title text-white mb-3">Prêt à transformer votre entreprise ?</h2>
                <p class="cta-subtitle text-white mb-4">Rejoignez les entreprises qui ont choisi Kanzey.co pour leur transformation digitale. Nos experts vous accompagnent à chaque étape.</p>
                <div class="d-flex justify-content-center align-items-center flex-wrap" style="gap: 16px;">
                    <a href="#contact" class="btn btn-cta-white" style="display: flex; align-items: center; gap: 8px; min-width: 270px; justify-content: center;">
                        Demander une démo gratuite
                        <span class="ms-2" style="font-size:1.15em;"><i class="bi bi-arrow-right"></i></span>
                    </a>
                    <a href="https://tally.so/r/nGk88k" target="_blank" class="btn btn-cta-white" style="display: flex; align-items: center; gap: 8px; min-width: 270px; justify-content: center;">
                        Discuter avec un expert
                    </a>
                </div>
                <div class="d-flex justify-content-center align-items-center flex-wrap mt-4" style="gap: 24px; color: #fff; font-size: 1.07rem;">
                    <div class="d-flex align-items-center" style="gap:10px;">
                        <i class="bi bi-telephone" style="font-size: 1.2em;"></i>
                        <span>+221 78 956 36 38</span>
                    </div>
                    <span style="font-size:2em; opacity:0.45;">&middot;</span>
                    <div class="d-flex align-items-center" style="gap:10px;">
                        <i class="bi bi-envelope" style="font-size: 1.15em;"></i>
                        <span>contact@kanzey.co</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Contactez-nous -->
<section id="contact" class="contact-section py-5">
    <div class="container">
        <div class="contact-header text-center">
            <h2 class="contact-title">Contactez-nous</h2>
            <p class="contact-subtitle">Une question ? Un projet ? Notre équipe est là pour vous accompagner.</p>
        </div>
        <div class="contact-content">
            <form id="contactForm" class="contact-form-card" method="post">
                <div class="form-grid">
                    <div class="form-group">
                        <label for="contact-name">Nom complet <span>*</span></label>
                        <input type="text" id="contact-name" name="name" placeholder="Votre nom" required>
                    </div>
                    <div class="form-group">
                        <label for="contact-email">Email <span>*</span></label>
                        <input type="email" id="contact-email" name="email" placeholder="Votre email" required>
                    </div>
                    <div class="form-group">
                        <label for="contact-company">Entreprise</label>
                        <input type="text" id="contact-company" name="company" placeholder="Nom de votre entreprise">
                    </div>
                    <div class="form-group">
                        <label for="contact-phone">Téléphone</label>
                        <input type="tel" id="contact-phone" name="phone" placeholder="+221 XX XXX XX XX">
                    </div>
                    <div class="form-group form-group-full">
                        <label for="contact-message">Message <span>*</span></label>
                        <textarea id="contact-message" name="message" rows="4" placeholder="Décrivez votre projet ou votre besoin..." required></textarea>
                    </div>
                </div>
                <button type="submit" class="btn-contact-submit">
                    Envoyer le message
                    <i class="bi bi-send ms-2"></i>
                </button>
            </form>

            <div class="contact-details">
                <div class="contact-card">
                    <div class="contact-card-icon">
                        <i class="bi bi-geo-alt"></i>
                    </div>
                    <div class="contact-card-content">
                        <h3>Adresse</h3>
                        <p>Dakar, Sénégal<br>Afrique de l’Ouest</p>
                    </div>
                </div>
                <div class="contact-card">
                    <div class="contact-card-icon">
                        <i class="bi bi-telephone"></i>
                    </div>
                    <div class="contact-card-content">
                        <h3>Téléphone</h3>
                        <p>+221 78 956 36 38</p>
                    </div>
                </div>
                <div class="contact-card">
                    <div class="contact-card-icon">
                        <i class="bi bi-envelope"></i>
                    </div>
                    <div class="contact-card-content">
                        <h3>Email</h3>
                        <p>contact@kanzey.co</p>
                    </div>
                </div>
                <div class="contact-hours-card">
                    <h3>Horaires d'ouverture</h3>
                    <ul>
                        <li><span>Lundi - Vendredi</span><span>9h00 - 17h00</span></li>
                        <li><span>Samedi</span><span>10h00 - 13h00</span></li>
                        <li><span>Dimanche</span><span>Fermé</span></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</section>

<?php
include 'includes/footer.php';
?>

<script>
    // Fonction pour afficher une notification toast
    function showNotification(message, type = 'success') {
        const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
        const toast = document.createElement('div');
        toast.className = `alert ${alertClass} position-fixed top-0 end-0 m-3`;
        toast.style.zIndex = '9999';
        toast.innerHTML = '<i class="bi ' + (type === 'success' ? 'bi-check-circle' : 'bi-exclamation-circle') + ' me-2"></i>' + message;
        document.body.appendChild(toast);

        setTimeout(() => {
            toast.remove();
        }, 3000);
    }

    document.addEventListener('DOMContentLoaded', function() {
        const newsletterForm = document.getElementById('newsletterForm');

        if (newsletterForm) {
            newsletterForm.addEventListener('submit', function(e) {
                e.preventDefault();

                const formData = new FormData(newsletterForm);
                const submitBtn = newsletterForm.querySelector('button[type="submit"]');
                const originalText = submitBtn.innerHTML;

                // Désactiver le bouton
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="bi bi-hourglass-split me-2"></i>Envoi...';

                fetch('<?php echo BASE_URL; ?>includes/process-newsletter.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            showNotification(data.message, 'success');
                            // Réinitialiser le formulaire
                            newsletterForm.reset();
                        } else {
                            showNotification(data.message, 'danger');
                        }
                    })
                    .catch(error => {
                        console.error('Erreur:', error);
                        showNotification('Erreur lors de l\'envoi', 'danger');
                    })
                    .finally(() => {
                        // Réactiver le bouton
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = originalText;
                    });
            });
        }

        // Gestionnaire du formulaire de contact
        const contactForm = document.getElementById('contactForm');

        if (contactForm) {
            contactForm.addEventListener('submit', function(e) {
                e.preventDefault();

                const formData = new FormData(contactForm);
                const submitBtn = contactForm.querySelector('button[type="submit"]');
                const originalText = submitBtn.innerHTML;

                // Désactiver le bouton
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="bi bi-hourglass-split me-2"></i>Envoi...';

                fetch('<?php echo BASE_URL; ?>includes/process-contact.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            showNotification(data.message, 'success');
                            // Réinitialiser le formulaire
                            contactForm.reset();
                        } else {
                            showNotification(data.message, 'danger');
                        }
                    })
                    .catch(error => {
                        console.error('Erreur:', error);
                        showNotification('Erreur lors de l\'envoi', 'danger');
                    })
                    .finally(() => {
                        // Réactiver le bouton
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = originalText;
                    });
            });
        }
    });
</script>