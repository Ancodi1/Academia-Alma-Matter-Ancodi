<?php
session_start();
require_once("models/mysqlConnect.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];
    
    $conn = new mysqlConn();
    $stmt = $conn->preparar("SELECT id, password, role FROM Usuario WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($row = $result->fetch_assoc()) {
        if (password_verify($password, $row['password'])) {
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['role'] = $row['role'];
            header("Location: index.php");
            exit;
        }
    }
    $error = "Credenciales inválidas";
}

?>
<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" type="text/css" href="academia.css">
    <link href='https://fonts.googleapis.com/css?family=Actor' rel='stylesheet'>
    <meta charset="utf-8">
    <title>Login - Academia Alma Mater</title>
</head>
<body class="page-login">
    <div id="contenido" class="login-container">
        <div class="login-card">
            <h1>Inicio de sesión</h1>
            <?php if (isset($error)) echo "<p class='aviso error'>$error</p>"; ?>
            <form method="post" class="login-form">
                <div class="form-group">
                    <label for="username">Usuario</label>
                    <input type="text" id="username" name="username" placeholder="admin" required>
                </div>
                <div class="form-group">
                    <label for="password">Contraseña</label>
                    <input type="password" id="password" name="password" placeholder="admin123" required>
                </div>
                <button type="submit">Iniciar Sesión</button>
            </form>
        </div>
    </div>
</body>
</html>