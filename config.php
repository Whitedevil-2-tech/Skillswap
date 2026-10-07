<?php
// Database connection (PDO) + shared helpers
session_start();

$host = 'localhost'; $db = 'skillswap_db'; $user = 'root'; $pass = '';
try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ]);
} catch (PDOException $ex) {
    die('Database connection failed. Check config.php');
}

function e($s) { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }
function csrf() { return $_SESSION['token'] ??= bin2hex(random_bytes(16)); }
function check_csrf() {
    if (!hash_equals($_SESSION['token'] ?? '', $_POST['token'] ?? '')) { http_response_code(419); die('Invalid CSRF token'); }
}
function require_login() { if (empty($_SESSION['user_id'])) { header('Location: login.php'); exit; } }
function flash($msg = null, $type = 'ok') {
    if ($msg !== null) { $_SESSION['flash'] = [$msg, $type]; return; }
    $f = $_SESSION['flash'] ?? null; unset($_SESSION['flash']); return $f;
}
