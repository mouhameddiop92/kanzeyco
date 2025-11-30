<?php
$pageTitle = "Gestion des articles";
require_once 'includes/admin-header.php';
require_once '../includes/articles-db.php';
// Initialisation centralisée
$action = isset($_GET['action']) ? $_GET['action'] : 'list';
$slug = isset($_GET['slug']) ? $_GET['slug'] : '';
$articles = getArticles(null, null);
$currentArticle = null;
if ($action === 'edit' && $slug) {
    $currentArticle = getArticleBySlug($slug);
}

$imageError = '';
$successMsg = '';

// Traitement d'ajout ou modification réelle
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupère tous les champs
    $title = $_POST['title'] ?? '';
    $excerpt = $_POST['excerpt'] ?? '';
    $contentText = $_POST['content'] ?? '';
    $category = $_POST['category'] ?? '';
    $date = $_POST['date'] ?? date('Y-m-d');
    $author = $_POST['author'] ?? '';
    $status = $_POST['status'] ?? 'published';

    // Gestion image uploadée
    $imagePath = '';
    if ($action === 'edit' && $currentArticle) {
        $imagePath = $currentArticle['image']; // garder l'ancienne si pas de nouvel upload
    }
    if (!empty($_FILES['image']['tmp_name'])) {
        $allowed = ['jpg','jpeg','png','gif','webp'];
        $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, $allowed)) {
            $imageError = 'Format d\'image non autorisé (' . htmlspecialchars($ext) . ')';
        } elseif ($_FILES['image']['size'] > 3*1024*1024) { // 3Mo
            $imageError = 'Image trop volumineuse (max 3Mo)';
        } else {
            $uploadsDir = realpath(__DIR__ . '/../uploads') ?: __DIR__ . '/../uploads';
            $baseName = uniqid('art_', true) . '.' . $ext;
            $targetPath = $uploadsDir . '/' . $baseName;
            if (move_uploaded_file($_FILES['image']['tmp_name'], $targetPath)) {
                $imagePath = 'uploads/' . $baseName;
            } else {
                $imageError = 'Échec de l\'upload de l\'image';
            }
        }
    }
    // Construction du contenu (JSON)
    $content = [];
    foreach (explode("\n\n", $contentText) as $block) {
        $block = trim($block);
        if ($block) { $content[] = [ 'type'=>'paragraph', 'text'=>$block ]; }
    }
    $slugNew = strtolower(trim(preg_replace('/[^a-z0-9]+/i','-', $title), '-'));

    // Insertion/Update (simulation d'abord, puis vrai code plus bas)
    if (!$imageError) {
        $pdo = getDBConnection();
        if ($pdo) {
            if ($action === 'edit' && $currentArticle) {
                // UPDATE
                $sql = "UPDATE articles SET title=?, excerpt=?, content=?, category=?, date_published=?, author=?, image=?, status=? WHERE slug=?";
                $pdo->prepare($sql)->execute([
                    $title,
                    $excerpt,
                    json_encode($content, JSON_UNESCAPED_UNICODE),
                    $category,
                    $date,
                    $author,
                    $imagePath,
                    $status,
                    $currentArticle['slug']
                ]);
                $successMsg = 'Article mis à jour';
            } else {
                // INSERT
                $sql = "INSERT INTO articles (slug,title,excerpt,content,category,date_published,author,image,status) VALUES (?,?,?,?,?,?,?,?,?)";
                $pdo->prepare($sql)->execute([
                    $slugNew,
                    $title,
                    $excerpt,
                    json_encode($content, JSON_UNESCAPED_UNICODE),
                    $category,
                    $date,
                    $author,
                    $imagePath,
                    $status
                ]);
                $successMsg = 'Article ajouté';
            }
            $action = 'list';
            $articles = getArticles();
        }
    }
}
?>

<div class="page-header">
    <h1 class="page-title">Gestion des articles</h1>
    <div class="page-actions">
        <a href="?action=new" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Nouvel article
        </a>
    </div>
</div>

<?php if ($action === 'list'): ?>
    <!-- Liste des articles -->
    <div class="card">
        <div class="card-header">
            <h5 class="card-title">Tous les articles (<?php echo count($articles); ?>)</h5>
            <div class="card-search">
                <input type="text" class="form-control" id="searchArticles" placeholder="Rechercher un article...">
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover" id="articlesTable">
                    <thead>
                        <tr>
                            <th>Titre</th>
                            <th>Catégorie</th>
                            <th>Auteur</th>
                            <th>Date</th>
                            <th>Statut</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($articles as $article): ?>
                        <tr>
                            <td>
                                <div class="article-title-cell">
                                    <strong><?php echo htmlspecialchars($article['title']); ?></strong>
                                    <small class="text-muted d-block"><?php echo htmlspecialchars($article['excerpt']); ?></small>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-secondary"><?php echo htmlspecialchars($article['category']); ?></span>
                            </td>
                            <td><?php echo htmlspecialchars($article['author']); ?></td>
                            <td><?php echo htmlspecialchars($article['date']); ?></td>
                            <td>
                                <span class="badge bg-success">Publié</span>
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <a href="../article-detail.php?slug=<?php echo $article['slug']; ?>" target="_blank" class="btn-icon" title="Voir">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="?action=edit&slug=<?php echo $article['slug']; ?>" class="btn-icon" title="Modifier">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button class="btn-icon btn-icon-danger" title="Supprimer" onclick="deleteArticle('<?php echo $article['slug']; ?>')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

<?php elseif ($action === 'new' || $action === 'edit'): ?>
    <!-- Formulaire d'ajout/modification -->
    <?php
    // $currentArticle = null; // This line is now handled by the new_code
    // if ($action === 'edit' && $slug) {
    //     foreach ($articles as $article) {
    //         if ($article['slug'] === $slug) {
    //             $currentArticle = $article;
    //             break;
    //         }
    //     }
    // }
    ?>
    
    <div class="card">
        <div class="card-header">
            <h5 class="card-title">
                <?php echo $action === 'new' ? 'Nouvel article' : 'Modifier l\'article'; ?>
            </h5>
            <a href="articles.php" class="btn-link">
                <i class="fas fa-arrow-left me-2"></i>Retour à la liste
            </a>
        </div>
        <div class="card-body">
            <form method="POST" action="articles.php" id="articleForm" enctype="multipart/form-data">
                <div class="row">
                    <div class="col-md-8">
                        <div class="form-group mb-3">
                            <label for="title" class="form-label">Titre *</label>
                            <input type="text" class="form-control" id="title" name="title" 
                                   value="<?php echo $currentArticle ? htmlspecialchars($currentArticle['title']) : ''; ?>" required>
                        </div>
                        
                        <div class="form-group mb-3">
                            <label for="excerpt" class="form-label">Extrait *</label>
                            <textarea class="form-control" id="excerpt" name="excerpt" rows="3" required><?php echo $currentArticle ? htmlspecialchars($currentArticle['excerpt']) : ''; ?></textarea>
                        </div>
                        
                        <div class="form-group mb-3">
                            <label for="content" class="form-label">Contenu *</label>
                            <textarea class="form-control" id="content" name="content" rows="10" required><?php 
                                if ($currentArticle && isset($currentArticle['content'])) {
                                    $contentText = '';
                                    foreach ($currentArticle['content'] as $block) {
                                        if ($block['type'] === 'paragraph') {
                                            $contentText .= $block['text'] . "\n\n";
                                        }
                                    }
                                    echo htmlspecialchars($contentText);
                                }
                            ?></textarea>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="form-group mb-3">
                            <label for="category" class="form-label">Catégorie *</label>
                            <select class="form-select" id="category" name="category" required>
                                <option value="">Sélectionner...</option>
                                <option value="Événementiel" <?php echo ($currentArticle && $currentArticle['category'] === 'Événementiel') ? 'selected' : ''; ?>>Événementiel</option>
                                <option value="Immobilier" <?php echo ($currentArticle && $currentArticle['category'] === 'Immobilier') ? 'selected' : ''; ?>>Immobilier</option>
                                <option value="Innovation" <?php echo ($currentArticle && $currentArticle['category'] === 'Innovation') ? 'selected' : ''; ?>>Innovation</option>
                                <option value="Marketing" <?php echo ($currentArticle && $currentArticle['category'] === 'Marketing') ? 'selected' : ''; ?>>Marketing</option>
                                <option value="Analytics" <?php echo ($currentArticle && $currentArticle['category'] === 'Analytics') ? 'selected' : ''; ?>>Analytics</option>
                                <option value="Success Story" <?php echo ($currentArticle && $currentArticle['category'] === 'Success Story') ? 'selected' : ''; ?>>Success Story</option>
                            </select>
                        </div>
                        
                        <div class="form-group mb-3">
                            <label for="date" class="form-label">Date de publication *</label>
                            <input type="date" class="form-control" id="date" name="date" 
                                   value="<?php echo $currentArticle ? date('Y-m-d', strtotime($currentArticle['date'])) : date('Y-m-d'); ?>" required>
                        </div>
                        
                        <div class="form-group mb-3">
                            <label for="author" class="form-label">Auteur *</label>
                            <input type="text" class="form-control" id="author" name="author" 
                                   value="<?php echo $currentArticle ? htmlspecialchars($currentArticle['author']) : 'Equipe Kanzey.co'; ?>" required>
                        </div>
                        
                        <div class="form-group mb-3">
                            <label for="image" class="form-label">URL de l'image</label>
                            <input type="file" class="form-control" id="image" name="image">
                            <?php if ($imageError): ?>
                                <div class="text-danger mt-2"><?php echo $imageError; ?></div>
                            <?php endif; ?>
                            <?php if ($successMsg): ?>
                                <div class="text-success mt-2"><?php echo $successMsg; ?></div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="form-group mb-3">
                            <label for="status" class="form-label">Statut</label>
                            <select class="form-select" id="status" name="status">
                                <option value="published" selected>Publié</option>
                                <option value="draft">Brouillon</option>
                            </select>
                        </div>
                    </div>
                </div>
                
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Enregistrer
                    </button>
                    <a href="articles.php" class="btn btn-secondary">Annuler</a>
                </div>
            </form>
        </div>
    </div>
<?php endif; ?>

<script>
// Recherche dans le tableau
document.getElementById('searchArticles')?.addEventListener('input', function(e) {
    const searchTerm = e.target.value.toLowerCase();
    const rows = document.querySelectorAll('#articlesTable tbody tr');
    
    rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        row.style.display = text.includes(searchTerm) ? '' : 'none';
    });
});

// Fonction de suppression
function deleteArticle(slug) {
    if (confirm('Êtes-vous sûr de vouloir supprimer cet article ?')) {
        // Ici, ajouter la logique de suppression
        alert('Article supprimé (simulation)');
        // window.location.href = 'articles.php?action=delete&slug=' + slug;
    }
}
</script>

<?php require_once 'includes/admin-footer.php'; ?>

