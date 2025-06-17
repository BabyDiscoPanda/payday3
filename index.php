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

    <section class="news-card">
        <img src="images/news1.jpg" alt="News Image" class="news-card-image">
        <div class="news-card-content">
            <h3 class="news-card-title">Nouvelle mise à jour disponible !</h3>
            <p class="news-card-summary">
                Découvrez les dernières fonctionnalités et améliorations apportées à PayDay 3 dans notre nouvelle mise à jour.
            </p>
            <a href="news-detail.php?id=1" class="news-card-link">Lire la suite</a>
        </div>
    </section>

    <?php include 'includes/footer.php'; ?>
</body>
</html>