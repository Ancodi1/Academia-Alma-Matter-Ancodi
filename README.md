# 🎓 Academia Alma Mater

Sistema de gestión académica completo desarrollado en PHP con MySQL. Permite gestionar alumnos, asignaturas y exámenes de forma intuitiva y segura.

## ✨ Características

- **Gestión de Alumnos**: CRUD completo con búsqueda y paginación
- **Gestión de Exámenes**: Crear, editar y eliminar exámenes por alumno
- **Gestión de Asignaturas**: Administrar materias y cursos
- **Búsqueda Avanzada**: Buscar alumnos por nombre o apellidos
- **Paginación**: Navegación eficiente en listados grandes
- **Seguridad**: Tokens CSRF, validaciones robustas, protección XSS
- **Responsive**: Diseño adaptable a móviles y tablets
- **UX Moderna**: Toasts, modales, animaciones suaves

## 🚀 Instalación

### Requisitos
- PHP 7.4 o superior
- MySQL 5.7 o superior
- Servidor web (Apache/Nginx)

### Pasos

1. **Clonar el repositorio**
```bash
git clone https://github.com/tu-usuario/academia-alma-mater.git
cd academia-alma-mater
```

2. **Configurar la base de datos**
```bash
# Importar la base de datos
mysql -u root -p < almamater.sql

# Aplicar optimizaciones (opcional pero recomendado)
mysql -u root -p < optimizaciones.sql
```

3. **Configurar conexión a BD**
Editar `models/mysqlConnect.php` con tus credenciales:
```php
$this->server = "localhost";
$this->user = "tu_usuario";
$this->password = "tu_password";
$this->database = "almamater";
```

4. **Configurar servidor web**
- Apache: Asegurar que mod_rewrite esté habilitado
- Nginx: Configurar reglas de reescritura para URLs limpias

5. **Permisos de archivos**
```bash
chmod 755 -R .
chmod 644 *.php
```

## 📁 Estructura del Proyecto

```
academia/
├── controllers/           # Controladores de la aplicación
│   ├── AlumnoController.php
│   ├── accionesAlumno.php
│   ├── accionesExamen.php
│   └── realizarExamen.php
├── models/               # Modelos y conexión a BD
│   ├── mysqlConnect.php
│   ├── csrf.php
│   └── pieDePagina.php
├── views/                # Vistas reutilizables
│   ├── cabecera.php
│   └── pieDePagina.php
├── css/                  # Estilos CSS
├── js/                   # JavaScript
├── img/                  # Imágenes y recursos
├── academia.css          # Estilos principales
├── editorAlumnos.php     # Gestión de alumnos
├── gestionAsignaturas.php # Gestión de asignaturas
├── realizarExamen.php    # Crear exámenes
├── examenesRealizados.php # Ver exámenes
├── almamater.sql         # Base de datos
├── optimizaciones.sql    # Índices de rendimiento
└── README.md
```

## 🔧 Configuración

### Variables de Entorno
Crear archivo `.env` (opcional):
```env
DB_HOST=localhost
DB_USER=root
DB_PASS=
DB_NAME=almamater
```

### Personalización
- **Logo**: Reemplazar `img/logo.png`
- **Colores**: Modificar variables CSS en `academia.css`
- **Título**: Cambiar en `views/cabecera.php`

## 🛡️ Seguridad

- ✅ **Tokens CSRF** en todos los formularios
- ✅ **Validación de entrada** en servidor y cliente
- ✅ **Escape de salida** para prevenir XSS
- ✅ **Sentencias preparadas** contra inyección SQL
- ✅ **Validación de tipos** y rangos de datos

## 📊 Rendimiento

- **Índices optimizados** para consultas frecuentes
- **Paginación eficiente** en listados grandes
- **Conexiones de BD** gestionadas automáticamente
- **Caché de CSS/JS** con versionado

## 🎨 Personalización

### Cambiar Colores
Editar `academia.css`:
```css
:root {
    --color-primary: #0b66c3;
    --color-success: #16a34a;
    --color-error: #dc2626;
}
```

### Añadir Campos
1. Modificar tabla en BD
2. Actualizar `AlumnoController.php`
3. Añadir inputs en formularios
4. Actualizar validaciones

## 🤝 Contribuir

1. Fork el proyecto
2. Crear rama para feature (`git checkout -b feature/nueva-funcionalidad`)
3. Commit cambios (`git commit -m 'Añadir nueva funcionalidad'`)
4. Push a la rama (`git push origin feature/nueva-funcionalidad`)
5. Abrir Pull Request

## 📝 Licencia

Este proyecto está bajo la Licencia MIT. Ver `LICENSE` para más detalles.

## 🐛 Reportar Bugs

Si encuentras un bug, por favor:
1. Verificar que no esté ya reportado
2. Crear issue con descripción detallada
3. Incluir pasos para reproducir
4. Especificar versión de PHP/MySQL

## 📞 Soporte

- **Documentación**: Ver este README
- **Issues**: Usar GitHub Issues
- **Discusiones**: GitHub Discussions

## 🚀 Roadmap

- [ ] Sistema de autenticación
- [ ] Roles de usuario (admin/profesor)
- [ ] Exportación a PDF/Excel
- [ ] API REST
- [ ] Dashboard con estadísticas
- [ ] Notificaciones por email
- [ ] Backup automático

## 📈 Changelog

### v1.0.0
- ✅ CRUD completo de alumnos y exámenes
- ✅ Búsqueda y paginación
- ✅ Seguridad CSRF y validaciones
- ✅ Diseño responsive
- ✅ Optimizaciones de rendimiento

---

**Desarrollado con ❤️ para la comunidad educativa**
