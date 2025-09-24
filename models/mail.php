<?php
// Envío de correos simple. Usa PHPMailer si está disponible, si no, mail()

// Cargar configuración si existe
@include_once(__DIR__ . '/../config.php');

function sendEmail(string $to, string $subject, string $htmlBody, string $textBody = ''): bool {
    // Si PHPMailer está disponible vía autoload
    if (class_exists('PHPMailer\PHPMailer\PHPMailer')) {
        try {
            $mail = new PHPMailer\PHPMailer\PHPMailer(true);
            $mail->isSMTP();
            $mail->Host = defined('SMTP_HOST') ? SMTP_HOST : 'localhost';
            $mail->Port = defined('SMTP_PORT') ? SMTP_PORT : 25;
            $mail->SMTPAuth = (defined('SMTP_USERNAME') && SMTP_USERNAME !== '');
            if ($mail->SMTPAuth) {
                $mail->Username = SMTP_USERNAME;
                $mail->Password = defined('SMTP_PASSWORD') ? SMTP_PASSWORD : '';
                $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
            }
            $fromEmail = defined('SMTP_FROM_EMAIL') && SMTP_FROM_EMAIL !== '' ? SMTP_FROM_EMAIL : 'no-reply@localhost';
            $fromName = defined('SMTP_FROM_NAME') ? SMTP_FROM_NAME : 'Academia';
            $mail->setFrom($fromEmail, $fromName);
            $mail->addAddress($to);
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body = $htmlBody;
            $mail->AltBody = $textBody ?: strip_tags($htmlBody);
            $mail->send();
            return true;
        } catch (Throwable $e) {
            error_log('Email error (PHPMailer): ' . $e->getMessage());
            return false;
        }
    }

    // Fallback: mail()
    $headers = [];
    $fromEmail = defined('SMTP_FROM_EMAIL') && SMTP_FROM_EMAIL !== '' ? SMTP_FROM_EMAIL : 'no-reply@localhost';
    $fromName = defined('SMTP_FROM_NAME') ? SMTP_FROM_NAME : 'Academia';
    $headers[] = 'From: ' . $fromName . ' <' . $fromEmail . '>';
    $headers[] = 'MIME-Version: 1.0';
    $headers[] = 'Content-type: text/html; charset=UTF-8';
    $ok = @mail($to, '=?UTF-8?B?' . base64_encode($subject) . '?=', $htmlBody, implode("\r\n", $headers));
    if (!$ok) {
        error_log('Email error: mail() devolvió false');
    }
    return $ok;
}

function notifyEvent(string $title, string $message): void {
    $to = defined('NOTIFY_TO_EMAIL') ? NOTIFY_TO_EMAIL : '';
    if ($to === '') return; // Si no está configurado, no enviamos
    $html = '<h3>' . htmlspecialchars($title) . '</h3><p>' . nl2br(htmlspecialchars($message)) . '</p>';
    sendEmail($to, $title, $html, $title . "\n\n" . $message);
}
?>


