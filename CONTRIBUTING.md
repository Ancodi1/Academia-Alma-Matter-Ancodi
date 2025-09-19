# 🤝 Guía de Contribución

¡Gracias por tu interés en contribuir a Academia Alma Mater! Este documento te guiará en el proceso de contribución.

## 🚀 Cómo Contribuir

### 1. Fork y Clone
```bash
# Fork el repositorio en GitHub
# Luego clona tu fork
git clone https://github.com/tu-usuario/academia-alma-mater.git
cd academia-alma-mater
```

### 2. Configurar el Entorno
```bash
# Crear rama para tu contribución
git checkout -b feature/nombre-de-tu-feature

# Configurar la base de datos
mysql -u root -p < almamater.sql
mysql -u root -p < optimizaciones.sql
```

### 3. Hacer Cambios
- Sigue las convenciones de código existentes
- Añade comentarios para código complejo
- Actualiza la documentación si es necesario
- Prueba tus cambios exhaustivamente

### 4. Commit y Push
```bash
git add .
git commit -m "feat: añadir nueva funcionalidad X"
git push origin feature/nombre-de-tu-feature
```

### 5. Crear Pull Request
- Ve a GitHub y crea un Pull Request
- Describe claramente qué cambios has hecho
- Menciona cualquier issue relacionado

## 📝 Convenciones de Código

### PHP
```php
// Usar camelCase para variables
$nombreAlumno = "Juan";

// Usar PascalCase para clases
class AlumnoController {
    // Usar camelCase para métodos
    public function getAlumnoPorId($id) {
        // Comentarios en español
        return $this->conexion->realizarConsultaSQL($sql);
    }
}
```

### JavaScript
```javascript
// Usar camelCase para variables y funciones
function mostrarToast(mensaje, tipo = 'success') {
    const elemento = document.createElement('div');
    // Comentarios en español
    return elemento;
}
```

### CSS
```css
/* Usar kebab-case para clases */
.btn-editar {
    /* Comentarios en español */
    background: #0b66c3;
}
```

## 🐛 Reportar Bugs

### Antes de Reportar
1. Verificar que no esté ya reportado
2. Probar en la última versión
3. Revisar la documentación

### Información Necesaria
- **Versión de PHP**: `php -v`
- **Versión de MySQL**: `mysql --version`
- **Navegador y versión**
- **Pasos para reproducir**
- **Comportamiento esperado vs actual**
- **Screenshots si es relevante**

### Template de Bug Report
```markdown
**Descripción del Bug**
Descripción clara y concisa del problema.

**Pasos para Reproducir**
1. Ir a '...'
2. Hacer clic en '...'
3. Ver error

**Comportamiento Esperado**
Qué debería pasar.

**Screenshots**
Si aplica, añadir screenshots.

**Información del Sistema**
- PHP: x.x.x
- MySQL: x.x.x
- Navegador: x.x.x
```

## ✨ Sugerir Mejoras

### Antes de Sugerir
1. Revisar issues existentes
2. Considerar si es realmente necesario
3. Pensar en la implementación

### Template de Feature Request
```markdown
**¿Es tu feature request relacionada con un problema?**
Descripción clara del problema.

**Describe la solución que te gustaría**
Descripción clara de lo que quieres que pase.

**Describe alternativas consideradas**
Otras soluciones que has considerado.

**Contexto adicional**
Cualquier otro contexto sobre la feature request.
```

## 🔧 Tipos de Contribuciones

### 🐛 Bug Fixes
- Corregir errores existentes
- Mejorar manejo de errores
- Añadir validaciones faltantes

### ✨ Nuevas Features
- Nuevas funcionalidades
- Mejoras de UX/UI
- Optimizaciones de rendimiento

### 📚 Documentación
- Mejorar README
- Añadir comentarios al código
- Crear guías de usuario

### 🧪 Testing
- Añadir tests unitarios
- Mejorar cobertura de tests
- Documentar casos de prueba

## 🏷️ Etiquetas de Issues

- `bug`: Algo no funciona
- `enhancement`: Nueva feature o mejora
- `documentation`: Mejoras en documentación
- `good first issue`: Bueno para principiantes
- `help wanted`: Se necesita ayuda extra
- `question`: Pregunta o discusión

## 📋 Checklist para Pull Requests

- [ ] Código sigue las convenciones del proyecto
- [ ] Cambios están probados localmente
- [ ] Documentación actualizada si es necesario
- [ ] No hay conflictos con la rama principal
- [ ] Commit messages son descriptivos
- [ ] PR tiene una descripción clara

## 🎯 Roadmap de Contribuciones

### Fácil (Good First Issues)
- Mejorar documentación
- Añadir tests básicos
- Corregir typos
- Mejorar comentarios

### Intermedio
- Añadir nuevas validaciones
- Mejorar UI/UX
- Optimizar consultas SQL
- Añadir logging

### Avanzado
- Sistema de autenticación
- API REST
- Dashboard con estadísticas
- Tests de integración

## 💬 Comunicación

- **Issues**: Para bugs y feature requests
- **Discussions**: Para preguntas generales
- **Pull Requests**: Para código y documentación

## 📜 Código de Conducta

### Nuestros Compromisos
- Ser respetuosos y inclusivos
- Aceptar críticas constructivas
- Priorizar el bienestar de la comunidad
- Mostrar empatía hacia otros miembros

### Comportamiento Esperado
- Usar lenguaje acogedor e inclusivo
- Respetar diferentes puntos de vista
- Aceptar disculpas sinceras
- Enfocarse en lo que es mejor para la comunidad

## 🏆 Reconocimientos

Los contribuidores serán reconocidos en:
- README del proyecto
- Release notes
- Página de contribuidores (futuro)

---

**¡Gracias por contribuir a hacer Academia Alma Mater mejor para todos! 🎓**
