<?php
session_start();
require_once __DIR__ . '/../DB/db.php';

// Optional: Only allow logged-in users (admin)
if (!isset($_SESSION['user'])) {
    header('Location: /login.php');
    exit;
}

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $date = $_POST['date'] ?? date('Y-m-d');
    $url = trim($_POST['url'] ?? '');
    $content = trim($_POST['content'] ?? '');
    
    if ($title && $date && $url && $content) {
        // Save content as a PHP file in /news/ directory
        $filename = basename($url);
        if (!preg_match('/^[a-zA-Z0-9_-]+$/', $filename)) {
            $error = 'URL must be a valid filename (letters, numbers, - or _).';
        } else {
            $filepath = __DIR__ . '/../news/' . $filename . '.php';
            $phpContent = "<?php\n?>\n" . customMarkdownToHtml($content);
            if (file_put_contents($filepath, $phpContent) !== false) {
                $query = "INSERT INTO news (newsTitle, newsDate, newsURL) VALUES (?, ?, ?)";
                $result = executeNonQuery($pdo, $query, [$title, $date, 'news/' . $filename . '.php']);
                if ($result) {
                    $success = 'Event added successfully!';
                } else {
                    $error = 'Failed to add event to database.';
                }
            } else {
                $error = 'Failed to write news file.';
            }
        }
    } else {
        $error = 'Please fill in all fields.';
    }
}

function customMarkdownToHtml($text) {
    // <title>...</title> => <h1>...</h1>
    $text = preg_replace('/<title>(.*?)<\/title>/is', '<h1>$1</h1>', $text);
    // <p>...</p> => <p>...</p>
    $text = preg_replace('/<p>(.*?)<\/p>/is', '<p>$1</p>', $text);
    // <list>...</list> => <ul>...</ul>
    $text = preg_replace('/<list>(.*?)<\/list>/is', '<ul>$1</ul>', $text);
    // <item>...</item> => <li>...</li>
    $text = preg_replace('/<item>(.*?)<\/item>/is', '<li>$1</li>', $text);
    return $text;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Add Event</title>
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>
    <?php include '../includes/header.php'; ?>
    <div class="main-content" style="max-width:500px;margin:40px auto;">
        <div class="news-card">
            <div class="news-card-content">
                <h2 class="news-card-title" style="text-align:center;">Add New Event</h2>
                <?php if ($success): ?>
                    <div style="color:#51ac07;text-align:center;margin-bottom:16px;">
                        <?= htmlspecialchars($success) ?>
                    </div>
                <?php elseif ($error): ?>
                    <div style="color:#ff4d4d;text-align:center;margin-bottom:16px;">
                        <?= htmlspecialchars($error) ?>
                    </div>
                <?php endif; ?>
                <form method="post" autocomplete="off">
                    <label for="title">Title</label><br>
                    <input type="text" id="title" name="title" required style="width:100%;padding:10px;margin-bottom:16px;border-radius:4px;border:1px solid #333;background:#232526;color:#f5f5f5;">
                    <br>
                    <label for="date">Date</label><br>
                    <input type="date" id="date" name="date" value="<?= date('Y-m-d') ?>" required style="width:100%;padding:10px;margin-bottom:16px;border-radius:4px;border:1px solid #333;background:#232526;color:#f5f5f5;">
                    <br>
                    <label for="url">URL (filename, no extension)</label><br>
                    <input type="text" id="url" name="url" required style="width:100%;padding:10px;margin-bottom:16px;border-radius:4px;border:1px solid #333;background:#232526;color:#f5f5f5;">
                    <br>
                    <label for="content">Content (custom markdown)</label><br>
                    <textarea id="content" name="content" rows="10" required style="width:100%;padding:10px;margin-bottom:24px;border-radius:4px;border:1px solid #333;background:#232526;color:#f5f5f5;"></textarea>
                    <br>
                    <button type="submit" class="button" style="width:100%;">Add Event</button>
                </form>
            </div>
        </div>
    </div>
    <?php include '../includes/footer.php'; ?>
</body>
</html>
