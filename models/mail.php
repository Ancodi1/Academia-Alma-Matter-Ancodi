<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';
if (file_exists(__DIR__ . '/../config.php')) {
    require_once(__DIR__ . '/../config.php');
} elseif (file_exists(__DIR__ . '/../config.example.php')) {
    require_once(__DIR__ . '/../config.example.php');
}

function enviarEmail($to, $subject, $body) {
    if (!defined('MAIL_HOST') || !defined('MAIL_USERNAME') || MAIL_USERNAME === '' || MAIL_USERNAME === 'tuemail@gmail.com') {
        error_log("Email no enviado: configuración SMTP pendiente para $to");
        return false;
    }
    $mail = new PHPMailer(true);

    try {
        // Configuración del servidor
        $mail->isSMTP();
        $mail->Host = MAIL_HOST;
        $mail->SMTPAuth = true;
        $mail->Username = MAIL_USERNAME;
        $mail->Password = MAIL_PASSWORD;
        $mail->SMTPSecure = MAIL_ENCRYPTION;
        $mail->Port = MAIL_PORT;

        // Destinatarios
        $mail->setFrom(MAIL_FROM, MAIL_FROM_NAME);
        $mail->addAddress($to);

        // Contenido
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = $body;

        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Error enviando email: {$mail->ErrorInfo}");
        return false;
    }
}
?>
