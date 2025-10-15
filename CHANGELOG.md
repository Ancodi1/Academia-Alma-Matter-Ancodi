# 📝 Changelog

Todos los cambios notables de este proyecto serán documentados en este archivo.

El formato está basado en [Keep a Changelog](https://keepachangelog.com/es-ES/1.0.0/),
y este proyecto adhiere a [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [1.1.0] - 2025-10-15

### ✨ Añadido
- **Sistema de Calendario Académico**: Gestión completa de eventos con colores personalizables
- **Control de Asistencia**: Sistema masivo por asignatura y fecha con estados (presente, falta, justificada, tardanza)
- **Gestión de Trabajos y Proyectos**: CRUD completo con estados, fechas de entrega y calificaciones
- **Sistema de Comunicados**: Avisos con fecha de expiración y destinatarios específicos
- **Historial Académico**: Registro detallado de evaluaciones con gráficos y estadísticas
- **Sistema de Notificaciones**: Notificaciones en tiempo real con API REST
- **Recuperación de Contraseña**: Sistema completo por email con tokens seguros
- **Sistema de Roles**: Autenticación basada en roles (admin/profesor/alumno)
- **Tema Oscuro/Claro**: Interfaz con modo oscuro y persistencia de preferencias
- **Exportación/Importación CSV**: Para alumnos y asignaturas con validación
- **Gráficos Interactivos**: Chart.js para visualización de datos académicos
- **API REST**: Endpoints para notificaciones y gestión de datos

### 🎨 Mejoras de UI/UX
- **Diseño Responsive**: Adaptación completa para móviles y tablets
- **Selectores de Fecha/Hora**: Mini-calendario y selector de tiempo separado
- **Navegación Mejorada**: Breadcrumbs y menú contextual
- **Indicadores Visuales**: Estados de carga, confirmaciones y feedback
- **Accesibilidad**: Focus visible, contraste mejorado y navegación por teclado
- **Tema Personalizable**: Variables CSS para fácil personalización

### 🔧 Mejoras Técnicas
- **Protección CSRF**: Tokens en todos los formularios
- **Rate Limiting**: Protección contra ataques de fuerza bruta
- **Prepared Statements**: Seguridad SQL mejorada
- **Validación de Datos**: Validación robusta en servidor y cliente
- **Manejo de Errores**: Logging mejorado y mensajes de error informativos
- **Optimización de Consultas**: Índices específicos para nuevas funcionalidades
 - **i18n selector**: Selector de idioma persistente (ES/EN)
 - **Dashboard**: Nueva serie “Exámenes por mes (12)”
 - **Exportaciones**: CSV en asistencia/historial y vistas imprimibles (PDF naveg.)
 - **API**: Header `X-CSRF-Token` requerido en `api/notifications.php`
 - **Minificación y caché**: `academia.min.css` y `js/asignaturas.min.js` + cache busting

### 🗄️ Base de Datos
- **Nuevas Tablas**: CalendarioAcademico, Asistencia, Trabajo, Comunicado, HistorialAcademico
- **Tabla de Usuarios**: Sistema completo de autenticación con roles
- **Sistema de Recuperación**: Tabla PasswordReset para tokens seguros
- **Índices Optimizados**: Para consultas frecuentes y mejor rendimiento
   - `Asistencia (fecha, id_asignatura)`
   - `HistorialAcademico (id_alumno, id_asignatura, periodo)`
- **Foreign Keys**: Integridad referencial mejorada

### 🔒 Seguridad
- **Autenticación Basada en Roles**: Control de acceso granular
- **Hash Seguro de Contraseñas**: Argon2id (fallback bcrypt)
- **Validación de Entrada**: Sanitización y validación de todos los datos
- **Protección XSS**: Escape de salida en todas las vistas
- **Tokens CSRF**: Protección contra ataques cross-site
- **Rate Limiting**: Protección contra ataques automatizados

### 📊 Rendimiento
- **Índices Específicos**: Para nuevas consultas de gestión académica
- **Paginación Optimizada**: En todos los listados
- **Caché de Consultas**: Para datos frecuentemente accedidos
- **Optimización de Assets**: CSS y JS minificados
- **Lazy Loading**: Para componentes pesados

### 📚 Documentación
- **README Actualizado**: Instrucciones completas de instalación
- **CREDENCIALES_ADMIN.md**: Información de acceso por defecto
- **Changelog Detallado**: Seguimiento completo de cambios
- **Comentarios en Código**: Documentación inline mejorada
- **Guías de Uso**: Para nuevas funcionalidades

## [1.0.0] - 2024-12-19

### ✨ Añadido
- Sistema completo de gestión de alumnos con CRUD
- Gestión de exámenes por alumno (crear, editar, eliminar)
- Gestión de asignaturas y cursos
- Sistema de búsqueda con filtros por nombre y apellidos
- Paginación eficiente en todos los listados
- Sistema de seguridad con tokens CSRF
- Validaciones robustas en servidor y cliente
- Interfaz responsive para móviles y tablets
- Sistema de notificaciones con toasts
- Modales de confirmación para acciones importantes
- Protección contra XSS e inyección SQL
- Optimizaciones de rendimiento con índices de BD
- Manejo automático de conexiones de base de datos
- Logging de errores para debugging
- Documentación completa con README y guías

### 🔧 Cambiado
- Migración de consultas SQL a sentencias preparadas
- Mejora del diseño visual con colores modernos
- Optimización de consultas de base de datos
- Refactorización del código JavaScript para mejor rendimiento
- Mejora de la accesibilidad con focus visible
- Actualización de la estructura de archivos

### 🐛 Corregido
- Error de paginación en listados de alumnos
- Vulnerabilidades XSS en formularios
- Memory leaks en JavaScript
- Manejo de errores de conexión a base de datos
- Validaciones faltantes en formularios
- Problemas de responsive en móviles

### 🔒 Seguridad
- Implementación de tokens CSRF en todos los formularios
- Escape de salida para prevenir XSS
- Validación de entrada en servidor y cliente
- Sentencias preparadas contra inyección SQL
- Validación de tipos y rangos de datos
- Sanitización de parámetros GET y POST

### 📊 Rendimiento
- Índices optimizados para consultas frecuentes
- Paginación eficiente para listados grandes
- Gestión automática de conexiones de BD
- Optimización de consultas SQL
- Mejora del rendimiento de JavaScript
- Caché de archivos CSS/JS con versionado

### 📚 Documentación
- README completo con instrucciones de instalación
- Guía de contribución detallada
- Changelog para seguimiento de versiones
- Comentarios en código para mejor mantenibilidad
- Archivo de optimizaciones SQL
- Licencia MIT para uso libre

## [0.9.0] - 2024-12-18

### ✨ Añadido
- Estructura básica del proyecto
- Conexión a base de datos MySQL
- Formularios básicos de gestión
- Estilos CSS iniciales

### 🔧 Cambiado
- Migración de PHP procedural a orientado a objetos
- Mejora de la estructura de archivos

### 🐛 Corregido
- Problemas de conexión a base de datos
- Errores de sintaxis en PHP

---

## Tipos de Cambios

- `✨ Añadido` para nuevas funcionalidades
- `🔧 Cambiado` para cambios en funcionalidades existentes
- `🐛 Corregido` para corrección de bugs
- `🔒 Seguridad` para mejoras de seguridad
- `📊 Rendimiento` para optimizaciones
- `📚 Documentación` para cambios en documentación
- `🗑️ Eliminado` para funcionalidades eliminadas
