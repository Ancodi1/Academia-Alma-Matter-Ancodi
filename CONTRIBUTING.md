# Guía de contribución

Gracias por contribuir a Academia Alma Mater. Este proyecto es una aplicación PHP/MySQL clásica, con controladores en `controllers/`, utilidades en `models/`, vistas reutilizables en `views/` y páginas PHP en la raíz.

## Flujo recomendado

1. Crea una rama descriptiva:

```bash
git checkout -b feature/nombre-corto
```

2. Levanta el entorno:

```bash
docker compose up -d --build
```

3. Realiza cambios pequeños y verificables.

4. Ejecuta comprobaciones antes de abrir PR:

```bash
find . -name "*.php" -not -path "./vendor/*" -exec php -l {} \;
php pruebas/pruebaBaseDatos.php
```

5. Sube la rama y abre Pull Request con contexto claro.

## Convenciones de código

- Mantén el estilo PHP existente del repositorio.
- Usa nombres en español cuando el módulo ya los use.
- Prefiere consultas preparadas con `preparar()` para entradas de usuario.
- Valida y normaliza datos antes de guardar.
- Escapa salida HTML con `htmlspecialchars`.
- Añade `csrf_token` en formularios que modifican datos.
- Protege acciones internas con `requerirInterno()` o `requerirAdmin()`.
- No mezcles refactors grandes con cambios funcionales pequeños.

## Base de datos

La base inicial está en `almamater.sql`. Las evoluciones incrementales viven en:

- `database_schema_updates.sql`
- `database_priority_medium_updates.sql`
- `database_advanced_updates.sql`
- `database_review_fixes.sql`

Cuando añadas tablas, columnas o índices:

- Crea SQL idempotente siempre que sea posible.
- Evita romper datos existentes.
- Documenta el orden de aplicación si importa.
- Actualiza `docker-compose.yml` si el nuevo SQL debe cargarse en entornos limpios.

## Seguridad

Antes de enviar cambios revisa:

- Formularios con token CSRF.
- Roles correctos en páginas y controladores.
- Consultas preparadas para todo dato externo.
- Validación de archivos por extensión, MIME y tamaño.
- Rutas de descarga dentro de `uploads/`.
- Ausencia de credenciales reales en commits.

## Documentación

Actualiza documentación cuando cambies:

- Instalación o despliegue.
- Variables de entorno.
- Tablas o migraciones requeridas.
- Roles y permisos.
- Endpoints de API.
- Flujo de usuario visible.

Archivos principales:

- `README.md`
- `DEPLOYMENT.md`
- `CHANGELOG.md`
- `.github/ISSUE_TEMPLATE/*.md`

## Pull Requests

Incluye en la descripción:

- Qué cambia.
- Por qué cambia.
- Cómo se probó.
- Capturas si afecta a interfaz.
- SQL necesario si aplica.

Checklist:

- [ ] Sintaxis PHP comprobada.
- [ ] Base de datos actualizada o sin cambios de esquema.
- [ ] Permisos y roles revisados.
- [ ] Documentación actualizada si aplica.
- [ ] No se incluyen credenciales ni datos privados.

## Issues

Para bugs, indica:

- Ruta o pantalla afectada.
- Rol usado.
- Pasos para reproducir.
- Resultado esperado y resultado real.
- Versión de PHP y MySQL.
- Logs relevantes si existen.

Para mejoras, indica:

- Problema que resuelve.
- Usuarios afectados.
- Comportamiento esperado.
- Impacto en datos, permisos o despliegue.

## Commits

Usa mensajes cortos y descriptivos. Ejemplos:

```text
feat: añadir filtro de profesor al calendario
fix: validar csrf en actualización de pagos
docs: actualizar guía de despliegue docker
```
