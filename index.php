<?php
session_start();
require_once __DIR__ . '/DB/db.php';
// Fetch the latest news from the database
$query = "SELECT * FROM news ORDER BY newsDate DESC LIMIT 5";
$newsItems = executeQuery($pdo, $query);

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon Application</title>
    <link rel="stylesheet" href="css/style.css">
    <script src="js/script.js" defer></script>
    <link rel="icon" href="favicon.ico" type="image/x-icon">
</head>
<body>
    <?php include 'includes/header.php'; ?>
    <main>
        <h2>Contenu Principal</h2>
        <p>Ceci est un exemple de page d'accueil.</p>
    </main>
    <div class="main-content" style="max-width:400px;margin:40px auto;">
        <?php if (count($newsItems) > 0): ?>
            <?php foreach ($newsItems as $news): ?>
                <div class="news-card" href="/<?= $news['newsURL'] ?>">
                    <div class="news-card-content">
                        <h2 class="news-card-title" style="text-align:center;">Dernières Actualités</h2>
                        <div class="news-item">
                            <h3 class="news-item-title"><?= htmlspecialchars($news['newsTitle']) ?></h3>
                            <p class="news-item-date"><?= date('d M Y', strtotime($news['newsDate'])) ?></p>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="news-card">
                <div class="news-card-content">
                    <h2 class="news-card-title" style="text-align:center;">Aucune Actualité</h2>
                </div>
            </div>
        <?php endif; ?>
    </div>
    <?php include 'includes/footer.php'; ?>
</body>
</html>