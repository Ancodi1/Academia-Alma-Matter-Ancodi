<?php
/**
 * Archivo de configuración de ejemplo
 * Copia este archivo como config.php y ajusta los valores según tu entorno
 */

// Configuración de base de datos
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'almamater');
define('DB_CHARSET', 'utf8');

// Configuración de la aplicación
define('APP_NAME', 'Refuerzo Escolar');
define('APP_VERSION', '1.0.0');
define('APP_URL', 'http://localhost/academia');

// Configuración de email
define('MAIL_HOST', 'smtp.gmail.com');
define('MAIL_PORT', 587);
define('MAIL_USERNAME', 'tuemail@gmail.com');
define('MAIL_PASSWORD', 'tupassword');
define('MAIL_ENCRYPTION', 'tls');
define('MAIL_FROM', 'tuemail@gmail.com');
define('MAIL_FROM_NAME', 'Refuerzo Escolar');
define('SESSION_LIFETIME', 7200); // 2 horas en segundos

// Configuración de paginación
define('DEFAULT_ITEMS_PER_PAGE', 5);
define('MAX_ITEMS_PER_PAGE', 50);

// Configuración de validación
define('MIN_AGE', 1);
define('MAX_AGE', 120);
define('MIN_GRADE', 0);
define('MAX_GRADE', 10);

// Configuración de logging
define('LOG_LEVEL', 'INFO'); // DEBUG, INFO, WARNING, ERROR
define('LOG_FILE', 'logs/app.log');

// Configuración de desarrollo
define('DEBUG_MODE', true); // Cambiar a false en producción
define('SHOW_ERRORS', true); // Cambiar a false en producción

// Configuración de email (para futuras funcionalidades)
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_USERNAME', '');
define('SMTP_PASSWORD', '');
define('SMTP_FROM_EMAIL', '');
define('SMTP_FROM_NAME', 'Refuerzo Escolar');

// Configuración de archivos
define('UPLOAD_MAX_SIZE', 5242880); // 5MB en bytes
define('ALLOWED_EXTENSIONS', ['jpg', 'jpeg', 'png', 'gif', 'pdf']);

// Configuración de caché
define('CACHE_ENABLED', false);
define('CACHE_LIFETIME', 3600); // 1 hora en segundos
define('CACHE_DIR', 'cache/');

// Configuración de backup
define('BACKUP_ENABLED', false);
define('BACKUP_DIR', 'backups/');
define('BACKUP_RETENTION_DAYS', 30);

// Configuración de idioma
define('DEFAULT_LANGUAGE', 'es');
define('SUPPORTED_LANGUAGES', ['es', 'en']);

// Configuración de zona horaria
date_default_timezone_set('Europe/Madrid');

// Configuración de errores (solo en desarrollo)
if (DEBUG_MODE) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}
?>
