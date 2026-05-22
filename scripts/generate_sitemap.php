<?php
require_once __DIR__ . '/../includes/articles-db.php';
require_once __DIR__ . '/../includes/config.php';

$articles = getArticles();
$base = rtrim(BASE_URL, '/');
$items = [];
$items[] = ['loc' => $base . '/', 'priority' => '1.0'];
$items[] = ['loc' => $base . '/articles.php', 'priority' => '0.8'];

foreach ($articles as $a) {
    $slug = $a['slug'] ?? null;
    if (!$slug) continue;
    $loc = $base . '/article-detail.php?slug=' . urlencode($slug);
    $lastmod = null;
    if (isset($a['date_published'])) {
        try {
            $d = new DateTime($a['date_published']);
            $lastmod = $d->format('Y-m-d');
        } catch (Exception $e) {
        }
    }
    $items[] = ['loc' => $loc, 'lastmod' => $lastmod, 'priority' => '0.6'];
}

$xml = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><urlset/>');
$xml->addAttribute('xmlns', 'http://www.sitemaps.org/schemas/sitemap/0.9');

foreach ($items as $it) {
    $url = $xml->addChild('url');
    $url->addChild('loc', htmlspecialchars($it['loc'], ENT_XML1 | ENT_COMPAT, 'UTF-8'));
    if (!empty($it['lastmod'])) $url->addChild('lastmod', $it['lastmod']);
    $url->addChild('changefreq', 'weekly');
    $url->addChild('priority', $it['priority']);
}

// Écrire sitemap.xml à la racine
$file = __DIR__ . '/../sitemap.xml';
$xmlStr = $xml->asXML();
if (file_put_contents($file, $xmlStr)) {
    echo "sitemap.xml généré: $file\n";
} else {
    echo "Échec écriture sitemap.xml\n";
}
