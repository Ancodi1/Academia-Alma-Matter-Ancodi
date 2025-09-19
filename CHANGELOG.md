# 📝 Changelog

Todos los cambios notables de este proyecto serán documentados en este archivo.

El formato está basado en [Keep a Changelog](https://keepachangelog.com/es-ES/1.0.0/),
y este proyecto adhiere a [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

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
