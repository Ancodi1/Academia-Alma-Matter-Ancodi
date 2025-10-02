<?php
require_once(__DIR__ . '/../models/csrf.php');
require_once(__DIR__ . '/../models/auth.php');
require_once(__DIR__ . '/../models/mail.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || !validarTokenCSRF($_POST['csrf_token'])) {
        header('Location: /academia/login.php?error=csrf');
        exit;
    }

    $action = $_POST['action'] ?? '';
    $auth = new AuthService();

    if ($action === 'request') {
        $email = trim($_POST['email'] ?? '');
        if ($email === '') {
            header('Location: /academia/login.php?error=email');
            exit;
        }
        $user = $auth->findUserByEmail($email);
        // Generamos siempre respuesta positiva para no filtrar usuarios
        if ($user) {
            $token = $auth->createPasswordResetToken((int)$user['id']);
            if ($token) {
                $link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . '://' . ($_SERVER['HTTP_HOST'] ?? 'localhost') . '/academia/reset_password.php?token=' . urlencode($token);
                $subject = 'Restablecer contraseña';
                $html = '<p>Hola,</p><p>Pulsa en el siguiente enlace para restablecer tu contraseña:</p><p><a href="' . htmlspecialchars($link) . '">Restablecer contraseña</a></p><p>Si no lo has solicitado, ignora este correo.</p>';
                sendEmail($email, $subject, $html);
            }
        }
        header('Location: /academia/login.php?mensaje=reset_enviado');
        exit;
    }

    if ($action === 'reset') {
        $token = $_POST['token'] ?? '';
        $password = $_POST['password'] ?? '';
        $password2 = $_POST['password2'] ?? '';
        if ($token === '' || $password === '' || $password2 === '' || $password !== $password2) {
            header('Location: /academia/reset_password.php?token=' . urlencode($token) . '&error=validacion');
            exit;
        }
        if (strlen($password) < 8) {
            header('Location: /academia/reset_password.php?token=' . urlencode($token) . '&error=debildeb');
            exit;
        }
        $row = $auth->findValidResetByToken($token);
        if (!$row) {
            header('Location: /academia/login.php?error=token');
            exit;
        }
        $ok = $auth->updateUserPassword((int)$row['user_id'], $password);
        if ($ok) {
            $auth->consumeResetToken((int)$row['id']);
            header('Location: /academia/login.php?mensaje=pass_actualizada');
            exit;
        }
        header('Location: /academia/reset_password.php?token=' . urlencode($token) . '&error=guardar');
        exit;
    }
}

http_response_code(405);
echo 'Método no permitido';
?>


