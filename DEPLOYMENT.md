# Guía de despliegue

Esta guía cubre la puesta en marcha de Academia Alma Mater en local, Docker y servidor tradicional. Para producción, cambia credenciales por defecto, activa HTTPS y revisa permisos de subida.

## Requisitos

Mínimos:

- PHP 7.4 o superior.
- MySQL 5.7 o superior.
- Apache 2.4 o Nginx con soporte PHP.
- Composer.
- Extensiones PHP `mysqli`, `pdo_mysql` y `fileinfo`.
- 512 MB de RAM.
- 100 MB de disco, más espacio para `uploads/`.

Recomendado:

- PHP 8.1 o superior.
- MySQL 8.0.
- HTTPS obligatorio.
- Usuario MySQL dedicado con permisos limitados.
- Copias de seguridad programadas.

## Despliegue con Docker

El repositorio ya incluye `Dockerfile` y `docker-compose.yml`.

```bash
docker compose up -d --build
```

Servicios:

- Web: http://localhost:8080
- MySQL: `localhost:3306`
- Base de datos: `academia`
- Usuario MySQL: `root`
- Contraseña: `password`

Variables configuradas para el contenedor web:

```env
DB_HOST=db
DB_USER=root
DB_PASSWORD=password
DB_NAME=academia
```

El contenedor de base de datos importa automáticamente:

1. `almamater.sql`
2. `database_schema_updates.sql`
3. `database_priority_medium_updates.sql`
4. `database_advanced_updates.sql`
5. `database_review_fixes.sql`

Para reconstruir la base desde cero:

```bash
docker compose down -v
docker compose up -d --build
```

## Despliegue manual en servidor

1. Sube el proyecto al directorio público o clónalo en el servidor:

```bash
git clone https://github.com/Ancodi1/Academia-Alma-Matter-Ancodi.git
```

2. Instala dependencias:

```bash
composer install --no-dev --optimize-autoloader
```

3. Crea la base de datos:

```sql
CREATE DATABASE almamater CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'academia_user'@'localhost' IDENTIFIED BY 'cambia-esta-password';
GRANT SELECT, INSERT, UPDATE, DELETE, CREATE, ALTER, INDEX, DROP, CREATE ROUTINE, ALTER ROUTINE ON almamater.* TO 'academia_user'@'localhost';
FLUSH PRIVILEGES;
```

4. Importa la base:

```bash
mysql -u academia_user -p almamater < almamater.sql
mysql -u academia_user -p almamater < database_schema_updates.sql
mysql -u academia_user -p almamater < database_priority_medium_updates.sql
mysql -u academia_user -p almamater < database_advanced_updates.sql
mysql -u academia_user -p almamater < database_review_fixes.sql
```

5. Configura variables de entorno o ajusta tu entorno PHP/Apache:

```env
DB_HOST=localhost
DB_USER=academia_user
DB_PASSWORD=cambia-esta-password
DB_NAME=almamater
```

6. Prepara permisos:

```bash
chmod -R 755 uploads
find . -type f -name "*.php" -exec chmod 644 {} \;
find . -type f -name "*.css" -exec chmod 644 {} \;
find . -type f -name "*.js" -exec chmod 644 {} \;
```

7. Inicia sesión con `admin` / `admin123` y cambia la contraseña inmediatamente desde la gestión de usuarios.

## Apache

Configura el `DocumentRoot` apuntando al directorio del proyecto. Si el proyecto queda en la raíz del virtual host, las rutas absolutas usadas por la aplicación funcionarán directamente.

Ejemplo orientativo:

```apache
<VirtualHost *:80>
    ServerName academia.example.com
    DocumentRoot /var/www/academia

    <Directory /var/www/academia>
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

Activa módulos habituales:

```bash
sudo a2enmod rewrite headers
sudo systemctl reload apache2
```

## Nginx

Ejemplo orientativo con PHP-FPM:

```nginx
server {
    listen 80;
    server_name academia.example.com;
    root /var/www/academia;
    index index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        include snippets/fastcgi-php.conf;
        fastcgi_pass unix:/run/php/php8.1-fpm.sock;
    }

    location ~ /\. {
        deny all;
    }
}
```

## Configuración de producción

En producción:

- Usa HTTPS.
- Cambia la contraseña `admin123`.
- Usa un usuario MySQL dedicado.
- Desactiva `DEBUG_MODE` y `SHOW_ERRORS` en `config.php`.
- Revisa `upload_max_filesize` y `post_max_size`.
- Protege backups, logs y archivos `.sql` si quedan dentro del directorio público.
- Excluye `config.php`, `.env`, `vendor/` generado y `uploads/` sensibles de commits.

Ejemplo de `php.ini`:

```ini
display_errors = Off
log_errors = On
error_log = /var/log/php_errors.log
max_execution_time = 30
memory_limit = 256M
upload_max_filesize = 10M
post_max_size = 10M
```

## Correo SMTP

El sistema puede enviar avisos con PHPMailer. Configura `config.php` a partir de `config.example.php`:

```php
define('MAIL_HOST', 'smtp.example.com');
define('MAIL_PORT', 587);
define('MAIL_USERNAME', 'usuario@example.com');
define('MAIL_PASSWORD', 'password');
define('MAIL_ENCRYPTION', 'tls');
define('MAIL_FROM', 'no-reply@example.com');
define('MAIL_FROM_NAME', 'Academia Alma Mater');
```

## Backups

Base de datos:

```bash
mysqldump -u academia_user -p almamater > backup_$(date +%Y%m%d_%H%M%S).sql
```

Archivos subidos:

```bash
tar -czf uploads_$(date +%Y%m%d_%H%M%S).tar.gz uploads/
```

Restaura primero la base y después `uploads/` para mantener referencias de archivos consistentes.

## Verificación posterior

- Abrir `/login.php`.
- Entrar como administrador.
- Confirmar que `/index.php` carga métricas del panel.
- Crear o editar un alumno de prueba.
- Subir un archivo pequeño en la ficha del alumno.
- Registrar asistencia y revisar reportes.
- Probar `/api.php?recurso=estado` con sesión iniciada.
