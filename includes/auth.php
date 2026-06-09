<?php
require_once __DIR__ . '/functions.php';
require_once __DIR__ . '/mailer.php';

function register_user($name, $email, $password) {
    $name = trim($name); $email = trim(strtolower($email));
    if ($name === '' || $email === '' || $password === '') return [false, 'All fields are required.'];
    if (!filter_var($email, FILTER_VALIDATE_EMAIL))         return [false, 'Invalid email address.'];
    if (strlen($password) < 8)                              return [false, 'Password must be at least 8 characters.'];

    $stmt = db()->prepare('SELECT id FROM users WHERE email = ?');
    $stmt->execute([$email]);
    if ($stmt->fetch()) return [false, 'An account with that email already exists.'];

    $hash = password_hash($password, PASSWORD_BCRYPT);
    $stmt = db()->prepare('INSERT INTO users (name, email, password, role, is_verified) VALUES (?, ?, ?, "user", 0)');
    $stmt->execute([$name, $email, $hash]);
    $uid = (int)db()->lastInsertId();

    $code = gen_code(6);
    $stmt = db()->prepare('INSERT INTO email_verifications (user_id, code, expires_at) VALUES (?, ?, DATE_ADD(NOW(), INTERVAL 30 MINUTE))');
    $stmt->execute([$uid, $code]);

    send_mail($email, 'Verify your email — Billah Dee King',
        "<p>Welcome to Billah Dee King.</p><p>Your verification code is:</p><h2 style='letter-spacing:6px;color:#bd9941'>$code</h2><p>This code expires in 30 minutes.</p>");

    $_SESSION['pending_verify_user'] = $uid;
    return [true, 'Account created. Check your email for the verification code.'];
}

function verify_email_code($user_id, $code) {
    $stmt = db()->prepare('SELECT * FROM email_verifications WHERE user_id = ? AND code = ? AND expires_at > NOW() ORDER BY id DESC LIMIT 1');
    $stmt->execute([$user_id, trim($code)]);
    $row = $stmt->fetch();
    if (!$row) return [false, 'Invalid or expired code.'];
    db()->prepare('UPDATE users SET is_verified = 1 WHERE id = ?')->execute([$user_id]);
    db()->prepare('DELETE FROM email_verifications WHERE user_id = ?')->execute([$user_id]);
    return [true, 'Email verified. You can now log in.'];
}

function login_user($email, $password) {
    $email = trim(strtolower($email));
    if ($email === '' || $password === '') return [false, 'Email and password are required.'];
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) return [false, 'Invalid email address.'];
    $stmt = db()->prepare('SELECT * FROM users WHERE email = ?');
    $stmt->execute([$email]);
    $u = $stmt->fetch();
    if (!$u || !password_verify($password, $u['password'])) return [false, 'Incorrect email or password.'];
    if (!$u['is_verified']) { $_SESSION['pending_verify_user'] = $u['id']; return [false, 'Please verify your email before logging in.']; }
    $_SESSION['user_id'] = $u['id'];
    return [true, 'Welcome back.'];
}

function send_password_reset($email) {
    $email = trim(strtolower($email));
    $stmt = db()->prepare('SELECT id FROM users WHERE email = ?');
    $stmt->execute([$email]);
    $u = $stmt->fetch();
    if (!$u) return [true, 'If that email exists, a reset code was sent.']; // do not leak
    $code = gen_code(6);
    db()->prepare('INSERT INTO password_resets (user_id, code, expires_at) VALUES (?, ?, DATE_ADD(NOW(), INTERVAL 30 MINUTE))')
        ->execute([$u['id'], $code]);
    send_mail($email, 'Password reset code — Billah Dee King',
        "<p>Your password reset code is:</p><h2 style='letter-spacing:6px;color:#bd9941'>$code</h2><p>This code expires in 30 minutes.</p>");
    return [true, 'If that email exists, a reset code was sent.'];
}

function reset_password($email, $code, $new_password) {
    if (strlen($new_password) < 8) return [false, 'Password must be at least 8 characters.'];
    $email = trim(strtolower($email));
    $stmt = db()->prepare('SELECT u.id FROM users u JOIN password_resets r ON r.user_id = u.id WHERE u.email = ? AND r.code = ? AND r.expires_at > NOW() ORDER BY r.id DESC LIMIT 1');
    $stmt->execute([$email, trim($code)]);
    $u = $stmt->fetch();
    if (!$u) return [false, 'Invalid or expired code.'];
    $hash = password_hash($new_password, PASSWORD_BCRYPT);
    db()->prepare('UPDATE users SET password = ? WHERE id = ?')->execute([$hash, $u['id']]);
    db()->prepare('DELETE FROM password_resets WHERE user_id = ?')->execute([$u['id']]);
    return [true, 'Password updated. You can now log in.'];
}
