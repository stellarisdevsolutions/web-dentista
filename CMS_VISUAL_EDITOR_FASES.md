# CMS Visual de Secciones & Biblioteca de Medios (Estilo Shopify Theme Customizer)

Documento de referencia técnica y operativa para el módulo de **Editor Visual y Gestor de Secciones** de la clínica dental. Este documento resume la arquitectura, decisiones de diseño y las 4 fases de implementación para mantener el contexto del proyecto.

---

## 1. Visión General de la Arquitectura

El sistema permite a un usuario sin conocimientos técnicos editar el contenido visual de la clínica dental (textos, llamadas a la acción, fotografías y visibilidad de bloques) sin riesgo de alterar el diseño, las curvas asimétricas de Tailwind CSS ni la responsividad para dispositivos móviles.

```
┌────────────────────────────────────────────────────────────────────────────────────────┐
│ BARRA SUPERIOR: [← Panel] | Selector de Página | [Escritorio/Móvil] | Estado | [Publicar] │
├────────────────────────────┬───────────────────────────────────────────────────────────┤
│ PANEL IZQUIERDO (~380px)   │ LIENZO DERECHO (Vista Previa en Vivo 1:1)                 │
│                            │                                                           │
│ [Vista 1: Lista Secciones] │ • Iframe aislado con la web real                          │
│ • Ícono + Nombre           │ • Borde verde esmeralda punteado en hover                 │
│ • Toggle Visibilidad [Ojo] │ • Borde verde sólido (3px) e insignia flotante en click    │
│                            │ • Interceptor de enlaces para evitar salir del lienzo     │
│ [Vista 2: Formulario]      │ • Sincronización instantánea en 0ms vía postMessage       │
│ • Inputs de Texto/CTA      │                                                           │
│ • Selector de Imagen ──────┼───► [ MODAL: BIBLIOTECA DE MEDIOS TIPO WORDPRESS ]        │
└────────────────────────────┴───────────────────────────────────────────────────────────┘
```

### Principios Clave:
1. **Rendimiento Máximo y SEO Intacto:** La web pública no carga ningún constructor ni script pesado. Se sirve mediante **Blade SSR puro con Tailwind precompilado**. Cero retraso de hidratación y Core Web Vitals en verde (Lighthouse 95-100).
2. **Entorno Aislado (Iframe):** El panel de administración no contamina el CSS de la web pública ni viceversa.
3. **Seguridad Robusta:** Rutas administrativas protegidas por sesión de administrador (`auth`, middleware `admin`).

---

## 2. Las 4 Fases de Implementación

### FASE 1: Base de Datos, Modelos y Contenido Dinámico
* **Migraciones:**
  - `create_site_sections_table`: Almacena `page`, `section_key`, `name`, `icon`, `order`, `is_active`, `content` (JSON) y `settings` (JSON). Llave única compuesta: `['page', 'section_key']`.
  - `create_media_assets_table`: Almacena archivos de la biblioteca con `filename`, `file_path`, `disk` (`public`), `mime_type`, `file_size`, `width`, `height`, `alt_text` y `title`.
* **Modelos:**
  - `App\Models\SiteSection`: Casts para `content` y `settings` a array, scopes `active()` y `forPage($page)`, y helper `$section->field('key', $default)`.
  - `App\Models\MediaAsset`: Accessors `$asset->url` (resuelto contra el disco público de Storage) y `$asset->formatted_size` (KB/MB).
* **Seeder:**
  - `Database\Seeders\SiteSectionSeeder`: Carga las 8 secciones iniciales de la portada (`hero`, `quick_cards`, `about_preview`, `how_it_works`, `testimonials_cases`, `services_catalog`, `faqs`, `cta_booking`) con los textos y fotos actuales de la clínica. Registrado en `DatabaseSeeder`.

---

### FASE 2: Biblioteca de Medios (Media Library Tipo WordPress)
* **Controlador:** `App\Http\Controllers\Admin\MediaLibraryController`
  - `GET /admin/media`: Vista dedicada e independiente de la biblioteca multimedia (`standalone.blade.php`).
  - `GET /admin/media-library`: Retorna lista en JSON de archivos multimedia con soporte para búsqueda en vivo (`?search=...`).
  - `POST /admin/media-library`: Admite subida individual (`file`) o en lote/múltiple (`files[]`), validando formatos (PNG, JPG, WEBP, SVG hasta 8MB) y dimensiones (`width`, `height`).
  - `PATCH /admin/media-library/{mediaAsset}`: Actualización en caliente de título y texto alternativo (Alt Text SEO & Accesibilidad).
  - `DELETE /admin/media-library/{mediaAsset}`: Elimina el registro de la base de datos y el archivo físico del almacenamiento.
  - `POST /admin/media-library/sync`: Endpoint para sincronizar fotos preexistentes en `storage/app/public` (`clinic`, `dentists`, `services`, `blog`).
* **Comando Artisan:** `php artisan media:sync`
  - Escanea los directorios de storage y registra automáticamente las imágenes no indexadas en la tabla `media_assets`.
* **Componente Modal Reutilizable:** `resources/views/admin/media-library/modal.blade.php`
  - Controlado con **Alpine.js** (`mediaLibraryModal()`).
  - **Pestaña 1 (Imágenes Guardadas):** Cuadrícula visual con buscador en tiempo real, selección con un clic, edición inline de `title` y `alt_text`, botón para copiar URL directa al portapapeles y botón para sincronizar fotos existentes.
  - **Pestaña 2 (Subir Archivos):** Zona de arrastrar y soltar (drag & drop) compatible con selección múltiple simultánea y barra de progreso animada.

---

### FASE 3: Interfaz del Editor Visual en Pantalla Completa
* **Rutas & Controlador:**
  - `GET /admin/editor`: `App\Http\Controllers\Admin\VisualEditorController@index` (despacha la vista principal del workspace).
  - `POST /admin/editor/save`: `App\Http\Controllers\Admin\VisualEditorController@save` (persiste el estado de las secciones).
* **Vista:** `resources/views/admin/editor/index.blade.php`
  - **Barra Superior:**
    - Botón `[← Panel]` para volver al Dashboard de Filament.
    - Selector desplegable de páginas (`Inicio`, `Sobre Nosotros`, `Servicios`, `Contacto`).
    - Toggle de visualización: **Escritorio** (pantalla completa) vs **Móvil** (marco centrado vertical de 390px × 844px simulando un smartphone real).
    - Estado de cambios en tiempo real (*"Todo guardado"* en verde vs *"Cambios sin publicar"* en ámbar).
    - Botón destacado `[Publicar Cambios]` con spinner y toast de confirmación.
  - **Panel Izquierdo (~380px):**
    - **Lista de Secciones:** Navegación por bloques con íconos vectoriales y switch de visibilidad (ojo verde/gris) para prender o apagar bloques en vivo.
    - **Formulario Contextual:** Transición fluida con botón `[← Volver a Secciones]`, inputs de texto mapeados dinámicamente y botón para abrir la Biblioteca de Medios en campos de imagen.
  - **Lienzo Derecho:**
    - Iframe embebido que carga la web con `?editor_preview=1`.
    - Reactividad sin lag (0ms de latencia) mediante `window.postMessage` bidireccional.

---

### FASE 4: Integración en Vistas Públicas y Limpieza de Filament
* **Bridge de Edición Visual:** `resources/views/partials/editor-bridge.blade.php`
  - Se inyecta al final de la página **únicamente cuando está presente el parámetro `?editor_preview=1`**.
  - Dibuja un **borde verde esmeralda suave punteado** al pasar el ratón (hover) sobre cualquier sección `[data-cms-section]`.
  - Al hacer clic, dibuja un **borde verde esmeralda sólido de 3px**, crea la insignia flotante `[EDITAR: ...]` y envía un mensaje al panel izquierdo para abrir su formulario.
  - Intercepta clics en enlaces (`<a>`) y formularios para evitar salir del lienzo por error.
* **Conexión en `welcome.blade.php`:**
  - Las 8 secciones leen sus valores de `$sections` con fallback a los textos por defecto.
  - Atributos semánticos `data-cms-section` y `data-cms-field` agregados en cada bloque editable.
* **Limpieza del Sidebar de Filament:**
  - Ocultados del menú los recursos fragmentados de contenido estático:
    - `BannerResource::$shouldRegisterNavigation = false;`
    - `SmileCaseResource::$shouldRegisterNavigation = false;`
    - `TestimonialResource::$shouldRegisterNavigation = false;`
    - `FaqResource::$shouldRegisterNavigation = false;`
  - Registrado el ítem de navegación de primer nivel **"Editor Web"** (ícono pincel) en `App\Providers\Filament\AdminPanelProvider`.

---

## 3. Pruebas Automatizadas y Verificación

El módulo cuenta con cobertura total de pruebas automatizadas en PHPUnit:
* `tests/Feature/VisualEditorTest.php`:
  - `admin_can_access_visual_editor`: Acceso exitoso para administradores.
  - `non_admin_cannot_access_visual_editor`: Rechazo 403 para usuarios no administradores.
  - `guest_is_redirected_to_login`: Redirección automática al login para usuarios sin autenticar.
  - `admin_can_save_section_updates_and_reflects_on_landing_page`: Persistencia de cambios y reflejo inmediato en la landing page pública.
  - `landing_page_embeds_editor_bridge_when_preview_param_present`: Inyección correcta del bridge visual en modo preview.
* `tests/Feature/MediaLibraryTest.php`:
  - `admin_can_list_media_assets`: Listado de archivos multimedia en JSON.
  - `admin_can_upload_image_to_media_library`: Subida y almacenamiento de imágenes con cálculo de dimensiones.
  - `admin_can_delete_media_asset`: Eliminación segura de archivo físico y registro en base de datos.
  - `non_admin_cannot_access_media_library`: Protección contra accesos no autorizados.

### Comandos de Verificación:
```bash
# Ejecutar pruebas del editor y biblioteca de medios
php artisan test --filter=VisualEditorTest
php artisan test --filter=MediaLibraryTest

# Ejecutar la suite completa del proyecto
php artisan test --compact

# Formatear el código según las convenciones de Laravel
vendor/bin/pint --dirty --format agent
```
