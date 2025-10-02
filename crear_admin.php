<?php
// Script para crear usuario administrador con credenciales sencillas
// Ejecutar una vez para crear el usuario admin

require_once(__DIR__ . '/models/mysqlConnect.php');

$db = new mysqlConn();

// Verificar si la tabla Usuario existe, si no crearla
$createTable = "CREATE TABLE IF NOT EXISTS `Usuario` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `username` VARCHAR(100) NOT NULL UNIQUE,
  `email` VARCHAR(255) NOT NULL UNIQUE,
  `nombre` VARCHAR(255) NOT NULL,
  `rol` ENUM('admin','profesor','alumno') NOT NULL DEFAULT 'profesor',
  `password_hash` VARCHAR(255) NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci";

$db->realizarConsultaSQL($createTable);

// Crear usuario admin con credenciales sencillas
$username = 'admin';
$email = 'admin@academia.com';
$nombre = 'Administrador';
$rol = 'admin';
$password = 'admin'; // Contraseña sencilla
$password_hash = password_hash($password, PASSWORD_BCRYPT);

// Verificar si ya existe
$checkStmt = $db->preparar('SELECT id FROM Usuario WHERE username = ?');
$checkStmt->bind_param('s', $username);
$checkStmt->execute();
$result = $checkStmt->get_result();

if ($result->num_rows === 0) {
    // Insertar usuario admin
    $insertStmt = $db->preparar('INSERT INTO Usuario (username, email, nombre, rol, password_hash) VALUES (?, ?, ?, ?, ?)');
    $insertStmt->bind_param('sssss', $username, $email, $nombre, $rol, $password_hash);
    
    if ($insertStmt->execute()) {
        echo "✅ Usuario administrador creado exitosamente:\n";
        echo "📧 Email: admin@academia.com\n";
        echo "🔑 Contraseña: admin\n";
        echo "👤 Usuario: admin\n";
        echo "🎯 Rol: Administrador\n\n";
        echo "⚠️  IMPORTANTE: Cambia estas credenciales en producción por seguridad.\n";
    } else {
        echo "❌ Error al crear el usuario administrador.\n";
    }
    $insertStmt->close();
} else {
    echo "ℹ️  El usuario administrador ya existe.\n";
    echo "📧 Email: admin@academia.com\n";
    echo "🔑 Contraseña: admin\n";
    echo "👤 Usuario: admin\n";
}

$checkStmt->close();
?>
