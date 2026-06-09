<?php
require_once __DIR__ . '/config.php';

// Load PHPMailer either from Composer or from local includes/PHPMailer/src
$autoload = __DIR__ . '/../vendor/autoload.php';
if (file_exists($autoload)) {
    require_once $autoload;
} else {
    $base = __DIR__ . '/PHPMailer/src/';
    if (file_exists($base . 'PHPMailer.php')) {
        require_once $base . 'Exception.php';
        require_once $base . 'PHPMailer.php';
        require_once $base . 'SMTP.php';
    }
}

function send_mail($to, $subject, $html_body, $alt = '') {
    if (!class_exists('PHPMailer\\PHPMailer\\PHPMailer')) {
        // Fallback: log to file so dev can still see codes locally
        @file_put_contents(__DIR__ . '/../mail.log',
            "[" . date('c') . "] TO: $to\nSUBJECT: $subject\n$html_body\n\n", FILE_APPEND);
        return false;
    }
    $mail = new PHPMailer\PHPMailer\PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host       = env('EMAIL_HOST', 'smtp.gmail.com');
        $mail->SMTPAuth   = true;
        $mail->Username   = env('EMAIL_USERNAME');
        $mail->Password   = env('EMAIL_PASSWORD');
        $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = (int) env('EMAIL_PORT', 587);
        $mail->setFrom(env('EMAIL_FROM_ADDRESS', 'no-reply@example.com'), env('EMAIL_FROM_NAME', 'Billah Dee King'));
        $mail->addAddress($to);
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $html_body;
        $mail->AltBody = $alt ?: strip_tags($html_body);
        $mail->send();
        return true;
    } catch (Exception $e) {
        @file_put_contents(__DIR__ . '/../mail.log',
            "[" . date('c') . "] FAILED $to: " . $mail->ErrorInfo . "\n", FILE_APPEND);
        return false;
    }
}
