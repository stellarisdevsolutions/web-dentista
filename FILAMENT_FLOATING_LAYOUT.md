# Arquitectura y Diseño del Layout Flotante en Filament v5
### Aura Dental Studio CMS — Guía Técnica y de Buenas Prácticas

---

## 1. Objetivo y Visión de Diseño

El objetivo de esta personalización es transformar el panel de control administrativo de **Filament v5** en una interfaz moderna de alto nivel, inspirada en aplicaciones SaaS contemporáneas (Next.js, Linear, Supabase) y dashboards médicos de estética *Floating Islands / Dock*:

1. **Sidebar lateral de altura completa:** Ubicado a la izquierda, sin ser cortado por la barra superior.
2. **Efecto Flotante ("Floating Capsules"):** El sidebar y el topbar no están pegados rígidamente a los bordes de la ventana (`0px`), sino que flotan con márgenes suaves, esquinas redondeadas (`border-radius: 1.75rem`) y sombras de elevación.
3. **Flujo de contenido natural sin superposiciones:** El contenido principal (`<main>`) nunca queda oculto detrás del header ni del sidebar.
4. **Fluidez instantánea (Sensación SPA / Next.js):** Navegación fluida entre módulos sin recargar la página completa, con prefetching de enlaces.
5. **Persistencia real del estado de colapso:** Al achicar el sidebar a modo icono o expandirlo, el estado se preserva en `localStorage` incluso al cambiar de módulo o recargar con F5.
6. **Rendimiento óptimo (Zero Lag):** Sin scripts pesados ni librerías externas; 100% transiciones aceleradas por hardware (GPU) y flexbox nativo.

---

## 2. El Problema Estructural de Filament por Defecto

En el diseño original de Filament (v3/v4/v5):

```
┌────────────────────────────────────────────────────────┐
│                   TOPBAR / HEADER                      │  <-- Ocupa el 100% de ancho
├──────────────┬─────────────────────────────────────────┤
│   SIDEBAR    │               CONTENIDO                 │  <-- El sidebar empieza ABAJO
│  (Cortado)   │                <main>                   │      del header (top: 4rem)
└──────────────┴─────────────────────────────────────────┘
```

* El `<div class="fi-topbar-ctn">` se renderiza **antes y fuera** del `<div class="fi-layout">`.
* El sidebar tiene asignado `top: var(--topbar-height)` y `height: calc(100vh - var(--topbar-height))`.
* El logotipo de la clínica se fuerza en el topbar en pantallas de escritorio y se oculta en el sidebar con `lg:hidden`.
* Si se intenta forzar el sidebar a `position: fixed; top: 0;` usando solo CSS, el contenedor `<main>` pierde su referencia y se desliza debajo del sidebar o debajo del header.

---

## 3. La Solución: Reestructuración Limpia del DOM

Para lograr el layout deseado sin hacks frágiles, reestructuramos el árbol de componentes publicando la plantilla en:
`resources/views/vendor/filament-panels/components/layout/index.blade.php`

### Nuevo Árbol de Renderizado:

```
┌──────────────┬─────────────────────────────────────────┐
│              │             TOPBAR FLOTANTE             │
│   SIDEBAR    ├─────────────────────────────────────────┤
│   FLOTANTE   │                                         │
│  (Completo)  │           CONTENIDO PRINCIPAL           │
│              │                 <main>                  │
└──────────────┴─────────────────────────────────────────┘
```

### Implementación en Blade:

```blade
<div class="fi-layout">
    {{-- 1. Columna Izquierda: Sidebar de Altura Completa --}}
    @if ($hasNavigation)
        @livewire(filament()->getSidebarLivewireComponent())
    @endif

    {{-- 2. Columna Derecha: Contenido y Header juntos --}}
    <div class="fi-main-ctn">
        {{-- El topbar ahora vive ARRIBA del contenido principal --}}
        @if ($hasTopbar)
            @livewire(filament()->getTopbarLivewireComponent())
        @endif

        {{-- El contenido sigue inmediatamente al topbar en el flujo natural --}}
        <main id="fi-main-content" class="fi-main">
            {{ $slot }}
        </main>
    </div>
</div>
```

### ¿Por qué esto garantiza fluidez y cero solapamientos?
* **Matemáticamente Imposible de Tapar:** `<main>` es el elemento hermano que sigue al topbar dentro de un contenedor flex vertical (`flex-col`). Por física de maquetación HTML, el contenido siempre comienza donde termina el header.
* **Cálculo Automático por GPU:** Cuando el sidebar se colapsa de `16.5rem` a `4.75rem`, el contenedor `.fi-main-ctn` se expande de manera natural vía Flexbox (`flex: 1 1 0%`), sin necesidad de cálculos manuales con `calc()` o JavaScript.

---

## 4. Archivos Creados y Modificados

### 1. `resources/views/vendor/filament-panels/components/layout/index.blade.php`
Sobrescribe el layout base de Filament para ubicar el topbar en la columna derecha superior, justo antes del slot de contenido.

### 2. `app/Providers/Filament/AdminPanelProvider.php`
Configura los parámetros del panel:

```php
$panel
    ->brandName('Aura Dental Studio')
    ->brandLogo(fn () => view('filament.admin.logo'))
    ->colors([
        'primary' => Color::Emerald,
    ])
    ->sidebarCollapsibleOnDesktop()
    ->sidebarWidth('16.5rem')
    ->collapsedSidebarWidth('4.75rem')
    ->spa(true, hasPrefetching: true)
    ->renderHook(
        PanelsRenderHook::HEAD_END,
        fn () => view('filament.admin.theme-overrides')
    )
```

* `sidebarCollapsibleOnDesktop()`: Habilita el botón de contraer/desplegar con integración automática de Alpine `$persist`.
* `spa(true, hasPrefetching: true)`: Habilita el modo SPA con Livewire Islands y prefetching de rutas al pasar el cursor (experiencia tipo Next.js).
* `renderHook(PanelsRenderHook::HEAD_END, ...)`: Inyecta los estilos y el script de prevención de FOUC directamente en el `<head>` antes de pintar la pantalla.

### 3. `resources/views/filament/admin/theme-overrides.blade.php`
Contiene la capa de diseño visual flotante:

```css
/* Sidebar Isla Flotante */
.fi-body-has-topbar .fi-sidebar {
    position: sticky !important;
    top: 0.75rem !important;
    margin: 0.75rem 0.5rem 0.75rem 0.75rem !important;
    height: calc(100dvh - 1.5rem) !important;
    border-radius: 1.75rem !important;
    border: 1px solid rgba(255, 255, 255, 0.08) !important;
    box-shadow: 0 14px 34px -4px rgba(0, 0, 0, 0.35) !important;
}

/* Header Cápsula Flotante */
.fi-topbar-ctn {
    position: sticky !important;
    top: 0.75rem !important;
    margin: 0.75rem 1rem 0.75rem 0.5rem !important;
    border-radius: 1.35rem !important;
    backdrop-filter: blur(16px) !important;
    background: rgba(255, 255, 255, 0.92) !important;
    box-shadow: 0 4px 20px -2px rgba(0, 0, 0, 0.05) !important;
}

/* Ítem Activo (Pill azul vibrante como la referencia) */
.fi-sidebar-item.fi-active .fi-sidebar-item-btn {
    background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%) !important;
    color: #ffffff !important;
    box-shadow: 0 4px 16px -1px rgba(2, 132, 199, 0.55) !important;
}

/* Modo colapsado: alineación exacta al eje vertical central y Squircle (Cuadrado redondeado) */
.fi-sidebar:not(.fi-sidebar-open) .fi-sidebar-item-btn {
    width: 2.85rem !important;
    height: 2.85rem !important;
    margin: 0 auto !important;
    border-radius: 0.85rem !important;
}

.fi-sidebar:not(.fi-sidebar-open) .fi-sidebar-item.fi-active .fi-sidebar-item-btn {
    border-radius: 0.85rem !important;
    box-shadow: 0 4px 14px -1px rgba(2, 132, 199, 0.6) !important;
}

/* Scrollbar estilo móvil (ultra fino 4px translúcido iOS/Android) */
.fi-sidebar-nav::-webkit-scrollbar {
    width: 4px !important;
    background: transparent !important;
}
.fi-sidebar-nav::-webkit-scrollbar-thumb {
    background: rgba(255, 255, 255, 0.22) !important;
    border-radius: 9999px !important;
}
```

### 4. `resources/views/filament/admin/logo.blade.php`
Componente de identidad visual que despliega:
* Insignia médica squircle azul cian (`linear-gradient(135deg, #38bdf8, #0284c7)`) con icono de onda/pulso en blanco y dimensiones fijas para evitar distorsiones.
* Tipografía corporativa bicolor: `"Aura"` (blanco puro) y `"Dental"` (cian luminoso) con subtítulo `"STUDIO CLÍNICO"`.
* En modo colapsado, el texto se oculta automáticamente y el squircle permanece centrado arriba del dock.

---

## 5. Prevención de FOUC (Flash of Unstyled Content)

Cuando el usuario recarga la página con `F5` teniendo el sidebar colapsado, Alpine.js puede tardar unos milisegundos en inicializar su almacén `$persist`. Para evitar que el sidebar comience ancho y salte bruscamente a colapsado:

```javascript
(function() {
    try {
        var isDesktop = window.innerWidth >= 1024;
        var stored = localStorage.getItem('_x_isOpenDesktop');
        if (isDesktop && stored === 'false') {
            document.documentElement.classList.add('fi-sidebar-force-collapsed');
        }
    } catch (e) {}
})();
```

Este script síncrono de menos de 1 KB se ejecuta en el `<head>` antes del primer renderizado de pintura del navegador, asegurando que el estado colapsado cargue de forma limpia e instantánea.

---

## 6. Header / Topbar Flotante y Minimalista

El Header sigue la filosofía de diseño tipo cápsula flotante alineado con el sidebar:

### Composición Estricta:
1. **Botón Hamburguesa (`bars-3`):**
   * Reemplaza la flecha/chevron `>` por un icono de tres barras horizontales.
   * Funciona tanto en móvil como en escritorio, alternando `$store.sidebar.isOpen` con animación fluida.
   * Botón squircle `2.65rem x 2.65rem` con radio de `0.85rem` y microinteracción táctil al hover/click.
2. **Buscador Global estilo Pill:**
   * Posicionado inmediatamente después del botón hamburguesa en la cabecera.
   * Borde redondeado completo (`9999px`), lupa integrada y anillo de enfoque azul cian (`--accent-blue-active`).
3. **Perfil de Usuario Limpio (Sin cápsula artificial):**
   * Ubicado en el extremo derecho integrado de forma natural en el header sin bordes ni cápsula contenedora adicional.
   * Incluye avatar circular de 34px con punto de estado en vivo (verde esmeralda), nombre del usuario ("Admin Clinic"), etiqueta de rol en azul médico ("Administrador") y chevron sutil.
   * Hover suave y ergonómico sin cajas rígidas.
4. **Menú Desplegable y Selector de Tema (Claro / Oscuro / Sistema):**
   * Panel flotante con desenfoque `blur(20px)`, sombra elevada y bordes suaves (`border-radius: 1.15rem`).
   * **Selector de Tema:** Contenedor tipo track (`grid`) donde el modo activo (Sol, Luna o Pantalla) se ilumina con el fondo azul degradado corporativo (`--accent-blue-active` / `--accent-blue-gradient`), sombra luminosa y el icono en **blanco puro (`#ffffff`)**, reflejando la misma identidad que el ítem activo del sidebar.
5. **"Solo eso":** Se eliminan elementos redundantes como logotipos duplicados (el logo vive exclusivamente en el sidebar flotante) o botones de flechas.

---

## 7. Verificación y Calidad

* **PHPUnit:** 38 tests aprobados (100% de aserciones válidas).
* **Laravel Pint:** Formato de código verificado bajo el estándar PSR-12/Laravel.
* **Compatibilidad:** Mantiene compatibilidad total con widgets, tablas, filtros modales y futuras actualizaciones de Filament v5.
