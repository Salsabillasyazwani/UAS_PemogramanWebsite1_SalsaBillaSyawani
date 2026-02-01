<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../API/connection/koneksi.php';
if (isset($_SESSION['is_logged_in']) && $_SESSION['is_logged_in'] === true) {
    $isLogin = true;
    if (!defined('AUTH_ID')) {
        define('AUTH_ID', $_SESSION['user_id']);
    }
    return; 
}
if (!isset($_COOKIE['auth_token'])) {
    $isLogin = false;
    return;
}

$hash = hash('sha256', $_COOKIE['auth_token']);

$stmt = $database_connection->prepare(
    "SELECT ID_user, username FROM users WHERE token=?"
);
$stmt->execute([$hash]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if ($user) {
    $_SESSION['user_id'] = $user['ID_user'];
    $_SESSION['username'] = $user['username'];
    $_SESSION['is_logged_in'] = true;

    $isLogin = true;
    if (!defined('AUTH_ID')) {
        define('AUTH_ID', $user['ID_user']);
    }
} else {
    $isLogin = false;
}