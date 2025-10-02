<?php
require_once(__DIR__ . '/models/session.php');
require_once(__DIR__ . '/models/csrf.php');
require_once(__DIR__ . '/models/auth.php');
require_once(__DIR__ . '/models/rate_limit.php');

$error = '';
$redirect = isset($_GET['redirect']) ? $_GET['redirect'] : '/academia/index.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $clientIp = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    $throttleKey = 'login:' . $clientIp;
    if (isThrottled($throttleKey, 5, 300)) {
        $error = 'Demasiados intentos. Inténtalo de nuevo en unos minutos.';
    } else if (!throttle($throttleKey, 5, 300)) {
        $error = 'Demasiados intentos. Inténtalo de nuevo en unos minutos.';
    } else {
    if (!validarTokenCSRF($_POST['csrf_token'] ?? '')) {
        $error = 'Token CSRF inválido.';
    } else {
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';

        if ($username === '' || $password === '') {
            $error = 'Usuario y contraseña son obligatorios.';
        } else {
            $auth = new AuthService();
            // Permitir login por email o usuario
            if (strpos($username, '@') !== false) {
                $user = $auth->findUserByEmail($username);
            } else {
                $user = $auth->findUserByUsername($username);
            }
            if ($user && $auth->verifyPassword($password, $user['password_hash'])) {
                loginUser((int)$user['id'], (string)$user['nombre'], (string)$user['rol']);
                header('Location: ' . $redirect);
                exit;
            } else {
                $error = 'Credenciales inválidas.';
            }
        }
    }
    }
}
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title>Iniciar sesión</title>
        <link rel="stylesheet" type="text/css" href="/academia/academia.css">
    </head>
    <body>
        <div id="contenido" style="max-width:420px;margin:40px auto;">
            <h1>Iniciar sesión</h1>
            <?php if ($error): ?>
                <div class="aviso error"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            <?php if (isset($_GET['mensaje']) && $_GET['mensaje'] === 'reset_enviado'): ?>
                <div class="aviso exito">Si el correo existe, hemos enviado instrucciones.</div>
            <?php endif; ?>
            <?php if (isset($_GET['mensaje']) && $_GET['mensaje'] === 'pass_actualizada'): ?>
                <div class="aviso exito">Contraseña actualizada. Ya puedes iniciar sesión.</div>
            <?php endif; ?>
            <form method="POST" action="">
                <input type="hidden" name="csrf_token" value="<?php echo generarTokenCSRF(); ?>">
                <div style="margin-bottom:12px;">
                    <label>Usuario</label><br>
                    <input type="text" name="username" autofocus>
                </div>
                <div style="margin-bottom:12px;">
                    <label>Contraseña</label><br>
                    <input type="password" name="password">
                </div>
                <button type="submit">Entrar</button>
            </form>
            <hr style="margin:20px 0;">
            <form method="POST" action="/academia/controllers/password_reset.php">
                <input type="hidden" name="csrf_token" value="<?php echo generarTokenCSRF(); ?>">
                <input type="hidden" name="action" value="request">
                <div style="margin-bottom:12px;">
                    <label>¿Olvidaste tu contraseña? Escribe tu email</label><br>
                    <input type="email" name="email" required>
                </div>
                <button type="submit">Enviar enlace de restablecimiento</button>
            </form>
        </div>
    </body>
</html>


