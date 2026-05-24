# Academia Alma Mater

Sistema de gestión académica para academias de refuerzo escolar, desarrollado en PHP y MySQL. La aplicación centraliza alumnos, asignaturas, matrículas, horarios, asistencia, exámenes, pagos, tareas, reportes y acceso diferenciado para equipo interno, alumnado y familias.

## Estado del proyecto

El proyecto ya incluye autenticación, roles, panel operativo, portal para alumnos/familias, API JSON básica y despliegue con Docker. La documentación anterior describía varias de estas piezas como roadmap; este README refleja el estado actual del repositorio.

## Funcionalidades principales

- Gestión de alumnos con ficha completa, datos de contacto, observaciones, búsqueda y paginación.
- Gestión de asignaturas, cursos y matrículas activas por alumno.
- Registro de exámenes y consulta de historial académico.
- Control de asistencia individual y en bloque por asignatura y fecha.
- Horarios semanales con aula, profesor y detección de solapamientos.
- Gestión de profesores para horarios y filtros de calendario.
- Pagos por alumno con estados pendiente, pagado y vencido.
- Tareas por asignatura y seguimiento de entregas.
- Archivos asociados a alumnos, con subida y descarga controlada.
- Panel de control con resumen de alumnos, asignaturas, matrículas, clases del día y alertas.
- Reportes con exportación CSV, Excel y vista imprimible para PDF.
- Portal para alumnos y familias con datos académicos, asistencia, pagos y matrículas.
- Administración de usuarios y roles.
- Auditoría de acciones sensibles.
- API JSON protegida por sesión.
- Interfaz responsive con modo claro/oscuro y manifest PWA.

## Roles

- `admin`: acceso completo, incluyendo usuarios y auditoría.
- `teacher`: acceso interno a la gestión académica y operativa.
- `student`: acceso al portal vinculado a su alumno.
- `family`: acceso al portal vinculado a un alumno.

El usuario inicial de desarrollo se crea desde los scripts SQL:

- Usuario: `admin`
- Contraseña: `admin123`

Cambia esta contraseña antes de usar el sistema en un entorno real.

## Requisitos

- PHP 7.4 o superior.
- MySQL 5.7 o superior.
- Extensiones PHP `mysqli` y `pdo_mysql`.
- Composer, necesario para PHPMailer.
- Apache o servidor compatible con PHP.
- Docker y Docker Compose si se usa el entorno contenedorizado.

## Puesta en marcha con Docker

```bash
docker compose up -d --build
```

Después abre:

- Aplicación: http://localhost:8080
- MySQL: `localhost:3306`
- Base de datos Docker: `academia`
- Usuario MySQL: `root`
- Contraseña MySQL: `password`

El `docker-compose.yml` importa `almamater.sql` y aplica las actualizaciones SQL incluidas en el repositorio.

Si necesitas reiniciar la base de datos desde cero:

```bash
docker compose down -v
docker compose up -d --build
```

## Instalación manual

1. Clona el repositorio:

```bash
git clone https://github.com/Ancodi1/Academia-Alma-Matter-Ancodi.git
cd Academia-Alma-Matter-Ancodi
```

2. Instala dependencias PHP:

```bash
composer install
```

3. Crea la base de datos:

```sql
CREATE DATABASE almamater CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

4. Importa la estructura y las actualizaciones:

```bash
mysql -u root -p almamater < almamater.sql
mysql -u root -p almamater < database_schema_updates.sql
mysql -u root -p almamater < database_priority_medium_updates.sql
mysql -u root -p almamater < database_advanced_updates.sql
mysql -u root -p almamater < database_review_fixes.sql
```

5. Configura la conexión. La aplicación lee estas variables de entorno:

```env
DB_HOST=localhost
DB_USER=root
DB_PASSWORD=
DB_NAME=almamater
```

Si no existen, `models/mysqlConnect.php` usa valores locales por defecto.

6. Opcionalmente copia la configuración de ejemplo:

```bash
cp config.example.php config.php
```

7. Asegura permisos de subida:

```bash
chmod -R 755 uploads
```

## Configuración de correo

El proyecto usa PHPMailer para avisos por email en acciones como asistencia y exámenes. Copia `config.example.php` a `config.php` y ajusta los valores SMTP:

```php
define('MAIL_HOST', 'smtp.example.com');
define('MAIL_PORT', 587);
define('MAIL_USERNAME', 'usuario@example.com');
define('MAIL_PASSWORD', 'password');
define('MAIL_FROM', 'no-reply@example.com');
```

## API

La API está en `api.php` y requiere una sesión iniciada. Recursos disponibles:

- `api.php?recurso=estado`
- `api.php?recurso=alumnos`
- `api.php?recurso=asignaturas`
- `api.php?recurso=horarios`
- `api.php?recurso=pagos`
- `api.php?recurso=resumen`

Los usuarios internos pueden ver datos globales. Los usuarios `student` y `family` solo reciben información vinculada a su alumno.

## Estructura

```text
.
├── controllers/          # Controladores y acciones POST
├── models/               # Conexión, autenticación, CSRF, correo y auditoría
├── views/                # Cabecera y pie reutilizables
├── js/                   # JavaScript de pantallas concretas
├── img/                  # Recursos visuales
├── uploads/              # Archivos subidos de alumnos
├── pruebas/              # Pruebas/manual checks existentes
├── .github/              # Workflows e issue templates
├── almamater.sql         # Base inicial
├── database_*.sql        # Actualizaciones de esquema
├── docker-compose.yml    # Entorno local completo
├── Dockerfile            # Imagen PHP/Apache
└── academia.css          # Estilos principales
```

## Seguridad

- Autenticación por sesión.
- Roles y restricciones de acceso por pantalla.
- Tokens CSRF en formularios sensibles.
- Consultas preparadas en los módulos principales.
- Escape de salida con `htmlspecialchars`.
- Validación de archivos por extensión, MIME y tamaño.
- Descarga de archivos limitada al directorio `uploads`.
- Auditoría para pagos, tareas y módulos sensibles.

## Desarrollo

Comprobación rápida de sintaxis PHP:

```bash
find . -name "*.php" -not -path "./vendor/*" -exec php -l {} \;
```

Prueba básica de conexión a base de datos:

```bash
php pruebas/pruebaBaseDatos.php
```

El workflow de GitHub Actions ejecuta lint de PHP, prepara MySQL y genera un artefacto de despliegue.

## Documentación relacionada

- [DEPLOYMENT.md](DEPLOYMENT.md): despliegue y operación.
- [CONTRIBUTING.md](CONTRIBUTING.md): flujo de contribución.
- [CHANGELOG.md](CHANGELOG.md): historial de cambios.
- [LICENSE](LICENSE): licencia MIT.
