<?php
session_start();
require_once("models/mysqlConnect.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];
    
    $conn = new mysqlConn();
    $stmt = $conn->conexion->prepare("SELECT id, password, role FROM Usuario WHERE username = ?");
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
<body>
    <div id="contenido" style="max-width: 400px; margin: 100px auto; text-align: center;">
        <h1>Login</h1>
        <?php if (isset($error)) echo "<p style='color:red;'>$error</p>"; ?>
        <form method="post">
            <input type="text" name="username" placeholder="Usuario" required><br><br>
            <input type="password" name="password" placeholder="Contraseña" required><br><br>
            <button type="submit">Iniciar Sesión</button>
        </form>
    </div>
</body>
</html>