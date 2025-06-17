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
    $summary = trim($_POST['summary'] ?? '');
    $content = trim($_POST['content'] ?? '');
    $images = $_FILES['images'] ?? null;
    $imagePaths = [];
    $primaryImg = '';
    $subtitleImages = [];

    if ($title && $date && $url && $summary && $content) {
        // Handle image uploads
        if ($images && isset($images['name']) && is_array($images['name'])) {
            $uploadDir = __DIR__ . '/../news_images/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            for ($i = 0; $i < count($images['name']); $i++) {
                if ($images['error'][$i] === UPLOAD_ERR_OK) {
                    $tmpName = $images['tmp_name'][$i];
                    $baseName = basename($images['name'][$i]);
                    $targetPath = $uploadDir . uniqid('img_') . '_' . $baseName;
                    if (move_uploaded_file($tmpName, $targetPath)) {
                        $imagePaths[] = 'news_images/' . basename($targetPath);
                    }
                }
            }
            // Set primary image if at least one image uploaded
            if (!empty($imagePaths)) {
                $primaryImg = $imagePaths[0];
            }
        }
        // Save content as a PHP file in /news/ directory
        $filename = basename($url);
        if (!preg_match('/^[a-zA-Z0-9_-]+$/', $filename)) {
            $error = 'URL must be a valid filename (letters, numbers, - or _).';
        } else {
            // Insert news to get the ID
            $query = "INSERT INTO news (newsTitle, newsDate, newsURL, newsSummary, newsPrimaryImg) VALUES (?, ?, ?, ?, ?)";
            $result = executeNonQuery($pdo, $query, [$title, $date, 'news/' . $filename . '.php', $summary, $primaryImg]);
            if ($result) {
                $newsId = getLastInsertId($pdo);
                $filepath = __DIR__ . '/../news/' . $filename . '.php';
                $phpContent = "<?php\nsession_start();\n\$newsId = $newsId;\n?>\n";
                $phpContent .= "<!DOCTYPE html>\n";
                $phpContent .= "<html lang='en'>\n<head>\n";
                $phpContent .= "    <meta charset='UTF-8'>\n";
                $phpContent .= "    <meta name='viewport' content='width=device-width, initial-scale=1.0'>\n";
                $phpContent .= "    <title>" . htmlspecialchars($title) . "</title>\n";
                $phpContent .= "    <link rel='stylesheet' href='/css/style.css'>\n";
                $phpContent .= "</head>\n<body>\n";
                $phpContent .= "<?php include __DIR__ . '/../includes/header.php'; ?>\n";
                $phpContent .= "<div class='main-content' style='max-width:800px;margin:40px auto;'>\n";
                // Main title
                $phpContent .= "<h1>" . htmlspecialchars($title) . "</h1>\n";
                // Add images to the top
                if (!empty($imagePaths)) {
                    $imgHtml = "<div class='news-images'>";
                    if($imagePaths!=null && count($imagePaths)>0) {
                    $imgHtml .= "<img src='/" . htmlspecialchars($imagePaths[0]) . "' style='max-width:100%;margin-bottom:10px;' />";
                    }
                    $imgHtml .= "</div>\n";
                    $phpContent .= $imgHtml;
                }
                // Content with subtitle images
                $phpContent .= customMarkdownToHtmlWithSubtitleImages($content, $imagePaths);
                $phpContent .= "<p class='news-item-date'>" . date('d M Y', strtotime($date)) . "</p>\n";
                $phpContent .= "</div>\n";
                // Footer include
                $phpContent .= "<?php include __DIR__ . '/../includes/footer.php'; ?>\n";
                $phpContent .= "</body>\n</html>\n";
                // Write to file
                if (file_put_contents($filepath, $phpContent) !== false) {
                    $success = 'Event added successfully!';
                } else {
                    $error = 'Failed to write news file.';
                }
            } else {
                $error = 'Failed to add event to database.';
            }
        }
    } else {
        $error = 'Please fill in all fields.';
    }
}

function customMarkdownToHtmlWithSubtitleImages($text, $imagePaths) {
    // <subtitle>...</subtitle> => <h2>...</h2> (with optional image below if [img] is present)
    $imgIndex = 1;
    $text = preg_replace_callback('/<subtitle>(.*?)<\/subtitle>/is', function($matches) use (&$imgIndex, $imagePaths) {
        $html = '<h2>' . htmlspecialchars($matches[1]) . '</h2>';
        // If [img] is present after subtitle, insert image
        if (isset($imagePaths[$imgIndex])) {
            $html .= "<img src='/" . htmlspecialchars($imagePaths[$imgIndex]) . "' style='max-width:100%;margin-bottom:10px;' />";
            $imgIndex++;
        }
        return $html;
    }, $text);
    // <p>...</p> => <p>...</p>
    $text = preg_replace('/<p>(.*?)<\/p>/is', '<p>$1</p>', $text);
    // <list>...</list> => <ul>...</ul>
    $text = preg_replace('/<list>(.*?)<\/list>/is', '<ul>$1</ul>', $text);
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
    <div class="main-content" style="max-width:600px;margin:40px auto;">
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
        <form method="post" autocomplete="off" enctype="multipart/form-data" style="margin:0 auto;max-width:500px;display:flex;flex-direction:column;align-items:center;">
            <label for="title" style="align-self:flex-start;">Title</label><br>
            <input type="text" id="title" name="title" required style="width:100%;padding:10px;margin-bottom:16px;border-radius:4px;border:1px solid #333;background:#232526;color:#f5f5f5;">
            <br>
            <label for="date" style="align-self:flex-start;">Date</label><br>
            <input type="date" id="date" name="date" value="<?= date('Y-m-d') ?>" required style="width:100%;padding:10px;margin-bottom:16px;border-radius:4px;border:1px solid #333;background:#232526;color:#f5f5f5;">
            <br>
            <label for="url" style="align-self:flex-start;">URL (filename, no extension)</label><br>
            <input type="text" id="url" name="url" required style="width:100%;padding:10px;margin-bottom:16px;border-radius:4px;border:1px solid #333;background:#232526;color:#f5f5f5;">
            <br>
            <label for="summary" style="align-self:flex-start;">Summary</label><br>
            <textarea id="summary" name="summary" rows="3" required style="width:100%;padding:10px;margin-bottom:16px;border-radius:4px;border:1px solid #333;background:#232526;color:#f5f5f5;"></textarea>
            <br>
            <label for="content" style="align-self:flex-start;">Content (custom markdown)</label><br>
            <textarea id="content" name="content" rows="10" required style="width:100%;padding:10px;margin-bottom:24px;border-radius:4px;border:1px solid #333;background:#232526;color:#f5f5f5;"></textarea>
            <br>
            <label for="images" style="align-self:flex-start;">Images (optional, you can select multiple):</label><br>
            <input type="file" id="images" name="images[]" multiple accept="image/*" style="margin-bottom:16px;width:100%;">
            <br>
            <button type="submit" class="button" style="width:100%;">Add Event</button>
        </form>
    </div>
    <?php include '../includes/footer.php'; ?>
</body>
</html>
