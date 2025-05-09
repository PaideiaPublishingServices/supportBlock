# Support Block Plugin for OJS

## Descripción
El Support Block Plugin añade un enlace de soporte técnico en el menú de navegación lateral del panel de administración de OJS (Open Journal Systems). Este plugin es desarrollado y mantenido por Paideia Studio.

## Características
- Añade un enlace de "Soporte" en el menú lateral de administración
- Funciona en todas las secciones del panel de administración
- Disponible solo para usuarios con roles de administrador o gestor
- Configurable a través de la edición del código
- Compatible con OJS 3.4.x

## Instalación
1. Descargue el archivo ZIP del plugin
2. Descomprima el archivo en la carpeta `plugins/blocks/` de su instalación de OJS
3. Inicie sesión en OJS como administrador
4. Navegue a Panel de administración → Configuración → Sitio web → Módulos
5. Busque "Bloque de Soporte" en la lista de plugins y habilítelo

## Configuración
Por defecto, el plugin está configurado para apuntar a la URL de soporte de Paideia Studio. Si desea cambiar esta URL, puede hacerlo editando el archivo `js/supportMenu.js`:

```javascript
// URL de soporte (debería ser inyectada por PHP, pero la hardcodeamos para prueba)
const supportUrl = 'https://desk.paideiastudio.net/helpdesk/soporte-tecnico-3';
