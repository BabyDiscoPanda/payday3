<?php

session_start();


?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Actualités</title>
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>
    <main>
        <h1>Actualités</h1>
        <p>Bienvenue sur la page des actualités !</p>
        <p>Restez à jour avec les dernières nouvelles et mises à jour.</p>
        <p>Toute les actualités sont disponibles sur <a href="https://www.example.com">le site officiel</a>.</p>
    </main>

    <?php
    
    // fetch news from the database and display them by descending order of date and 3 lines 3 columns
    require_once 'DB/db.php'; // Assumes you have a db.php for PDO connection

    // Pagination setup
    $newsPerPage = 9;
    $page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
    $offset = ($page - 1) * $newsPerPage;

    // Count total news
    $stmt = $pdo->query("SELECT COUNT(*) FROM news");
    $totalNews = $stmt->fetchColumn();
    $totalPages = ceil($totalNews / $newsPerPage);

    // Fetch news
    $stmt = $pdo->prepare("SELECT newsId, newsTitle, newsPrimaryImg, newsSummary, newsDate, newsURL FROM news ORDER BY newsDate DESC LIMIT :limit OFFSET :offset");
    $stmt->bindValue(':limit', $newsPerPage, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    $newsList = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo '<div class="news-grid" style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px;">';
    foreach ($newsList as $news) {
        $newsUrl = isset($news['newsURL']) ? $news['newsURL']: '#';
        echo '<a href="' . htmlspecialchars($newsUrl) . '" style="text-decoration:none;color:inherit;">';
        echo '<div class="news-box" style="border:1px solid #ccc; padding:16px; border-radius:8px; background:#fff; transition:box-shadow 0.2s;">';
        if (!empty($news['newsPrimaryImg'])) {
            echo '<img src="' . htmlspecialchars($news['newsPrimaryImg']) . '" alt="' . htmlspecialchars($news['newsTitle']) . '" style="width:100%;height:150px;object-fit:cover;border-radius:4px;">';
        }
        echo '<h2 style="font-size:1.2em;margin:12px 0 8px 0;">' . htmlspecialchars($news['newsTitle']) . '</h2>';
        echo '<p style="font-size:0.95em;color:#555;">' . htmlspecialchars($news['newsSummary']) . '</p>';
        echo '<span style="font-size:0.85em;color:#888;">' . date('d/m/Y', strtotime($news['newsDate'])) . '</span>';
        echo '</div>';
        echo '</a>';
    }
    echo '</div>';

    // Pagination links
    if ($totalPages > 1) {
        echo '<div class="pagination" style="margin:24px 0;text-align:center;">';
        for ($i = 1; $i <= $totalPages; $i++) {
            if ($i == $page) {
                echo '<span style="margin:0 6px;font-weight:bold;">' . $i . '</span>';
            } else {
                echo '<a href="?page=' . $i . '" style="margin:0 6px;text-decoration:none;color:#007bff;">' . $i . '</a>';
            }
        }
        echo '</div>';
    }
    
    ?>

    <?php include 'includes/footer.php'; ?>
</body>
</html>
