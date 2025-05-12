<?php
session_start();

$host = 'localhost';
$dbname = 'tour_db';
$username = 'root';
$password = 'root';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
} catch (PDOException $e) {
    die(json_encode(['status' => 'error', 'message' => 'Ошибка подключения к БД']));
}

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function getUserName() {
    return $_SESSION['username'] ?? 'Гость';
}

if (isset($_SESSION['user_id'])) {
    $stmt = $pdo->prepare("SELECT is_blocked, block_reason FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch();
    
    if ($user && $user['is_blocked']) {
        session_unset();
        session_destroy();
        $_SESSION['block_message'] = "Ваш аккаунт заблокирован. Причина: " . htmlspecialchars($user['block_reason']);
        header('Location: ../index.php');
        exit;
    }
}

?>
