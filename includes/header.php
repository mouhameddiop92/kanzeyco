<?php require_once __DIR__ . '/config.php'; ?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php
    // Titre et description dynamiques pour SEO
    $siteName = 'KANZEYCO';
    $defaultTitle = "$siteName - Transformons l'Afrique par le digital";
    $titleTag = isset($pageTitle) && $pageTitle ? ($pageTitle . ' - ' . $siteName) : $defaultTitle;
    $metaDescription = $metaDescription ?? ($siteName . ' — Articles, solutions digitales et conseils pour entreprises en Afrique.');
    $metaImage = $metaImage ?? (BASE_URL . 'assets/images/marqueting.avif');
    $canonical = isset($canonical) ? $canonical : (isset($_SERVER['REQUEST_URI']) ? rtrim(BASE_URL, '/') . $_SERVER['REQUEST_URI'] : BASE_URL);
    ?>
    <title><?php echo htmlspecialchars($titleTag); ?></title>

    <meta name="description" content="<?php echo htmlspecialchars($metaDescription); ?>">
    <link rel="canonical" href="<?php echo htmlspecialchars($canonical); ?>">
    <meta name="robots" content="index,follow">

    <!-- Open Graph / Facebook -->
    <meta property="og:locale" content="fr_FR">
    <meta property="og:site_name" content="<?php echo htmlspecialchars($siteName); ?>">
    <meta property="og:title" content="<?php echo htmlspecialchars($titleTag); ?>">
    <meta property="og:description" content="<?php echo htmlspecialchars($metaDescription); ?>">
    <meta property="og:type" content="article">
    <meta property="og:url" content="<?php echo htmlspecialchars($canonical); ?>">
    <meta property="og:image" content="<?php echo htmlspecialchars($metaImage); ?>">

    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?php echo htmlspecialchars($titleTag); ?>">
    <meta name="twitter:description" content="<?php echo htmlspecialchars($metaDescription); ?>">
    <meta name="twitter:image" content="<?php echo htmlspecialchars($metaImage); ?>">

    <!-- JSON-LD Organization -->
    <script type="application/ld+json">
        <?php echo json_encode([
            '@context' => 'https://schema.org',
            '@type' => 'Organization',
            'name' => $siteName,
            'url' => rtrim(BASE_URL, '/'),
            'logo' => BASE_URL . 'assets/images/Logo%20Kanzey%20Co.png',
            'sameAs' => []
        ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT); ?>
    </script>
    <?php if (isset($articleJsonLd) && $articleJsonLd): ?>
        <script type="application/ld+json">
            <?php echo $articleJsonLd; ?>
        </script>
    <?php endif; ?>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/style.css?v=1.1">
</head>

<body>
    <!-- Navigation Bar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm sticky-top">
        <div class="container">
            <a class="navbar-brand" href="<?php echo BASE_URL; ?>" id="logoLink">
                <div class="logo" id="siteLogo">
                    <img src="<?php echo BASE_URL; ?>assets/images/Logo%20Kanzey%20Co.png" alt="KANZEYCO - Solutions digitales" width="100" height="100" loading="eager" decoding="async" style="width: 100px; height: 100px; object-fit: contain;">
                </div>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
                <ul class="navbar-nav align-items-lg-center me-lg-3">
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo BASE_URL; ?>index.php#accueil">Accueil</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo BASE_URL; ?>index.php#solutions">Solutions</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo BASE_URL; ?>index.php#services">Services</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo BASE_URL; ?>index.php#actualites">Blog</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo BASE_URL; ?>index.php#apropos">À propos</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo BASE_URL; ?>index.php#contact">Contact</a>
                    </li>
                </ul>
                <a href="<?php echo BASE_URL; ?>index.php#contact" class="btn btn-primary btn-login">Demander une démo</a>
            </div>
        </div>
    </nav>