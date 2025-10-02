# Credenciales de Administrador

## Usuario Administrador Creado

Para acceder al sistema como administrador, usa estas credenciales:

- **Email:** `admin@academia.com`
- **Usuario:** `admin`  
- **Contraseña:** `admin`
- **Rol:** Administrador

## Cómo usar

1. Ve a `/academia/login.php`
2. Introduce el email: `admin@academia.com`
3. Introduce la contraseña: `admin`
4. Haz clic en "Entrar"

## Funcionalidades disponibles como admin

- ✅ Gestión completa de alumnos (crear, editar, eliminar)
- ✅ Gestión completa de asignaturas
- ✅ Exportar/Importar datos CSV
- ✅ Ver dashboard con estadísticas
- ✅ Sistema de notificaciones
- ✅ Restablecimiento de contraseñas
- ✅ Cambio de tema (claro/oscuro)

## Seguridad

⚠️ **IMPORTANTE:** Estas credenciales son para desarrollo/demo. En producción debes:

1. Cambiar la contraseña por una más segura
2. Usar un email real del administrador
3. Considerar habilitar 2FA

## Crear el usuario

Si necesitas recrear el usuario admin, ejecuta:
```
http://tu-servidor/academia/crear_admin.php
```

Esto creará automáticamente el usuario con las credenciales mencionadas.
