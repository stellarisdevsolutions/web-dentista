<script>
    /* Pre-apply sidebar collapsed class from localStorage to eliminate FOUC */
    (function() {
        try {
            var isDesktop = window.innerWidth >= 1024;
            var stored = localStorage.getItem('_x_isOpenDesktop');
            if (isDesktop && stored === 'false') {
                document.documentElement.classList.add('fi-sidebar-force-collapsed');
            }
        } catch (e) {}
    })();
</script>

<style>
    /* ==========================================================================
       AURA DENTAL STUDIO - MODERN NAVY MEDICAL THEME (POLISHED SQUIRCLE DOCK)
       ========================================================================== */

    :root {
        --sidebar-width: 17rem;
        --collapsed-sidebar-width: 4.75rem;
        --sidebar-bg: #0b172a;               /* Deep midnight medical navy blue */
        --sidebar-bg-hover: rgba(255, 255, 255, 0.08);
        --sidebar-border: rgba(255, 255, 255, 0.08);
        --accent-blue-active: #0284c7;        /* Sky-600 / Vibrant ocean blue */
        --accent-blue-gradient: linear-gradient(135deg, #0284c7 0%, #0369a1 100%);
        --accent-cyan: #38bdf8;
    }

    /* Fondo general del canvas tipo aplicación moderna */
    .fi-body {
        background-color: #f1f5f9 !important;
    }

    .dark .fi-body {
        background-color: #050b14 !important;
    }

    /* --------------------------------------------------------------------------
       1. SCROLL ESTILO CELULAR (SLIM MOBILE OVERLAY SCROLLBAR - iOS / ANDROID)
       -------------------------------------------------------------------------- */
    /* Scroll en el Sidebar (transparente con cápsula translúcida fina) */
    .fi-sidebar-nav::-webkit-scrollbar {
        width: 4px !important;
        height: 4px !important;
        background: transparent !important;
    }

    .fi-sidebar-nav::-webkit-scrollbar-track {
        background: transparent !important;
    }

    .fi-sidebar-nav::-webkit-scrollbar-thumb {
        background: rgba(255, 255, 255, 0.22) !important;
        border-radius: 9999px !important;
    }

    .fi-sidebar-nav::-webkit-scrollbar-thumb:hover {
        background: rgba(255, 255, 255, 0.45) !important;
    }

    .fi-sidebar-nav {
        scrollbar-width: thin !important;
        scrollbar-color: rgba(255, 255, 255, 0.22) transparent !important;
    }

    /* Scroll en la Página Principal y Widgets */
    ::-webkit-scrollbar {
        width: 5px !important;
        height: 5px !important;
        background: transparent !important;
    }

    ::-webkit-scrollbar-track {
        background: transparent !important;
    }

    ::-webkit-scrollbar-thumb {
        background: rgba(148, 163, 184, 0.35) !important;
        border-radius: 9999px !important;
    }

    ::-webkit-scrollbar-thumb:hover {
        background: rgba(100, 116, 139, 0.6) !important;
    }

    html, body {
        scrollbar-width: thin !important;
        scrollbar-color: rgba(148, 163, 184, 0.35) transparent !important;
    }

    /* --------------------------------------------------------------------------
       2. SIDEBAR FLOTANTE (CÁPSULA CON HEADER FIJO ARRIBA)
       -------------------------------------------------------------------------- */
    .fi-sidebar {
        background-color: var(--sidebar-bg) !important;
        display: flex !important;
        flex-direction: column !important;
    }

    @media (min-width: 1024px) {
        /* Sidebar estilo Isla / Cápsula flotante */
        .fi-body-has-topbar .fi-sidebar {
            position: sticky !important;
            top: 0.75rem !important;
            margin: 0.75rem 0.5rem 0.75rem 0.75rem !important;
            height: calc(100dvh - 1.5rem) !important;
            border-radius: 1.75rem !important;
            border: 1px solid var(--sidebar-border) !important;
            box-shadow: 0 16px 36px -4px rgba(2, 6, 23, 0.5), 0 0 0 1px rgba(255, 255, 255, 0.05) !important;
            z-index: 30 !important;
            transition: width 0.25s cubic-bezier(0.4, 0, 0.2, 1) !important;
            overflow: hidden !important;  /* El contenedor no scrollea: el scroll es solo interno del nav */
        }

        /* Encabezado del sidebar FIJO en la parte superior (NUNCA se desplaza con el scroll) */
        .fi-sidebar-header-ctn {
            flex-shrink: 0 !important;
            background-color: var(--sidebar-bg) !important;
            border-bottom: 1px solid var(--sidebar-border) !important;
            z-index: 10 !important;
        }

        .fi-body-has-topbar .fi-sidebar-header {
            display: flex !important;
            height: 5rem !important;
            padding-inline: 1.25rem !important;
            align-items: center !important;
            justify-content: flex-start !important;
            background-color: transparent !important;
            box-shadow: none !important;
            border-bottom: none !important;
        }

        /* Ocultar logo repetido en el header superior en desktop */
        .fi-topbar-start .fi-logo {
            display: none !important;
        }

        /* Área de navegación interna con scroll estilo móvil */
        .fi-sidebar-nav {
            flex: 1 1 0% !important;
            overflow-y: auto !important;
            overflow-x: hidden !important;
            padding-inline: 0.75rem !important;
            padding-top: 1rem !important;
            padding-bottom: 1.5rem !important;
        }
    }

    /* CONTROL DEL LOGO EN FILAMENT: EVITAR QUE SE APLASTE O SE DISTORSIÓNE */
    .fi-logo {
        height: auto !important;
        max-height: none !important;
        width: 100% !important;
        display: flex !important;
        align-items: center !important;
    }

    .fi-sidebar-header-logo-ctn {
        display: flex !important;
        align-items: center !important;
        width: 100% !important;
    }

    .fi-sidebar-header-logo-ctn a {
        display: flex !important;
        align-items: center !important;
        text-decoration: none !important;
        width: 100% !important;
    }

    .aura-logo-badge svg {
        width: 22px !important;
        height: 22px !important;
        min-width: 22px !important;
        min-height: 22px !important;
        max-width: 22px !important;
        max-height: 22px !important;
        stroke: #ffffff !important;
    }

    /* OCULTAR USUARIO ADMINISTRADOR EN EL FOOTER DEL SIDEBAR (va en el Header) */
    .fi-sidebar-footer,
    .fi-sidebar .fi-user-menu {
        display: none !important;
    }

    /* --------------------------------------------------------------------------
       3. TOPBAR / HEADER FLOTANTE (DOCK CAPSULE: HAMBURGUESA + BUSCADOR + USUARIO)
       -------------------------------------------------------------------------- */
    .fi-topbar-ctn {
        position: sticky !important;
        top: 0.75rem !important;
        margin: 0.75rem 1rem 0.75rem 0.5rem !important;
        border-radius: 1.25rem !important;
        border: 1px solid rgba(226, 232, 240, 0.85) !important;
        background: rgba(255, 255, 255, 0.95) !important;
        backdrop-filter: blur(16px) !important;
        -webkit-backdrop-filter: blur(16px) !important;
        box-shadow: 0 4px 20px -2px rgba(15, 23, 42, 0.05), 0 1px 3px 0 rgba(15, 23, 42, 0.03) !important;
        z-index: 25 !important;
        transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1) !important;
    }

    .dark .fi-topbar-ctn {
        border-color: rgba(255, 255, 255, 0.08) !important;
        background: rgba(15, 23, 42, 0.88) !important;
        box-shadow: 0 10px 30px -5px rgba(0, 0, 0, 0.3) !important;
    }

    .fi-topbar {
        display: flex !important;
        align-items: center !important;
        justify-content: space-between !important;
        width: 100% !important;
        background: transparent !important;
        box-shadow: none !important;
        ring-width: 0 !important;
        border: none !important;
        height: 4.25rem !important;
        padding-inline: 1rem !important;
    }

    .fi-topbar-start {
        display: flex !important;
        align-items: center !important;
        gap: 0.85rem !important;
        flex: 1 1 auto !important;
        min-width: 0 !important;
    }

    /* 1. Botón de Hamburguesa Flotante */
    .fi-topbar-hamburger-btn {
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        width: 2.65rem !important;
        height: 2.65rem !important;
        min-width: 2.65rem !important;
        border-radius: 0.85rem !important;
        background-color: #f8fafc !important;
        border: 1px solid rgba(226, 232, 240, 0.9) !important;
        color: #475569 !important;
        cursor: pointer !important;
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1) !important;
        box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.03) !important;
    }

    .fi-topbar-hamburger-btn:hover {
        background-color: #f1f5f9 !important;
        border-color: rgba(2, 132, 199, 0.4) !important;
        color: #0284c7 !important;
        transform: scale(1.04) !important;
        box-shadow: 0 2px 8px -2px rgba(2, 132, 199, 0.2) !important;
    }

    .fi-topbar-hamburger-btn:active {
        transform: scale(0.96) !important;
    }

    .dark .fi-topbar-hamburger-btn {
        background-color: rgba(30, 41, 59, 0.7) !important;
        border-color: rgba(255, 255, 255, 0.08) !important;
        color: #cbd5e1 !important;
    }

    .dark .fi-topbar-hamburger-btn:hover {
        background-color: rgba(51, 65, 85, 0.8) !important;
        color: #38bdf8 !important;
        border-color: rgba(56, 189, 248, 0.4) !important;
    }

    .fi-topbar-hamburger-icon {
        width: 1.35rem !important;
        height: 1.35rem !important;
        transition: transform 0.2s ease !important;
    }

    /* 2. Buscador Global Estilo Pill */
    .fi-topbar-search-ctn {
        display: flex !important;
        align-items: center !important;
        flex: 1 1 auto !important;
        max-width: 28rem !important;
        min-width: 11rem !important;
    }

    .fi-topbar-search-ctn .fi-global-search-ctn,
    .fi-topbar-search-ctn .fi-global-search,
    .fi-topbar-search-ctn .fi-global-search-field {
        width: 100% !important;
    }

    .fi-topbar-search-ctn .fi-input-wrp {
        border-radius: 9999px !important;
        background-color: #f8fafc !important;
        border: 1px solid rgba(226, 232, 240, 0.9) !important;
        box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.03) !important;
        transition: all 0.2s ease !important;
        height: 2.65rem !important;
    }

    .fi-topbar-search-ctn input[type="search"] {
        height: 2.65rem !important;
        font-size: 0.875rem !important;
        padding-inline-start: 0.5rem !important;
    }

    .dark .fi-topbar-search-ctn .fi-input-wrp {
        background-color: rgba(30, 41, 59, 0.7) !important;
        border-color: rgba(255, 255, 255, 0.08) !important;
        color: #f8fafc !important;
    }

    .fi-topbar-search-ctn .fi-input-wrp:focus-within {
        background-color: #ffffff !important;
        border-color: var(--accent-blue-active) !important;
        box-shadow: 0 0 0 3px rgba(2, 132, 199, 0.18) !important;
    }

    .dark .fi-topbar-search-ctn .fi-input-wrp:focus-within {
        background-color: rgba(15, 23, 42, 0.95) !important;
        border-color: var(--accent-blue-active) !important;
    }

    /* 3. Menú de Usuario a la Derecha */
    .fi-topbar-end {
        display: flex !important;
        align-items: center !important;
        gap: 0.75rem !important;
        margin-inline-start: auto !important;
        flex-shrink: 0 !important;
    }

    .fi-user-menu-topbar-pill {
        display: inline-flex !important;
        align-items: center !important;
        gap: 0.65rem !important;
        height: 2.65rem !important;
        padding: 0.2rem 0.5rem !important;
        border-radius: 0.85rem !important;
        background-color: transparent !important;
        border: none !important;
        cursor: pointer !important;
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1) !important;
        box-shadow: none !important;
    }

    .fi-user-menu-topbar-pill:hover {
        background-color: rgba(241, 245, 249, 0.75) !important;
        border: none !important;
        box-shadow: none !important;
        transform: none !important;
    }

    .fi-user-menu-topbar-pill:active {
        transform: scale(0.98) !important;
    }

    .dark .fi-user-menu-topbar-pill {
        background-color: transparent !important;
        border: none !important;
        box-shadow: none !important;
    }

    .dark .fi-user-menu-topbar-pill:hover {
        background-color: rgba(255, 255, 255, 0.06) !important;
        border: none !important;
    }

    .fi-user-avatar-wrapper {
        position: relative !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
    }

    .fi-user-avatar-wrapper img,
    .fi-user-avatar-wrapper .fi-avatar {
        width: 2.15rem !important;
        height: 2.15rem !important;
        border-radius: 9999px !important;
        object-fit: cover !important;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08) !important;
    }

    .fi-user-status-dot {
        position: absolute !important;
        bottom: 0 !important;
        right: 0 !important;
        width: 0.55rem !important;
        height: 0.55rem !important;
        background-color: #10b981 !important;
        border: 2px solid #ffffff !important;
        border-radius: 9999px !important;
    }

    .dark .fi-user-status-dot {
        border-color: #1e293b !important;
    }

    .fi-user-info-text {
        display: flex !important;
        flex-direction: column !important;
        text-align: left !important;
        line-height: 1.15 !important;
    }

    @media (max-width: 640px) {
        .fi-user-info-text {
            display: none !important;
        }
    }

    .fi-user-name {
        font-size: 0.825rem !important;
        font-weight: 600 !important;
        color: #1e293b !important;
        white-space: nowrap !important;
    }

    .dark .fi-user-name {
        color: #f1f5f9 !important;
    }

    .fi-user-role {
        font-size: 0.685rem !important;
        font-weight: 600 !important;
        color: #0284c7 !important;
        letter-spacing: 0.02em !important;
    }

    .dark .fi-user-role {
        color: #38bdf8 !important;
    }

    .fi-user-chevron-icon {
        width: 0.85rem !important;
        height: 0.85rem !important;
        color: #94a3b8 !important;
        transition: transform 0.2s ease, color 0.2s ease !important;
    }

    .fi-user-menu-topbar-pill:hover .fi-user-chevron-icon {
        color: #0284c7 !important;
        transform: translateY(1px) !important;
    }

    .dark .fi-user-menu-topbar-pill:hover .fi-user-chevron-icon {
        color: #38bdf8 !important;
    }

    /* --------------------------------------------------------------------------
       DROPDOWN PANEL & THEME SWITCHER (ESTILO AURA CLÍNICO MODERNO)
       -------------------------------------------------------------------------- */
    .fi-dropdown-panel {
        border-radius: 1.15rem !important;
        border: 1px solid rgba(226, 232, 240, 0.95) !important;
        background: rgba(255, 255, 255, 0.98) !important;
        backdrop-filter: blur(20px) !important;
        -webkit-backdrop-filter: blur(20px) !important;
        box-shadow: 0 14px 40px -4px rgba(15, 23, 42, 0.12), 0 4px 12px -2px rgba(15, 23, 42, 0.05) !important;
        padding: 0.45rem !important;
        min-width: 14rem !important;
        z-index: 50 !important;
    }

    .dark .fi-dropdown-panel {
        border-color: rgba(255, 255, 255, 0.08) !important;
        background: rgba(15, 23, 42, 0.96) !important;
        box-shadow: 0 16px 42px -4px rgba(0, 0, 0, 0.5) !important;
    }

    .fi-dropdown-list-item {
        border-radius: 0.75rem !important;
        padding: 0.6rem 0.85rem !important;
        font-weight: 500 !important;
        font-size: 0.875rem !important;
        transition: all 0.15s ease !important;
    }

    .fi-dropdown-list-item:hover {
        background-color: rgba(241, 245, 249, 0.85) !important;
        color: #0284c7 !important;
    }

    .dark .fi-dropdown-list-item:hover {
        background-color: rgba(255, 255, 255, 0.06) !important;
        color: #38bdf8 !important;
    }

    /* Track contenedor del selector de tema (Modo claro, oscuro, máquina) */
    .fi-theme-switcher {
        display: grid !important;
        grid-template-columns: repeat(3, minmax(0, 1fr)) !important;
        gap: 0.3rem !important;
        background-color: #f1f5f9 !important;
        padding: 0.25rem !important;
        border-radius: 0.85rem !important;
        margin: 0.35rem 0.25rem !important;
        border: 1px solid rgba(226, 232, 240, 0.8) !important;
    }

    .dark .fi-theme-switcher {
        background-color: rgba(30, 41, 59, 0.75) !important;
        border-color: rgba(255, 255, 255, 0.06) !important;
    }

    /* Botones de selección de tema */
    .fi-theme-switcher-btn {
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        height: 2.25rem !important;
        border-radius: 0.65rem !important;
        color: #64748b !important;
        background: transparent !important;
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1) !important;
        cursor: pointer !important;
    }

    .fi-theme-switcher-btn:hover {
        background-color: rgba(255, 255, 255, 0.8) !important;
        color: #0284c7 !important;
    }

    .dark .fi-theme-switcher-btn {
        color: #94a3b8 !important;
    }

    .dark .fi-theme-switcher-btn:hover {
        background-color: rgba(255, 255, 255, 0.08) !important;
        color: #38bdf8 !important;
    }

    /* Botón de tema ACTIVO: Fondo azul gradiente + icono blanco puro */
    .fi-theme-switcher-btn.fi-active,
    .fi-theme-switcher-btn[aria-pressed="true"] {
        background: var(--accent-blue-gradient) !important;
        background-color: var(--accent-blue-active) !important;
        color: #ffffff !important;
        box-shadow: 0 4px 12px -1px rgba(2, 132, 199, 0.45) !important;
    }

    .fi-theme-switcher-btn.fi-active svg,
    .fi-theme-switcher-btn.fi-active .fi-icon,
    .fi-theme-switcher-btn[aria-pressed="true"] svg,
    .fi-theme-switcher-btn[aria-pressed="true"] .fi-icon {
        color: #ffffff !important;
        stroke: #ffffff !important;
        opacity: 1 !important;
    }

    /* Separación entre grupos de navegación (Control compacto del espaciado) */
    .fi-sidebar-nav-groups {
        gap: 0.35rem !important;
    }

    /* Solo cuando el sidebar está expandido */
    .fi-sidebar-open .fi-sidebar-group {
        margin-bottom: 0.25rem !important;
        gap: 0.25rem !important;
    }

    /* Cuando el grupo está cerrado (plegado) con sidebar abierto: espaciado compacto */
    .fi-sidebar-open .fi-sidebar-group.fi-collapsed {
        margin-bottom: 0.2rem !important;
        gap: 0 !important;
    }

    /* Botón del encabezado del grupo */
    .fi-sidebar-group-btn {
        padding-block: 0.35rem !important;
        padding-inline: 0.5rem !important;
        border-radius: 0.65rem !important;
        transition: background-color 0.15s ease !important;
    }

    .fi-sidebar-group-btn:hover {
        background-color: rgba(255, 255, 255, 0.04) !important;
    }

    /* Cuando el grupo está abierto (desplegado) con sidebar abierto: separación armónica inferior */
    .fi-sidebar-open .fi-sidebar-group:not(.fi-collapsed) {
        margin-bottom: 0.85rem !important;
    }

    /* Títulos de grupos (GESTIÓN CLÍNICA, CMS Y CONTENIDO WEB, etc.) */
    .fi-sidebar-group-label {
        color: #64748b !important;
        font-size: 0.72rem !important;
        font-weight: 700 !important;
        letter-spacing: 0.08em !important;
        text-transform: uppercase !important;
        padding-inline: 0.25rem !important;
    }

    .fi-sidebar-group-collapse-btn {
        color: #64748b !important;
    }

    /* Links inactivos (Nombre del módulo e icono MÁS CLAROS) */
    .fi-sidebar-item-btn {
        border-radius: 0.95rem !important;
        padding: 0.7rem 0.95rem !important;
        color: #f1f5f9 !important;             /* Texto claro y nítido */
        font-weight: 500 !important;
        font-size: 0.9rem !important;
        transition: all 0.15s ease-in-out !important;
    }

    .fi-sidebar-item-btn:hover {
        background-color: var(--sidebar-bg-hover) !important;
        color: #ffffff !important;
    }

    /* Iconos inactivos claros y luminosos */
    .fi-sidebar-item-btn .fi-icon {
        color: #cbd5e1 !important;             /* Icono plateado claro nítido */
        opacity: 0.95 !important;
        width: 1.25rem !important;
        height: 1.25rem !important;
        transition: transform 0.15s ease, color 0.15s ease !important;
    }

    .fi-sidebar-item-btn:hover .fi-icon {
        color: #ffffff !important;
        opacity: 1 !important;
        transform: scale(1.08) !important;
    }

    .fi-sidebar-item-label {
        color: #f1f5f9 !important;
        font-weight: 500 !important;
    }

    .fi-sidebar-item-btn:hover .fi-sidebar-item-label {
        color: #ffffff !important;
    }

    /* Ítem Activo (Pill Azul Vibrante como la imagen "Escritorio") */
    .fi-sidebar-item.fi-active .fi-sidebar-item-btn,
    .fi-sidebar-item-btn.fi-active {
        background-color: var(--accent-blue-active) !important;
        background: var(--accent-blue-gradient) !important;
        color: #ffffff !important;
        box-shadow: 0 4px 16px -1px rgba(2, 132, 199, 0.55) !important;
    }

    .fi-sidebar-item.fi-active .fi-sidebar-item-btn .fi-icon,
    .fi-sidebar-item-btn.fi-active .fi-icon {
        color: #ffffff !important;
        opacity: 1 !important;
    }

    .fi-sidebar-item.fi-active .fi-sidebar-item-label,
    .fi-sidebar-item-btn.fi-active .fi-sidebar-item-label {
        font-weight: 700 !important;
        color: #ffffff !important;
    }

    /* --------------------------------------------------------------------------
       5. ESTADO COLAPSADO (ALINEACIÓN PERFECTA AL CENTRO + SQUIRCLE CUADRADO)
       -------------------------------------------------------------------------- */
    @media (min-width: 1024px) {
        .fi-sidebar:not(.fi-sidebar-open) {
            width: var(--collapsed-sidebar-width) !important;
            align-items: center !important;
        }

        .fi-sidebar:not(.fi-sidebar-open) .brand-text-title {
            display: none !important;
        }

        /* 1. Header y Logo: Centrado Matemático Exacto en el Eje */
        .fi-sidebar:not(.fi-sidebar-open) .fi-sidebar-header-ctn,
        .fi-sidebar:not(.fi-sidebar-open) .fi-sidebar-header {
            width: 100% !important;
            padding: 0 !important;
            margin: 0 !important;
            display: flex !important;
            justify-content: center !important;
            align-items: center !important;
        }

        .fi-sidebar:not(.fi-sidebar-open) .fi-sidebar-header-logo-ctn,
        .fi-sidebar:not(.fi-sidebar-open) .fi-sidebar-header-logo-ctn a,
        .fi-sidebar:not(.fi-sidebar-open) .fi-logo,
        .fi-sidebar:not(.fi-sidebar-open) .aura-clinic-logo-wrapper {
            width: 100% !important;
            padding: 0 !important;
            margin: 0 !important;
            display: flex !important;
            justify-content: center !important;
            align-items: center !important;
            gap: 0 !important;
        }

        .fi-sidebar:not(.fi-sidebar-open) .aura-logo-badge {
            margin: 0 auto !important;
        }

        /* 2. Lista de Navegación: Ancho 100% con Cero Desplazamientos */
        .fi-sidebar:not(.fi-sidebar-open) .fi-sidebar-nav {
            width: 100% !important;
            padding-inline: 0 !important;
            padding-left: 0 !important;
            padding-right: 0 !important;
            display: flex !important;
            flex-direction: column !important;
            align-items: center !important;
        }

        .fi-sidebar:not(.fi-sidebar-open) .fi-sidebar-nav-groups {
            width: 100% !important;
            max-width: 100% !important;
            padding: 0 !important;
            margin: 0 !important;
            display: flex !important;
            flex-direction: column !important;
            align-items: center !important;
            gap: 0.5rem !important;
        }

        .fi-sidebar:not(.fi-sidebar-open) .fi-sidebar-group {
            width: 100% !important;
            max-width: 100% !important;
            padding: 0 !important;
            margin: 0 !important;
            display: flex !important;
            flex-direction: column !important;
            align-items: center !important;
            height: auto !important;
            gap: 0.45rem !important;
        }

        .fi-sidebar:not(.fi-sidebar-open) .fi-sidebar-group-items {
            width: 100% !important;
            max-width: 100% !important;
            padding: 0 !important;
            margin: 0 !important;
            display: flex !important;
            flex-direction: column !important;
            align-items: center !important;
            height: auto !important;
            max-height: none !important;
            overflow: visible !important;
            gap: 0.45rem !important;
        }

        .fi-sidebar:not(.fi-sidebar-open) .fi-sidebar-item {
            width: 100% !important;
            display: flex !important;
            justify-content: center !important;
            align-items: center !important;
            padding: 0 !important;
            margin: 0 !important;
            height: auto !important;
            position: relative !important;
        }

        /* 3. Botones e Iconos: Squircle Cuadrado Centrado Milimétricamente */
        .fi-sidebar:not(.fi-sidebar-open) .fi-sidebar-item-btn {
            width: 2.85rem !important;
            height: 2.85rem !important;
            min-width: 2.85rem !important;
            min-height: 2.85rem !important;
            padding: 0 !important;
            margin: 0 auto !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            border-radius: 0.85rem !important;  /* Squircle / Cuadrado suave */
        }

        .fi-sidebar:not(.fi-sidebar-open) .fi-sidebar-item-btn .fi-icon {
            margin: 0 auto !important;
        }

        /* Ítem Activo en modo colapsado: SQUIRCLE / CUADRADO REDONDEADO ELEGANTE */
        .fi-sidebar:not(.fi-sidebar-open) .fi-sidebar-item.fi-active .fi-sidebar-item-btn,
        .fi-sidebar:not(.fi-sidebar-open) .fi-sidebar-item-btn.fi-active {
            border-radius: 0.85rem !important;  /* No es círculo, sino squircle */
            box-shadow: 0 4px 14px -1px rgba(2, 132, 199, 0.6) !important;
        }

        .fi-sidebar:not(.fi-sidebar-open) .fi-sidebar-group-label {
            display: none !important;
        }
    }

    /* --------------------------------------------------------------------------
       6. MAIN CONTENT AREA (ESPACIADO ELEGANTE, FLUIDEZ TOTAL)
       -------------------------------------------------------------------------- */
    .fi-main-ctn {
        display: flex !important;
        flex-direction: column !important;
        flex: 1 1 0% !important;
        min-width: 0 !important;
    }

    .fi-main {
        flex: 1 1 0% !important;
        padding-inline: 1rem !important;
        padding-top: 0.5rem !important;
        padding-bottom: 2.5rem !important;
    }

    /* Tarjetas y widgets redondeados y con sombra sutil */
    .fi-section,
    .fi-widget {
        border-radius: 1.5rem !important;
        box-shadow: 0 2px 8px -1px rgba(0, 0, 0, 0.04), 0 1px 3px 0 rgba(0, 0, 0, 0.02) !important;
        border: 1px solid rgba(226, 232, 240, 0.9) !important;
        background-color: #ffffff !important;
    }

    .dark .fi-section,
    .dark .fi-widget {
        border: 1px solid rgba(255, 255, 255, 0.06) !important;
        background-color: #0f172a !important;
    }



    /* --------------------------------------------------------------------------
       MODERN PAGE HEADER ENHANCEMENTS (DONEZO-STYLE SINGLE-ROW HEADER - SOLID COLORS)
       -------------------------------------------------------------------------- */
    .fi-header {
        display: flex !important;
        flex-direction: row !important;
        align-items: center !important;
        justify-content: space-between !important;
        gap: 1.5rem !important;
        margin-bottom: 1.5rem !important;
        padding-bottom: 0 !important;
        flex-wrap: nowrap !important;
    }

    .fi-header > div:first-child {
        display: flex !important;
        flex-direction: column !important;
        gap: 0.2rem !important;
        min-width: 0 !important;
        flex: 1 1 auto !important;
    }

    .fi-header-heading {
        font-size: 1.75rem !important;
        font-weight: 800 !important;
        letter-spacing: -0.03em !important;
        color: #0f172a !important;
        line-height: 1.2 !important;
        margin: 0 !important;
        white-space: nowrap !important;
    }

    .dark .fi-header-heading {
        color: #ffffff !important;
    }

    /* PURE SOLID COLOR - NO GRADIENTS */
    .aura-heading-highlight {
        color: #0284c7 !important;
        background: none !important;
        -webkit-background-clip: initial !important;
        -webkit-text-fill-color: initial !important;
    }

    .dark .aura-heading-highlight {
        color: #38bdf8 !important;
        background: none !important;
        -webkit-background-clip: initial !important;
        -webkit-text-fill-color: initial !important;
    }

    .fi-header-subheading {
        font-size: 0.85rem !important;
        color: #64748b !important;
        margin: 0 !important;
        line-height: 1.4 !important;
        white-space: nowrap !important;
        overflow: hidden !important;
        text-overflow: ellipsis !important;
    }

    .dark .fi-header-subheading {
        color: #94a3b8 !important;
    }

    .fi-header-actions {
        display: inline-flex !important;
        flex-direction: row !important;
        align-items: center !important;
        gap: 0.65rem !important;
        flex-shrink: 0 !important;
        flex-wrap: nowrap !important;
        margin: 0 !important;
    }

    /* 1. Botón "Nueva Cita" - Azul Océano Vibrante y Limpio (#0284c7) */
    .aura-btn-header-primary {
        background: #0284c7 !important;
        background-color: #0284c7 !important;
        color: #ffffff !important;
        border: 1px solid #0284c7 !important;
        border-radius: 9999px !important;
        font-weight: 700 !important;
        font-size: 0.85rem !important;
        padding: 0.55rem 1.25rem !important;
        box-shadow: 0 4px 14px -1px rgba(2, 132, 199, 0.4) !important;
        transition: all 0.2s cubic-bezier(0.23, 1, 0.32, 1) !important;
        display: inline-flex !important;
        align-items: center !important;
        gap: 0.45rem !important;
        white-space: nowrap !important;
        flex-shrink: 0 !important;
    }

    .aura-btn-header-primary svg {
        color: #ffffff !important;
        stroke: #ffffff !important;
        width: 16px !important;
        height: 16px !important;
        min-width: 16px !important;
        min-height: 16px !important;
    }

    .aura-btn-header-primary:hover {
        background: #0369a1 !important;
        background-color: #0369a1 !important;
        border-color: #0369a1 !important;
        transform: translateY(-1px) !important;
        box-shadow: 0 6px 18px -1px rgba(2, 132, 199, 0.5) !important;
    }

    .dark .aura-btn-header-primary {
        background: #0284c7 !important;
        background-color: #0284c7 !important;
        color: #ffffff !important;
        border: 1.5px solid #38bdf8 !important;
        box-shadow: 0 4px 16px -2px rgba(2, 132, 199, 0.45) !important;
    }

    .dark .aura-btn-header-primary:hover {
        background: #0ea5e9 !important;
        background-color: #0ea5e9 !important;
        border-color: #7dd3fc !important;
    }

    .aura-btn-header-primary:active {
        transform: scale(0.97) !important;
    }

    .aura-btn-header-celeste svg {
        color: #ffffff !important;
        stroke: #ffffff !important;
        width: 16px !important;
        height: 16px !important;
        min-width: 16px !important;
        min-height: 16px !important;
    }

    .aura-btn-header-outline-blue svg {
        color: #0284c7 !important;
        stroke: #0284c7 !important;
        width: 16px !important;
        height: 16px !important;
        min-width: 16px !important;
        min-height: 16px !important;
    }

    .dark .aura-btn-header-outline-blue svg {
        color: #38bdf8 !important;
        stroke: #38bdf8 !important;
    }

    /* 2. Botón "Ver Agenda" - Celeste sólido (#0ea5e9) */
    .aura-btn-header-celeste {
        background: #0ea5e9 !important;
        background-color: #0ea5e9 !important;
        color: #ffffff !important;
        border: 1px solid #0ea5e9 !important;
        border-radius: 9999px !important;
        font-weight: 700 !important;
        font-size: 0.85rem !important;
        padding: 0.55rem 1.25rem !important;
        box-shadow: 0 4px 12px -1px rgba(14, 165, 233, 0.35) !important;
        transition: all 0.2s cubic-bezier(0.23, 1, 0.32, 1) !important;
        display: inline-flex !important;
        align-items: center !important;
        gap: 0.45rem !important;
        white-space: nowrap !important;
        flex-shrink: 0 !important;
    }

    .aura-btn-header-celeste:hover {
        background: #0284c7 !important;
        background-color: #0284c7 !important;
        border-color: #0284c7 !important;
        transform: translateY(-1px) !important;
    }

    .dark .aura-btn-header-celeste {
        background: #0284c7 !important;
        background-color: #0284c7 !important;
        color: #ffffff !important;
        border: 1px solid #0284c7 !important;
        box-shadow: 0 4px 14px -2px rgba(14, 165, 233, 0.4) !important;
    }

    .dark .aura-btn-header-celeste:hover {
        background: #0ea5e9 !important;
        background-color: #0ea5e9 !important;
        border-color: #0ea5e9 !important;
    }

    .aura-btn-header-celeste:active {
        transform: scale(0.97) !important;
    }

    /* 3. Botón "Ver Sitio Web" - Blanco con bordes azul sólido */
    .aura-btn-header-outline-blue {
        background: #ffffff !important;
        background-color: #ffffff !important;
        color: #0284c7 !important;
        border: 1.5px solid #0284c7 !important;
        border-radius: 9999px !important;
        font-weight: 700 !important;
        font-size: 0.85rem !important;
        padding: 0.55rem 1.25rem !important;
        box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.04) !important;
        transition: all 0.2s cubic-bezier(0.23, 1, 0.32, 1) !important;
        display: inline-flex !important;
        align-items: center !important;
        gap: 0.45rem !important;
        white-space: nowrap !important;
        flex-shrink: 0 !important;
    }

    .aura-btn-header-outline-blue:hover {
        background: #f0f9ff !important;
        background-color: #f0f9ff !important;
        color: #0369a1 !important;
        border-color: #0369a1 !important;
        transform: translateY(-1px) !important;
    }

    .dark .aura-btn-header-outline-blue {
        background: #0b172a !important;
        background-color: #0b172a !important;
        color: #38bdf8 !important;
        border: 1.5px solid #38bdf8 !important;
        box-shadow: none !important;
    }

    .dark .aura-btn-header-outline-blue:hover {
        background: #1e293b !important;
        background-color: #1e293b !important;
        color: #7dd3fc !important;
        border-color: #7dd3fc !important;
        transform: translateY(-1px) !important;
    }

    .aura-btn-header-outline-blue:active {
        transform: scale(0.97) !important;
    }

    /* --------------------------------------------------------------------------
       BENTO LAYOUT: 3 COLUMNAS (2 COLUMNAS DE KPIS + 1 COLUMNA ESPECIALISTAS DOBLE FILA)
       -------------------------------------------------------------------------- */
    .fi-wi-stats-bento,
    .fi-wi > div:has(.aura-bento-layout) {
        grid-column: 1 / -1 !important;
        width: 100% !important;
        max-width: 100% !important;
    }

    .aura-bento-layout {
        display: grid !important;
        grid-template-columns: 1fr !important;
        gap: 1.15rem !important;
        width: 100% !important;
    }

    @media (min-width: 640px) and (max-width: 1023px) {
        .aura-bento-layout {
            grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
        }

        .aura-specialists-card {
            grid-column: 1 / -1 !important;
        }
    }

    @media (min-width: 1024px) {
        .aura-bento-layout {
            grid-template-columns: repeat(3, minmax(0, 1fr)) !important;
            grid-template-rows: auto auto !important;
            align-items: stretch !important;
        }

        .aura-kpi-theme-blue {
            grid-column: 1 !important;
            grid-row: 1 !important;
        }

        .aura-kpi-theme-orange {
            grid-column: 2 !important;
            grid-row: 1 !important;
        }

        .aura-kpi-theme-emerald {
            grid-column: 1 !important;
            grid-row: 2 !important;
        }

        .aura-kpi-theme-indigo {
            grid-column: 2 !important;
            grid-row: 2 !important;
        }

        .aura-specialists-card {
            grid-column: 3 !important;
            grid-row: 1 / span 2 !important;
            height: 100% !important;
        }
    }

    /* Base KPI Item Card (Finnova Modern Style) */
    .aura-kpi-item {
        position: relative;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        padding: 1.35rem 1.4rem !important;
        border-radius: 1.35rem !important;
        background-color: #ffffff !important;
        border: 1px solid #e2e8f0 !important;
        box-shadow: 0 1px 3px 0 rgba(15, 23, 42, 0.04) !important;
        box-sizing: border-box !important;
        transition: transform 0.2s cubic-bezier(0.23, 1, 0.32, 1), box-shadow 0.2s ease, border-color 0.2s ease !important;
    }

    .dark .aura-kpi-item {
        background-color: #0c182d !important;
        border-color: rgba(255, 255, 255, 0.08) !important;
        box-shadow: 0 4px 14px -2px rgba(0, 0, 0, 0.3) !important;
    }

    .aura-kpi-item:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 20px -3px rgba(15, 23, 42, 0.08) !important;
        border-color: #cbd5e1 !important;
    }

    .dark .aura-kpi-item:hover {
        border-color: rgba(255, 255, 255, 0.16) !important;
    }

    /* Head */
    .aura-finnova-head,
    .aura-kpi-head-simple {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 0.65rem;
    }

    /* Body simple */
    .aura-kpi-body-simple {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 0.75rem;
        margin-bottom: 0.65rem;
    }

    /* Big Icon Boxes (for cards without the days chart) */
    .aura-kpi-big-icon {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 3rem;
        height: 3rem;
        border-radius: 1rem;
        flex-shrink: 0;
        transition: transform 0.2s ease;
    }

    .aura-kpi-item:hover .aura-kpi-big-icon {
        transform: scale(1.05);
    }

    .aura-big-icon-blue {
        background-color: #f0f9ff;
        color: #0284c7;
        border: 1px solid #bae6fd;
    }

    .dark .aura-big-icon-blue {
        background-color: #082f49;
        color: #38bdf8;
        border-color: #0369a1;
    }

    .aura-big-icon-orange {
        background-color: #fff7ed;
        color: #ea580c;
        border: 1px solid #fed7aa;
    }

    .dark .aura-big-icon-orange {
        background-color: #431407;
        color: #fb923c;
        border-color: #7c2d12;
    }

    .aura-big-icon-indigo {
        background-color: #eef2ff;
        color: #4f46e5;
        border: 1px solid #c7d2fe;
    }

    .dark .aura-big-icon-indigo {
        background-color: #1e1b4b;
        color: #818cf8;
        border-color: #3730a3;
    }

    /* Foot simple */
    .aura-kpi-foot-simple {
        padding-top: 0.55rem;
        border-top: 1px solid #f1f5f9;
    }

    .dark .aura-kpi-foot-simple {
        border-top-color: #1e293b;
    }

    .aura-kpi-label {
        font-size: 0.78rem;
        font-weight: 700;
        color: #475569;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        white-space: nowrap;
    }

    .aura-kpi-chip {
        display: inline-flex;
        align-items: center;
        padding: 0.15rem 0.55rem;
        border-radius: 9999px;
        font-size: 0.65rem;
        font-weight: 700;
        white-space: nowrap;
    }

    .aura-chip-blue {
        background-color: #f0f9ff;
        color: #0284c7;
        border: 1px solid #bae6fd;
    }

    .dark .aura-chip-blue {
        background-color: #082f49;
        color: #7dd3fc;
        border-color: #0369a1;
    }

    .aura-chip-amber {
        background-color: #fffbeb;
        color: #b45309;
        border: 1px solid #fde68a;
    }

    .dark .aura-chip-amber {
        background-color: #451a03;
        color: #fcd34d;
        border-color: #78350f;
    }

    .aura-chip-green {
        background-color: #ecfdf5;
        color: #059669;
        border: 1px solid #a7f3d0;
    }

    .dark .aura-chip-green {
        background-color: #064e3b;
        color: #6ee7b7;
        border-color: #047857;
    }

    .aura-chip-indigo {
        background-color: #eef2ff;
        color: #4f46e5;
        border: 1px solid #c7d2fe;
    }

    .dark .aura-chip-indigo {
        background-color: #1e1b4b;
        color: #a5b4fc;
        border-color: #3730a3;
    }

    .aura-icon-circle {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 1.85rem;
        height: 1.85rem;
        border-radius: 9999px;
        flex-shrink: 0;
    }

    .aura-icon-blue {
        background-color: #e0f2fe;
        color: #0284c7;
        border: 1px solid #bae6fd;
    }

    .dark .aura-icon-blue {
        background-color: #082f49;
        color: #38bdf8;
        border-color: #0369a1;
    }

    .aura-icon-orange {
        background-color: #ffedd5;
        color: #ea580c;
        border: 1px solid #fed7aa;
    }

    .dark .aura-icon-orange {
        background-color: #431407;
        color: #fb923c;
        border-color: #7c2d12;
    }

    .aura-icon-emerald {
        background-color: #d1fae5;
        color: #059669;
        border: 1px solid #a7f3d0;
    }

    .dark .aura-icon-emerald {
        background-color: #064e3b;
        color: #34d399;
        border-color: #047857;
    }

    .aura-icon-indigo {
        background-color: #e0e7ff;
        color: #4f46e5;
        border: 1px solid #c7d2fe;
    }

    .dark .aura-icon-indigo {
        background-color: #1e1b4b;
        color: #818cf8;
        border-color: #3730a3;
    }

    /* Body */
    .aura-finnova-body {
        display: flex;
        align-items: flex-end;
        justify-content: space-between;
        gap: 0.75rem;
        margin-bottom: 0.65rem;
    }

    .aura-finnova-stat {
        display: flex;
        flex-direction: column;
    }

    .aura-kpi-num {
        font-size: 2.25rem;
        font-weight: 800;
        color: #0f172a;
        line-height: 1.1;
        letter-spacing: -0.03em;
        font-variant-numeric: tabular-nums;
    }

    .dark .aura-kpi-num {
        color: #ffffff;
    }

    .aura-kpi-subtext {
        font-size: 0.72rem;
        color: #64748b;
        margin-top: 0.2rem;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .dark .aura-kpi-subtext {
        color: #94a3b8;
    }

    /* Mini Vertical Bar Chart */
    .aura-mini-bars {
        display: flex;
        align-items: flex-end;
        gap: 0.35rem;
        height: 2.6rem;
    }

    .aura-bar-col {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 0.2rem;
        height: 100%;
        justify-content: flex-end;
    }

    .aura-bar {
        width: 6px;
        background-color: #cbd5e1;
        border-radius: 3px;
        min-height: 4px;
        transition: height 0.3s ease;
    }

    .dark .aura-bar {
        background-color: #334155;
    }

    .aura-bar-label {
        font-size: 0.58rem;
        font-weight: 700;
        color: #94a3b8;
        line-height: 1;
    }

    /* Active Bar & Day Colors per Theme */
    .aura-kpi-theme-blue .aura-bar.aura-bar-active { background-color: #0284c7; }
    .aura-kpi-theme-blue .aura-bar-label.aura-label-active { color: #0284c7; }

    .aura-kpi-theme-orange .aura-bar.aura-bar-active { background-color: #ea580c; }
    .aura-kpi-theme-orange .aura-bar-label.aura-label-active { color: #ea580c; }

    .aura-kpi-theme-emerald .aura-bar.aura-bar-active { background-color: #059669; }
    .aura-kpi-theme-emerald .aura-bar-label.aura-label-active { color: #059669; }

    .aura-kpi-theme-indigo .aura-bar.aura-bar-active { background-color: #4f46e5; }
    .aura-kpi-theme-indigo .aura-bar-label.aura-label-active { color: #4f46e5; }

    .dark .aura-kpi-theme-blue .aura-bar.aura-bar-active { background-color: #38bdf8; }
    .dark .aura-kpi-theme-blue .aura-bar-label.aura-label-active { color: #38bdf8; }

    .dark .aura-kpi-theme-orange .aura-bar.aura-bar-active { background-color: #fb923c; }
    .dark .aura-kpi-theme-orange .aura-bar-label.aura-label-active { color: #fb923c; }

    .dark .aura-kpi-theme-emerald .aura-bar.aura-bar-active { background-color: #10b981; }
    .dark .aura-kpi-theme-emerald .aura-bar-label.aura-label-active { color: #34d399; }

    .dark .aura-kpi-theme-indigo .aura-bar.aura-bar-active { background-color: #818cf8; }
    .dark .aura-kpi-theme-indigo .aura-bar-label.aura-label-active { color: #818cf8; }

    /* Foot */
    .aura-finnova-foot {
        padding-top: 0.55rem;
        border-top: 1px solid #f1f5f9;
    }

    .dark .aura-finnova-foot {
        border-top-color: #1e293b;
    }

    .aura-foot-status {
        font-size: 0.72rem;
        font-weight: 600;
        color: #475569;
    }

    .dark .aura-foot-status {
        color: #94a3b8;
    }

    .dark .aura-medical-foot {
        border-top-color: #1e293b;
    }

    .aura-foot-desc {
        font-size: 0.7rem;
        color: #64748b;
    }

    .dark .aura-foot-desc {
        color: #94a3b8;
    }

    /* --------------------------------------------------------------------------
       COLUMNA 3 (DOBLE FILA): LISTADO DE ESPECIALISTAS (PINTEREST / FINNOVA STYLE)
       -------------------------------------------------------------------------- */
    .aura-specialists-card {
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        padding: 1.45rem !important;
        border-radius: 1.35rem !important;
        background-color: #ffffff !important;
        border: 1px solid #e2e8f0 !important;
        box-shadow: 0 1px 3px 0 rgba(15, 23, 42, 0.04) !important;
        box-sizing: border-box !important;
        transition: transform 0.2s ease, box-shadow 0.2s ease, border-color 0.2s ease !important;
    }

    .dark .aura-specialists-card {
        background-color: #0c182d !important;
        border-color: rgba(255, 255, 255, 0.08) !important;
        box-shadow: 0 4px 14px -2px rgba(0, 0, 0, 0.3) !important;
    }

    .aura-specialists-card:hover {
        box-shadow: 0 10px 24px -4px rgba(15, 23, 42, 0.08) !important;
        border-color: #cbd5e1 !important;
    }

    .dark .aura-specialists-card:hover {
        border-color: rgba(255, 255, 255, 0.16) !important;
    }

    .aura-spec-header {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 0.5rem;
        margin-bottom: 1rem;
    }

    .aura-spec-title-group {
        display: flex;
        flex-direction: column;
    }

    .aura-spec-title {
        font-size: 1.05rem;
        font-weight: 800;
        color: #0f172a;
        margin: 0;
        line-height: 1.2;
        letter-spacing: -0.02em;
    }

    .dark .aura-spec-title {
        color: #ffffff;
    }

    .aura-spec-subtitle {
        font-size: 0.74rem;
        color: #64748b;
        margin-top: 0.15rem;
    }

    .dark .aura-spec-subtitle {
        color: #94a3b8;
    }

    .aura-spec-count-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        padding: 0.25rem 0.65rem;
        border-radius: 9999px;
        background-color: #ecfdf5;
        color: #047857;
        border: 1px solid #a7f3d0;
        font-size: 0.72rem;
        font-weight: 700;
        white-space: nowrap;
    }

    .dark .aura-spec-count-badge {
        background-color: #064e3b;
        color: #6ee7b7;
        border-color: #047857;
    }

    .aura-pulse-dot {
        width: 6px;
        height: 6px;
        border-radius: 9999px;
        background-color: #10b981;
    }

    .aura-spec-list {
        display: flex;
        flex-direction: column;
        gap: 0.6rem;
        flex: 1;
    }

    .aura-spec-row {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 0.6rem 0.75rem;
        border-radius: 0.85rem;
        background-color: #f8fafc;
        border: 1px solid #f1f5f9;
        transition: background-color 0.15s ease, border-color 0.15s ease;
    }

    .aura-spec-row:hover {
        background-color: #f1f5f9;
        border-color: #e2e8f0;
    }

    .dark .aura-spec-row {
        background-color: #111e35;
        border-color: rgba(255, 255, 255, 0.04);
    }

    .dark .aura-spec-row:hover {
        background-color: #162644;
        border-color: rgba(255, 255, 255, 0.08);
    }

    .aura-spec-avatar-box {
        width: 2.35rem;
        height: 2.35rem;
        border-radius: 9999px;
        overflow: hidden;
        flex-shrink: 0;
    }

    .aura-spec-img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .aura-spec-avatar-fallback {
        width: 100%;
        height: 100%;
        border-radius: 9999px;
        color: #ffffff;
        font-size: 0.75rem;
        font-weight: 800;
        display: flex;
        align-items: center;
        justify-content: center;
        text-transform: uppercase;
        user-select: none;
    }

    .aura-spec-info {
        display: flex;
        flex-direction: column;
        flex: 1;
        min-width: 0;
    }

    .aura-spec-name {
        font-size: 0.82rem;
        font-weight: 700;
        color: #0f172a;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .dark .aura-spec-name {
        color: #ffffff;
    }

    .aura-spec-specialty {
        font-size: 0.7rem;
        color: #64748b;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        margin-top: 0.1rem;
    }

    .dark .aura-spec-specialty {
        color: #94a3b8;
    }

    .aura-spec-status-pill {
        display: inline-flex;
        align-items: center;
        gap: 0.3rem;
        padding: 0.15rem 0.5rem;
        border-radius: 9999px;
        background-color: #ffffff;
        border: 1px solid #e2e8f0;
        font-size: 0.65rem;
        font-weight: 600;
        color: #047857;
        flex-shrink: 0;
    }

    .dark .aura-spec-status-pill {
        background-color: #0c182d;
        border-color: rgba(255, 255, 255, 0.08);
        color: #6ee7b7;
    }

    .aura-spec-dot {
        width: 5px;
        height: 5px;
        border-radius: 9999px;
        background-color: #10b981;
    }

    .aura-spec-footer {
        padding-top: 0.85rem;
        border-top: 1px solid #f1f5f9;
        margin-top: 0.85rem;
    }

    .dark .aura-spec-footer {
        border-top-color: #1e293b;
    }

    .aura-spec-view-all {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.45rem;
        font-size: 0.78rem;
        font-weight: 700;
        color: #0284c7;
        text-decoration: none;
        padding: 0.45rem 0.75rem;
        border-radius: 0.75rem;
        background-color: #f0f9ff;
        border: 1px solid #bae6fd;
        transition: all 0.15s ease;
    }

    .aura-spec-view-all:hover {
        background-color: #0284c7;
        color: #ffffff;
        border-color: #0284c7;
    }

    .dark .aura-spec-view-all {
        background-color: #082f49;
        color: #7dd3fc;
        border-color: #0369a1;
    }

    .dark .aura-spec-view-all:hover {
        background-color: #0284c7;
        color: #ffffff;
    }

    /* --------------------------------------------------------------------------
       FINNOVA APPOINTMENT FEED (MODERN REPLACEMENT FOR BASIC DATED TABLE)
       -------------------------------------------------------------------------- */
    .fi-wi-appointments-feed,
    .fi-wi > div:has(.aura-feed-container) {
        width: 100% !important;
        max-width: 100% !important;
    }

    @media (min-width: 1024px) {
        .fi-wi-appointments-feed,
        .fi-wi:has(.fi-wi-appointments-feed),
        .fi-wi:has(.aura-feed-container) {
            grid-column: 1 / span 2 !important;
            grid-row: span 2 !important;
            width: 100% !important;
        }

        .fi-wi:has(.fi-wi-chart) {
            grid-column: 3 !important;
            width: 100% !important;
        }
    }

    /* Chart widgets styling (Column 3) */
    .fi-wi-chart .fi-section,
    .fi-wi:has(.fi-wi-chart) .fi-section,
    .fi-wi:has(.fi-wi-chart) section {
        border-radius: 1.35rem !important;
        background-color: #ffffff !important;
        border: 1px solid #e2e8f0 !important;
        box-shadow: 0 1px 3px 0 rgba(15, 23, 42, 0.04) !important;
        overflow: hidden !important;
    }

    .dark .fi-wi-chart .fi-section,
    .dark .fi-wi:has(.fi-wi-chart) .fi-section,
    .dark .fi-wi:has(.fi-wi-chart) section {
        background-color: #0c182d !important;
        border-color: rgba(255, 255, 255, 0.08) !important;
        box-shadow: 0 4px 14px -2px rgba(0, 0, 0, 0.3) !important;
    }

    .aura-feed-container {
        border-radius: 1.5rem !important;
        background-color: #ffffff !important;
        border: 1px solid rgba(226, 232, 240, 0.9) !important;
        box-shadow: 0 4px 20px -2px rgba(15, 23, 42, 0.05) !important;
        padding: 1.5rem !important;
        box-sizing: border-box !important;
    }

    .dark .aura-feed-container {
        background-color: #0c182d !important;
        border-color: rgba(255, 255, 255, 0.08) !important;
        box-shadow: 0 8px 30px -4px rgba(0, 0, 0, 0.4) !important;
    }

    .aura-feed-header-block {
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
        margin-bottom: 1.25rem;
        padding-bottom: 1.15rem;
        border-bottom: 1px solid rgba(226, 232, 240, 0.8);
    }

    .dark .aura-feed-header-block {
        border-bottom-color: rgba(255, 255, 255, 0.06);
    }

    .aura-feed-title-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        flex-wrap: wrap;
    }

    .aura-feed-title {
        font-size: 1.15rem !important;
        font-weight: 800 !important;
        color: #0f172a !important;
        margin: 0 !important;
        letter-spacing: -0.015em !important;
    }

    .dark .aura-feed-title {
        color: #ffffff !important;
    }

    .aura-feed-subtitle {
        font-size: 0.835rem !important;
        color: #64748b !important;
        margin: 0 !important;
        line-height: 1.4 !important;
    }

    .dark .aura-feed-subtitle {
        color: #94a3b8 !important;
    }

    .aura-feed-filters-row {
        display: flex;
        align-items: center;
        margin-top: 0.2rem;
    }

    .aura-feed-tabs {
        display: inline-flex;
        align-items: center;
        background-color: #f1f5f9;
        padding: 0.25rem;
        border-radius: 0.85rem;
        border: 1px solid rgba(226, 232, 240, 0.8);
    }

    .dark .aura-feed-tabs {
        background-color: rgba(15, 23, 42, 0.6);
        border-color: rgba(255, 255, 255, 0.06);
    }

    .aura-feed-tab-btn {
        padding: 0.4rem 0.85rem;
        border-radius: 0.65rem;
        font-size: 0.8rem;
        font-weight: 600;
        color: #64748b;
        background: transparent;
        border: none;
        cursor: pointer;
        transition: all 0.15s ease;
    }

    .dark .aura-feed-tab-btn {
        color: #94a3b8;
    }

    .aura-feed-tab-btn:hover {
        color: #0284c7;
    }

    .dark .aura-feed-tab-btn:hover {
        color: #38bdf8;
    }

    .aura-feed-tab-btn.aura-tab-active {
        background: #0284c7;
        color: #ffffff !important;
        box-shadow: 0 2px 8px -1px rgba(2, 132, 199, 0.4);
    }

    .aura-btn-go-module {
        display: inline-flex;
        align-items: center;
        gap: 0.45rem;
        padding: 0.48rem 0.95rem;
        border-radius: 0.75rem;
        background-color: #0284c7;
        color: #ffffff !important;
        font-size: 0.78rem;
        font-weight: 700;
        text-decoration: none;
        box-shadow: 0 2px 6px -1px rgba(2, 132, 199, 0.4);
        transition: all 0.15s ease;
        white-space: nowrap;
    }

    .aura-btn-go-module:hover {
        background-color: #0369a1;
        transform: translateY(-1px);
        box-shadow: 0 4px 10px -1px rgba(2, 132, 199, 0.5);
    }

    .aura-btn-go-module:active {
        transform: scale(0.97);
    }

    .aura-feed-view-all {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        font-size: 0.825rem;
        font-weight: 700;
        color: #0284c7;
        text-decoration: none;
        transition: transform 0.15s ease;
    }

    .aura-feed-view-all:hover {
        color: #0369a1;
        transform: translateX(2px);
    }

    .dark .aura-feed-view-all {
        color: #38bdf8;
    }

    /* List Rows */
    .aura-feed-list {
        display: flex;
        flex-direction: column;
        gap: 0.65rem;
    }

    .aura-feed-row {
        display: grid;
        grid-template-columns: 1fr;
        gap: 0.85rem;
        padding: 0.95rem 1.15rem;
        border-radius: 1rem;
        background: #f8fafc;
        border: 1px solid rgba(226, 232, 240, 0.75);
        align-items: center;
        transition: all 0.15s ease;
    }

    @media (min-width: 768px) {
        .aura-feed-row {
            grid-template-columns: 2fr 1.6fr 1.3fr 1fr;
            gap: 1rem;
        }
    }

    .dark .aura-feed-row {
        background: rgba(15, 23, 42, 0.45);
        border-color: rgba(255, 255, 255, 0.05);
    }

    .aura-feed-row:hover {
        background: #f1f5f9;
        border-color: rgba(2, 132, 199, 0.3);
        transform: translateY(-1px);
        box-shadow: 0 4px 14px -2px rgba(15, 23, 42, 0.04);
    }

    .dark .aura-feed-row:hover {
        background: rgba(30, 41, 59, 0.5);
        border-color: rgba(56, 189, 248, 0.3);
    }

    .aura-feed-patient {
        display: flex;
        align-items: center;
        gap: 0.85rem;
    }

    .aura-feed-avatar {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 2.6rem;
        height: 2.6rem;
        border-radius: 0.8rem;
        background: #0284c7 !important;
        background-color: #0284c7 !important;
        color: #ffffff;
        font-weight: 700;
        font-size: 0.875rem;
        box-shadow: 0 2px 8px -1px rgba(2, 132, 199, 0.35);
        flex-shrink: 0;
    }

    .aura-feed-patient-info {
        display: flex;
        flex-direction: column;
        line-height: 1.25;
    }

    .aura-patient-name {
        font-size: 0.9rem;
        font-weight: 700;
        color: #0f172a;
    }

    .dark .aura-patient-name {
        color: #f1f5f9;
    }

    .aura-patient-phone {
        display: inline-flex;
        align-items: center;
        gap: 0.3rem;
        font-size: 0.775rem;
        color: #64748b;
        margin-top: 0.15rem;
    }

    .dark .aura-patient-phone {
        color: #94a3b8;
    }

    .aura-feed-service-col {
        display: flex;
        flex-direction: column;
        gap: 0.2rem;
    }

    .aura-service-badge {
        font-size: 0.825rem;
        font-weight: 600;
        color: #1e293b;
    }

    .dark .aura-service-badge {
        color: #e2e8f0;
    }

    .aura-dentist-tag {
        display: inline-flex;
        align-items: center;
        gap: 0.3rem;
        font-size: 0.75rem;
        color: #0284c7;
        font-weight: 500;
    }

    .dark .aura-dentist-tag {
        color: #38bdf8;
    }

    .aura-feed-datetime {
        display: flex;
        flex-direction: column;
        gap: 0.25rem;
    }

    .aura-date-text {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        font-size: 0.775rem;
        color: #475569;
        font-weight: 500;
    }

    .dark .aura-date-text {
        color: #94a3b8;
    }

    .aura-time-pill {
        display: inline-block;
        padding: 0.15rem 0.5rem;
        border-radius: 9999px;
        background: rgba(148, 163, 184, 0.15);
        color: #334155;
        font-size: 0.725rem;
        font-weight: 700;
        width: fit-content;
    }

    .dark .aura-time-pill {
        background: rgba(148, 163, 184, 0.12);
        color: #cbd5e1;
    }

    .aura-status-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        padding: 0.3rem 0.75rem;
        border-radius: 9999px;
        font-size: 0.75rem;
        font-weight: 700;
        width: fit-content;
    }

    .aura-status-confirmed {
        background: rgba(16, 185, 129, 0.12);
        color: #047857;
        border: 1px solid rgba(16, 185, 129, 0.25);
    }

    .dark .aura-status-confirmed {
        background: rgba(16, 185, 129, 0.18);
        color: #34d399;
    }

    .aura-status-pending {
        background: rgba(245, 158, 11, 0.12);
        color: #b45309;
        border: 1px solid rgba(245, 158, 11, 0.25);
    }

    .dark .aura-status-pending {
        background: rgba(245, 158, 11, 0.18);
        color: #fde68a;
    }

    .aura-status-progress {
        background: rgba(14, 165, 233, 0.12);
        color: #0284c7;
        border: 1px solid rgba(14, 165, 233, 0.25);
    }

    .dark .aura-status-progress {
        background: rgba(14, 165, 233, 0.18);
        color: #38bdf8;
    }

    .aura-status-completed {
        background: rgba(100, 116, 139, 0.12);
        color: #475569;
    }

    .dark .aura-status-completed {
        background: rgba(148, 163, 184, 0.12);
        color: #94a3b8;
    }

    .aura-feed-actions {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        justify-content: flex-end;
    }

    .aura-btn-whatsapp {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        padding: 0.4rem 0.85rem;
        border-radius: 9999px;
        background: #10b981;
        color: #ffffff !important;
        font-size: 0.775rem;
        font-weight: 700;
        text-decoration: none;
        box-shadow: 0 2px 8px rgba(16, 185, 129, 0.3);
        transition: all 0.15s ease;
    }

    .aura-btn-whatsapp:hover {
        background: #059669;
        transform: scale(1.02);
    }

    .aura-btn-confirm {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        padding: 0.4rem 0.85rem;
        border-radius: 9999px;
        background: rgba(14, 165, 233, 0.12);
        color: #0284c7 !important;
        border: 1px solid rgba(14, 165, 233, 0.3);
        font-size: 0.775rem;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.15s ease;
    }

    .aura-btn-confirm:hover {
        background: #0284c7;
        color: #ffffff !important;
    }

    .aura-btn-edit {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 2rem;
        height: 2rem;
        border-radius: 9999px;
        background: rgba(100, 116, 139, 0.1);
        color: #64748b;
        text-decoration: none;
        transition: all 0.15s ease;
    }

    .aura-btn-edit:hover {
        background: rgba(2, 132, 199, 0.15);
        color: #0284c7;
    }

    .dark .aura-btn-edit {
        background: rgba(255, 255, 255, 0.06);
        color: #94a3b8;
    }

    .dark .aura-btn-edit:hover {
        color: #38bdf8;
    }

    .aura-feed-empty {
        padding: 3rem 1.5rem;
        text-align: center;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
    }

    .aura-empty-icon {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 3.5rem;
        height: 3.5rem;
        border-radius: 1rem;
        background: rgba(14, 165, 233, 0.1);
        color: #0284c7;
        margin-bottom: 1rem;
    }

    .aura-feed-empty h4 {
        font-size: 1rem;
        font-weight: 700;
        color: #0f172a;
        margin: 0;
    }

    .dark .aura-feed-empty h4 {
        color: #f8fafc;
    }

    .aura-feed-empty p {
        font-size: 0.825rem;
        color: #64748b;
        margin: 0.35rem 0 0 0;
    }

    /* Botón inferior amigable para ir a citas */
    .aura-feed-bottom-action {
        display: flex;
        align-items: center;
        justify-content: center;
        margin-top: 1.25rem;
        padding-top: 1.15rem;
        border-top: 1px solid rgba(226, 232, 240, 0.8);
    }

    .dark .aura-feed-bottom-action {
        border-top-color: rgba(255, 255, 255, 0.06);
    }

    .aura-btn-footer-appointments {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        width: 100%;
        max-width: 320px;
        padding: 0.65rem 1.25rem;
        border-radius: 0.85rem;
        background-color: #f0f9ff;
        color: #0284c7 !important;
        border: 1px solid rgba(2, 132, 199, 0.3);
        font-size: 0.825rem;
        font-weight: 700;
        text-decoration: none;
        transition: all 0.2s ease;
        text-align: center;
    }

    .aura-btn-footer-appointments:hover {
        background-color: #0284c7;
        color: #ffffff !important;
        border-color: #0284c7;
        box-shadow: 0 4px 12px -2px rgba(2, 132, 199, 0.35);
        transform: translateY(-1px);
    }

    .aura-btn-footer-appointments:active {
        transform: scale(0.98);
    }

    .dark .aura-btn-footer-appointments {
        background-color: rgba(2, 132, 199, 0.15);
        color: #38bdf8 !important;
        border-color: rgba(56, 189, 248, 0.3);
    }

    .dark .aura-btn-footer-appointments:hover {
        background-color: #0284c7;
        color: #ffffff !important;
        border-color: #0284c7;
    }

    /* --------------------------------------------------------------------------
       PREVENT STICKY/FLOATING TOPBAR FROM OVERLAPPING PAGE CONTENT
       -------------------------------------------------------------------------- */
    .fi-main-ctn,
    .fi-page {
        padding-top: 0.75rem !important;
    }

    /* --------------------------------------------------------------------------
       APPOINTMENT MANAGEMENT MODULE KPIS (ULTRA-COMPACT, LOW HEIGHT BAR)
       -------------------------------------------------------------------------- */
    .fi-wi-appointment-stats {
        margin-bottom: 1.25rem !important;
        width: 100% !important;
    }

    .aura-app-compact-bar {
        display: grid !important;
        grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
        gap: 0.85rem !important;
        width: 100% !important;
    }

    @media (min-width: 1024px) {
        .aura-app-compact-bar {
            grid-template-columns: repeat(4, minmax(0, 1fr)) !important;
            gap: 1rem !important;
        }
    }

    /* Mini stat card */
    .aura-mini-stat {
        display: flex !important;
        align-items: center !important;
        gap: 0.85rem !important;
        padding: 0.75rem 1rem !important;
        min-height: 4.15rem !important;
        border-radius: 1rem !important;
        background-color: #ffffff !important;
        border: 1px solid #e2e8f0 !important;
        box-shadow: 0 1px 3px 0 rgba(15, 23, 42, 0.04) !important;
        transition: transform 0.15s ease, box-shadow 0.15s ease !important;
    }

    .aura-mini-stat:hover {
        transform: translateY(-1px) !important;
        box-shadow: 0 4px 12px -2px rgba(15, 23, 42, 0.06) !important;
    }

    .dark .aura-mini-stat {
        background-color: #0c182d !important;
        border-color: rgba(255, 255, 255, 0.08) !important;
        box-shadow: 0 4px 12px -2px rgba(0, 0, 0, 0.3) !important;
    }

    /* Mini icon circle/box */
    .aura-mini-icon {
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        width: 2.5rem !important;
        height: 2.5rem !important;
        min-width: 2.5rem !important;
        border-radius: 0.75rem !important;
        flex-shrink: 0 !important;
    }

    .aura-mini-icon-emerald {
        background-color: #ecfdf5 !important;
        color: #059669 !important;
        border: 1px solid #a7f3d0 !important;
    }

    .dark .aura-mini-icon-emerald {
        background-color: rgba(5, 150, 105, 0.2) !important;
        color: #34d399 !important;
        border-color: rgba(5, 150, 105, 0.3) !important;
    }

    .aura-mini-icon-orange {
        background-color: #fffbeb !important;
        color: #d97706 !important;
        border: 1px solid #fde68a !important;
    }

    .dark .aura-mini-icon-orange {
        background-color: rgba(217, 119, 6, 0.2) !important;
        color: #fbbf24 !important;
        border-color: rgba(217, 119, 6, 0.3) !important;
    }

    .aura-mini-icon-blue {
        background-color: #f0f9ff !important;
        color: #0284c7 !important;
        border: 1px solid #bae6fd !important;
    }

    .dark .aura-mini-icon-blue {
        background-color: rgba(2, 132, 199, 0.2) !important;
        color: #38bdf8 !important;
        border-color: rgba(2, 132, 199, 0.3) !important;
    }

    .aura-mini-icon-slate {
        background-color: #f8fafc !important;
        color: #475569 !important;
        border: 1px solid #e2e8f0 !important;
    }

    .dark .aura-mini-icon-slate {
        background-color: rgba(148, 163, 184, 0.15) !important;
        color: #94a3b8 !important;
        border-color: rgba(148, 163, 184, 0.25) !important;
    }

    .aura-mini-info {
        display: flex !important;
        flex-direction: column !important;
        line-height: 1.15 !important;
    }

    .aura-mini-num {
        font-size: 1.35rem !important;
        font-weight: 800 !important;
        color: #0f172a !important;
        letter-spacing: -0.02em !important;
    }

    .dark .aura-mini-num {
        color: #ffffff !important;
    }

    .aura-mini-label {
        font-size: 0.75rem !important;
        font-weight: 600 !important;
        color: #64748b !important;
        margin-top: 0.1rem !important;
        white-space: nowrap !important;
    }

    .dark .aura-mini-label {
        color: #94a3b8 !important;
    }

    /* --------------------------------------------------------------------------
       MODERN DENTAL SAAS TABLE STYLING
       -------------------------------------------------------------------------- */
    .fi-ta {
        display: flex !important;
        flex-direction: column !important;
        gap: 0.85rem !important;
    }

    .fi-ta-ctn {
        border-radius: 1.25rem !important;
        border: 1px solid #e2e8f0 !important;
        background-color: #ffffff !important;
        box-shadow: 0 4px 20px -2px rgba(15, 23, 42, 0.04) !important;
        overflow: hidden !important;
    }

    .dark .fi-ta-ctn {
        border-color: rgba(255, 255, 255, 0.08) !important;
        background-color: #0c182d !important;
        box-shadow: 0 10px 30px -4px rgba(0, 0, 0, 0.4) !important;
    }

    /* Table Toolbar (Search & Filter bar) */
    .fi-ta-header-toolbar {
        padding: 0.85rem 1.15rem !important;
        border-bottom: 1px solid #f1f5f9 !important;
        background-color: #ffffff !important;
        display: flex !important;
        align-items: center !important;
        gap: 0.75rem !important;
    }

    .dark .fi-ta-header-toolbar {
        background-color: #0c182d !important;
        border-bottom-color: rgba(255, 255, 255, 0.06) !important;
    }

    /* Search input pill */
    .fi-ta-search-field input {
        border-radius: 0.85rem !important;
        border: 1px solid #e2e8f0 !important;
        font-size: 0.84rem !important;
        background-color: #f8fafc !important;
        padding-block: 0.55rem !important;
        transition: all 0.2s ease !important;
    }

    .fi-ta-search-field input:focus {
        border-color: #0284c7 !important;
        background-color: #ffffff !important;
        box-shadow: 0 0 0 3px rgba(2, 132, 199, 0.15) !important;
    }

    .dark .fi-ta-search-field input {
        background-color: rgba(15, 23, 42, 0.6) !important;
        border-color: rgba(255, 255, 255, 0.1) !important;
        color: #f8fafc !important;
    }

    .dark .fi-ta-search-field input:focus {
        border-color: #38bdf8 !important;
        background-color: #0f172a !important;
        box-shadow: 0 0 0 3px rgba(56, 189, 248, 0.2) !important;
    }

    /* Filter button next to search */
    button[wire\\:click*="openFilters"],
    .fi-ta-filter-trigger button,
    .fi-ta-header-toolbar button:has(svg) {
        border-radius: 0.85rem !important;
        font-weight: 600 !important;
        font-size: 0.825rem !important;
        padding-inline: 1rem !important;
        padding-block: 0.52rem !important;
        transition: all 0.15s ease !important;
    }

    /* Table Header cells */
    .fi-ta-header-cell {
        background-color: #f8fafc !important;
        color: #475569 !important;
        font-size: 0.725rem !important;
        font-weight: 700 !important;
        letter-spacing: 0.05em !important;
        text-transform: uppercase !important;
        padding-block: 0.85rem !important;
        padding-inline: 1rem !important;
        border-bottom: 1px solid #e2e8f0 !important;
    }

    .dark .fi-ta-header-cell {
        background-color: #0b1424 !important;
        color: #94a3b8 !important;
        border-bottom-color: rgba(255, 255, 255, 0.06) !important;
    }

    /* Table Rows */
    .fi-ta-row {
        transition: background-color 0.15s ease !important;
    }

    .fi-ta-row:hover {
        background-color: #f8fafc !important;
    }

    .dark .fi-ta-row:hover {
        background-color: rgba(30, 41, 59, 0.4) !important;
    }

    .fi-ta-cell {
        padding-block: 0.95rem !important;
        padding-inline: 1rem !important;
        border-bottom: 1px solid #f1f5f9 !important;
    }

    .dark .fi-ta-cell {
        border-bottom-color: rgba(255, 255, 255, 0.04) !important;
    }
</style>

