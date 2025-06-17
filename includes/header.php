<?php
// Simple header for Payday 3 News Website

session_start();


?>
<header>
    <h1>Payday 3 News Translator</h1>
    <nav>
        <a href="/index.php">Accueil</a>
        <a href="/news.php">Actualités</a>
        <?php if (isset($_SESSION['user'])): ?>
            <a href="/admin/index.php">Admin</a>
            <a href="/logout.php">Se déconnecter</a>
        <?php else: ?>
        <a href="/login.php">Se connecter</a>
        <?php endif; ?>
    </nav>
</header>
