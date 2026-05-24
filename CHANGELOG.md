# Changelog

Todos los cambios notables del proyecto se documentan en este archivo.

El formato sigue la idea de Keep a Changelog y el proyecto usa versionado semántico de forma orientativa.

## [1.1.0] - 2026-05-24

### Añadido

- Panel de control con métricas, clases del día, ausencias recientes y alumnos que requieren seguimiento.
- Autenticación con roles `admin`, `teacher`, `student` y `family`.
- Portal para alumnado y familias vinculado a un alumno.
- Gestión de usuarios y restablecimiento de contraseñas desde administración.
- Matrículas entre alumnos y asignaturas.
- Control de asistencia individual y en bloque.
- Calendario semanal y gestión de horarios con profesor y aula.
- Gestión de profesores.
- Gestión de pagos y estados de cobro.
- Tareas por asignatura y seguimiento de entregas.
- Subida, descarga y eliminación de archivos de alumno.
- Auditoría de acciones sensibles.
- Reportes académicos y de asistencia con exportación CSV, Excel y vista imprimible.
- API JSON protegida por sesión.
- Modo claro/oscuro y manifest PWA.
- Scripts SQL incrementales para nuevas tablas, columnas, índices y revisiones.
- Dockerfile y Docker Compose para entorno local completo.

### Cambiado

- README, guía de despliegue y guía de contribución actualizados al estado real del proyecto.
- Conexión de base de datos preparada para variables de entorno.
- Navegación reorganizada por módulos operativos.
- Flujo de acceso separado entre usuarios internos y portal.

### Seguridad

- Restricción de pantallas por rol.
- CSRF aplicado en acciones de escritura.
- Validación de archivos por extensión, MIME y tamaño.
- Descarga de archivos limitada al directorio `uploads`.
- Uso extendido de consultas preparadas en módulos con entrada de usuario.

## [1.0.0] - 2024-12-19

### Añadido

- Gestión inicial de alumnos con CRUD.
- Gestión de exámenes por alumno.
- Gestión de asignaturas y cursos.
- Búsqueda por nombre y apellidos.
- Paginación en listados.
- Validaciones en servidor y cliente.
- Interfaz responsive.
- Notificaciones con toasts.
- Modales de confirmación.
- Optimizaciones de rendimiento con índices de base de datos.
- Documentación inicial.

### Cambiado

- Migración progresiva hacia consultas preparadas.
- Mejora visual de formularios y listados.
- Reorganización inicial de controladores, modelos y vistas.

### Corregido

- Problemas de paginación en listados.
- Escapes de salida en formularios.
- Validaciones faltantes.
- Manejo de errores de conexión a base de datos.

## [0.9.0] - 2024-12-18

### Añadido

- Estructura básica del proyecto.
- Conexión a MySQL.
- Formularios iniciales de gestión.
- Estilos CSS base.
