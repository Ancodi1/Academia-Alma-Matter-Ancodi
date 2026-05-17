# 🚀 Guía de Despliegue

Esta guía te ayudará a desplegar Refuerzo Escolar en diferentes entornos.

## 📋 Requisitos del Servidor

### Mínimos
- **PHP**: 7.4 o superior
- **MySQL**: 5.7 o superior
- **Memoria RAM**: 512MB
- **Espacio en disco**: 100MB
- **Servidor web**: Apache 2.4+ o Nginx 1.18+

### Recomendados
- **PHP**: 8.0 o superior
- **MySQL**: 8.0 o superior
- **Memoria RAM**: 1GB o más
- **Espacio en disco**: 500MB o más
- **Servidor web**: Apache 2.4+ con mod_rewrite o Nginx 1.18+

## 🌐 Despliegue en Servidor Compartido

### 1. Preparar Archivos
```bash
# Comprimir archivos (excluyendo archivos innecesarios)
zip -r academia.zip . -x "*.git*" "*.md" "tests/*" "cache/*" "logs/*"
```

### 2. Subir al Servidor
- Subir archivos via FTP/SFTP
- Extraer en el directorio público (public_html, www, etc.)

### 3. Configurar Base de Datos
```sql
-- Crear base de datos
CREATE DATABASE almamater CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Importar estructura y datos
mysql -u usuario -p almamater < almamater.sql
mysql -u usuario -p almamater < optimizaciones.sql
```

### 4. Configurar Conexión
Editar `models/mysqlConnect.php`:
```php
$this->server = "localhost";
$this->user = "tu_usuario_bd";
$this->password = "tu_password_bd";
$this->database = "almamater";
```

### 5. Configurar Permisos
```bash
chmod 755 -R .
chmod 644 *.php
chmod 644 *.css
chmod 644 *.js
```

## 🐳 Despliegue con Docker

### 1. Crear Dockerfile
```dockerfile
FROM php:8.0-apache

# Instalar extensiones PHP
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Copiar archivos
COPY . /var/www/html/

# Configurar Apache
RUN a2enmod rewrite
COPY .htaccess /var/www/html/

# Configurar permisos
RUN chown -R www-data:www-data /var/www/html
```

### 2. Crear docker-compose.yml
```yaml
version: '3.8'
services:
  web:
    build: .
    ports:
      - "80:80"
    depends_on:
      - db
    environment:
      - DB_HOST=db
      - DB_USER=root
      - DB_PASS=password
      - DB_NAME=almamater

  db:
    image: mysql:8.0
    environment:
      MYSQL_ROOT_PASSWORD: password
      MYSQL_DATABASE: almamater
    volumes:
      - mysql_data:/var/lib/mysql
      - ./almamater.sql:/docker-entrypoint-initdb.d/almamater.sql
      - ./optimizaciones.sql:/docker-entrypoint-initdb.d/optimizaciones.sql

volumes:
  mysql_data:
```

### 3. Desplegar
```bash
docker-compose up -d
```

## ☁️ Despliegue en la Nube

### Heroku
```bash
# Instalar Heroku CLI
# Crear Procfile
echo "web: vendor/bin/heroku-php-apache2" > Procfile

# Crear app.json
cat > app.json << EOF
{
  "name": "refuerzo-escolar",
  "description": "Sistema de gestión académica",
  "keywords": ["php", "mysql", "academia"],
  "website": "https://tu-app.herokuapp.com",
  "repository": "https://github.com/tu-usuario/refuerzo-escolar",
  "success_url": "/",
  "env": {
    "DB_HOST": {
      "description": "Host de la base de datos",
      "value": "localhost"
    }
  },
  "addons": [
    "cleardb:ignite"
  ]
}
EOF

# Desplegar
git add .
git commit -m "Deploy to Heroku"
git push heroku main
```

### DigitalOcean App Platform
1. Conectar repositorio GitHub
2. Configurar variables de entorno
3. Seleccionar stack PHP
4. Configurar base de datos MySQL

### AWS EC2
```bash
# Instalar LAMP stack
sudo apt update
sudo apt install apache2 mysql-server php php-mysql

# Clonar repositorio
git clone https://github.com/tu-usuario/refuerzo-escolar.git
sudo mv refuerzo-escolar /var/www/html/

# Configurar Apache
sudo a2enmod rewrite
sudo systemctl restart apache2

# Configurar MySQL
sudo mysql_secure_installation
mysql -u root -p < /var/www/html/almamater.sql
```

## 🔧 Configuración de Producción

### 1. Configurar PHP
```ini
; php.ini
display_errors = Off
log_errors = On
error_log = /var/log/php_errors.log
max_execution_time = 30
memory_limit = 256M
upload_max_filesize = 10M
post_max_size = 10M
```

### 2. Configurar Apache
```apache
# .htaccess
RewriteEngine On
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(.*)$ index.php [QSA,L]

# Seguridad
Header always set X-Content-Type-Options nosniff
Header always set X-Frame-Options DENY
Header always set X-XSS-Protection "1; mode=block"
Header always set Strict-Transport-Security "max-age=31536000; includeSubDomains"
```

### 3. Configurar MySQL
```sql
-- Crear usuario específico
CREATE USER 'academia_user'@'localhost' IDENTIFIED BY 'password_seguro';
GRANT SELECT, INSERT, UPDATE, DELETE ON almamater.* TO 'academia_user'@'localhost';
FLUSH PRIVILEGES;
```

### 4. Configurar SSL
```bash
# Con Let's Encrypt
sudo apt install certbot python3-certbot-apache
sudo certbot --apache -d tu-dominio.com
```

## 📊 Monitoreo y Mantenimiento

### 1. Logs
```bash
# Ver logs de Apache
tail -f /var/log/apache2/error.log

# Ver logs de PHP
tail -f /var/log/php_errors.log

# Ver logs de MySQL
tail -f /var/log/mysql/error.log
```

### 2. Backup
```bash
#!/bin/bash
# backup.sh
DATE=$(date +%Y%m%d_%H%M%S)
mysqldump -u root -p almamater > backup_$DATE.sql
tar -czf backup_$DATE.tar.gz /var/www/html/
```

### 3. Actualizaciones
```bash
# Actualizar aplicación
git pull origin main
composer install --no-dev --optimize-autoloader

# Actualizar base de datos si es necesario
mysql -u root -p almamater < updates.sql
```

## 🚨 Solución de Problemas

### Error de Conexión a BD
```php
// Verificar conexión
$mysqli = new mysqli("localhost", "usuario", "password", "almamater");
if ($mysqli->connect_error) {
    die("Error: " . $mysqli->connect_error);
}
```

### Error 500
- Verificar logs de Apache/PHP
- Verificar permisos de archivos
- Verificar sintaxis PHP

### Error de Permisos
```bash
sudo chown -R www-data:www-data /var/www/html/
sudo chmod -R 755 /var/www/html/
```

## 🔒 Seguridad en Producción

1. **Cambiar credenciales por defecto**
2. **Configurar firewall**
3. **Usar HTTPS**
4. **Actualizar regularmente**
5. **Monitorear logs**
6. **Hacer backups regulares**

## 📈 Optimización

1. **Habilitar caché de PHP**
2. **Configurar compresión gzip**
3. **Optimizar imágenes**
4. **Usar CDN para archivos estáticos**
5. **Configurar índices de BD**

---

**¡Tu aplicación está lista para producción! 🎉**
