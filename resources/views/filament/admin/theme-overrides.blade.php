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
</style>
