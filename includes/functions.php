<?php
require_once __DIR__ . '/db.php';

function e($v) { return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }

function redirect($path) { header("Location: $path"); exit; }

function csrf_token() {
    if (empty($_SESSION['csrf'])) $_SESSION['csrf'] = bin2hex(random_bytes(32));
    return $_SESSION['csrf'];
}
function csrf_check($token) { return !empty($token) && hash_equals($_SESSION['csrf'] ?? '', $token); }

function flash($key, $msg = null) {
    if ($msg === null) {
        $m = $_SESSION['flash'][$key] ?? null;
        unset($_SESSION['flash'][$key]);
        return $m;
    }
    $_SESSION['flash'][$key] = $msg;
}

function format_price($n) { return '$' . number_format((float)$n); }

function gen_code($len = 6) {
    $code = '';
    for ($i = 0; $i < $len; $i++) $code .= random_int(0, 9);
    return $code;
}

function current_user() {
    if (empty($_SESSION['user_id'])) return null;
    static $u = null;
    if ($u !== null) return $u;
    $stmt = db()->prepare('SELECT * FROM users WHERE id = ?');
    $stmt->execute([$_SESSION['user_id']]);
    $u = $stmt->fetch() ?: null;
    return $u;
}
function require_login()  { if (!current_user()) redirect('login.php'); }
function require_admin()  { $u = current_user(); if (!$u || $u['role'] !== 'admin') redirect('../login.php'); }

function property_main_image($property_id) {
    $stmt = db()->prepare('SELECT image_path FROM property_images WHERE property_id = ? ORDER BY is_primary DESC, id ASC LIMIT 1');
    $stmt->execute([$property_id]);
    $row = $stmt->fetch();
    return $row ? $row['image_path'] : 'assets/images/placeholder.jpg';
}

function property_images($property_id) {
    $stmt = db()->prepare('SELECT * FROM property_images WHERE property_id = ? ORDER BY is_primary DESC, id ASC');
    $stmt->execute([$property_id]);
    return $stmt->fetchAll();
}
