<?php
session_start();
require_once __DIR__ . '/DB/db.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username && $password) {
        $hashedPassword = sha1($password);
        $query = "SELECT * FROM users WHERE usersUsername = ? AND usersPassword = ? LIMIT 1";
        $result = executeQuery($pdo, $query, [$username, $hashedPassword]);
        if ($result && count($result) === 1) {
            $_SESSION['user'] = $result[0]['usersUsername'];
            // Update usersLastLogIn to current date
            $updateQuery = "UPDATE users SET usersLastLogIn = NOW() WHERE usersId = ?";
            executeNonQuery($pdo, $updateQuery, [$result[0]['usersId']]);
            header('Location: admin/index.php');
            exit;
        } else {
            $error = 'Invalid username or password.';
        }
    } else {
        $error = 'Please enter both username and password.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Payday 3 News</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>
    <div class="main-content" style="max-width:400px;margin:40px auto;">
        <div class="news-card">
            <div class="news-card-content">
                <h2 class="news-card-title" style="text-align:center;">Login</h2>
                <?php if ($error): ?>
                    <div style="color:#ff4d4d;text-align:center;margin-bottom:16px;">
                        <?= htmlspecialchars($error) ?>
                    </div>
                <?php endif; ?>
                <form method="post" autocomplete="off">
                    <label for="username">Username</label><br>
                    <input type="text" id="username" name="username" required style="width:100%;padding:10px;margin-bottom:16px;border-radius:4px;border:1px solid #333;background:#232526;color:#f5f5f5;">
                    <br>
                    <label for="password">Password</label><br>
                    <input type="password" id="password" name="password" required style="width:100%;padding:10px;margin-bottom:24px;border-radius:4px;border:1px solid #333;background:#232526;color:#f5f5f5;">
                    <br>
                    <button type="submit" class="button" style="width:100%;">Login</button>
                </form>
            </div>
        </div>
    </div>
    <?php include 'includes/footer.php'; ?>
</body>
</html>