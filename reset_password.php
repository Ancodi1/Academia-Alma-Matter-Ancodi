<?php
require_once(__DIR__ . '/models/csrf.php');
require_once(__DIR__ . '/models/auth.php');

$token = $_GET['token'] ?? '';
$error = $_GET['error'] ?? '';

// Validamos preexistencia del token para UX (no vinculante)
$isValid = false;
if ($token !== '') {
    $auth = new AuthService();
    $row = $auth->findValidResetByToken($token);
    $isValid = (bool)$row;
}
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title>Restablecer contraseña</title>
        <link rel="stylesheet" type="text/css" href="/academia/academia.css">
    </head>
    <body>
        <div id="contenido" style="max-width:420px;margin:40px auto;">
            <h1>Restablecer contraseña</h1>
            <?php if ($error === 'validacion'): ?>
                <div class="aviso error">Revisa los campos del formulario.</div>
            <?php elseif ($error === 'debildeb'): ?>
                <div class="aviso error">La contraseña debe tener al menos 8 caracteres.</div>
            <?php elseif ($error === 'guardar'): ?>
                <div class="aviso error">No se pudo guardar la contraseña. Intenta de nuevo.</div>
            <?php endif; ?>
            <?php if (!$isValid): ?>
                <div class="aviso error">Enlace inválido o caducado.</div>
            <?php else: ?>
            <form method="POST" action="/academia/controllers/password_reset.php">
                <input type="hidden" name="csrf_token" value="<?php echo generarTokenCSRF(); ?>">
                <input type="hidden" name="action" value="reset">
                <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">
                <div style="margin-bottom:12px;">
                    <label>Nueva contraseña</label><br>
                    <input type="password" name="password" required>
                </div>
                <div style="margin-bottom:12px;">
                    <label>Repite la nueva contraseña</label><br>
                    <input type="password" name="password2" required>
                </div>
                <button type="submit">Guardar contraseña</button>
            </form>
            <?php endif; ?>
        </div>
    </body>
 </html>


