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

    /* Minimal inline Chart.js plugin to draw values on top of bar charts */
    (function() {
        const barValuesOnTopPlugin = {
            id: 'barValuesOnTop',
            afterDatasetsDraw(chart) {
                if (chart.config.type !== 'bar') return;
                if (!chart.options.plugins?.barValuesOnTop?.display) return;
                const ctx = chart.ctx;
                const isDark = document.documentElement.classList.contains('dark');
                const textColor = isDark ? '#F1F5F9' : '#0F172A';

                chart.data.datasets.forEach((dataset, i) => {
                    const meta = chart.getDatasetMeta(i);
                    if (!meta || !meta.data) return;
                    meta.data.forEach((bar, index) => {
                        const value = dataset.data[index];
                        if (value > 0) {
                            ctx.save();
                            ctx.fillStyle = textColor;
                            ctx.font = '800 12px Manrope, sans-serif';
                            ctx.textAlign = 'center';
                            ctx.textBaseline = 'bottom';
                            const targetY = Math.max((chart.chartArea?.top || 0) + 14, bar.y - 6);
                            ctx.fillText(value, bar.x, targetY);
                            ctx.restore();
                        }
                    });
                });
            }
        };

        window.filamentChartJsGlobalPlugins = window.filamentChartJsGlobalPlugins || [];
        window.filamentChartJsGlobalPlugins.push(barValuesOnTopPlugin);
    })();
</script>

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">

<style>
    /* ==========================================================================
       AURA DENTAL STUDIO - MODERN NAVY MEDICAL THEME (POLISHED SQUIRCLE DOCK)
       ========================================================================== */

    :root {
        --sidebar-width: 17rem;
        --collapsed-sidebar-width: 4.75rem;
        --sidebar-bg: #0A1730;               /* Color base: #0A1730 (azul marino) */
        --sidebar-bg-hover: #16294D;         /* Hover: fondo #16294D */
        --sidebar-border: rgba(255, 255, 255, 0.06);
        --accent-blue-active: #0A74B8;
        --accent-blue-gradient: linear-gradient(90deg, #0A74B8, #0EA5E9);
        --accent-cyan: #0EA5E9;

        /* Aura System Design Tokens */
        --aura-navy: #0A1730;
        --aura-cyan: #0EA5E9;
        --aura-sky: #38BDF8;
        --aura-muted: #9FB3D1;
        --aura-amber: #FDE68A;
        --aura-blue-dark: #0A74B8;
        --aura-blue-soft: #7DD3FC;
        --aura-blue-tint: #CFE3F4;
        --aura-slate-border: #E6ECF4;
        --aura-slate-text: #5B6B86;
        --aura-group-label: #6F83A6;
    }

    /* Fondo general del canvas tipo aplicación moderna */
    .fi-body {
        background-color: #f1f5f9 !important;
    }

    .dark .fi-body {
        background-color: #050b14 !important;
    }

    /* Screen reader only utility (accesible para lectores de pantalla, invisible visualmente) */
    .sr-only,
    .fi-sr-only {
        position: absolute !important;
        width: 1px !important;
        height: 1px !important;
        padding: 0 !important;
        margin: -1px !important;
        overflow: hidden !important;
        clip: rect(0, 0, 0, 0) !important;
        white-space: nowrap !important;
        border: 0 !important;
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
            top: 16px !important;
            margin: 16px !important;
            height: calc(100dvh - 32px) !important;
            border-radius: 28px !important;
            border: 1px solid var(--sidebar-border) !important;
            box-shadow: 0 14px 40px rgba(15, 61, 140, 0.30) !important;
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

    /* Títulos de grupos (ADMINISTRACIÓN, GESTIÓN CLÍNICA, CMS Y CONTENIDO WEB, etc.) */
    .fi-sidebar-group-label {
        color: #6F83A6 !important;
        font-size: 0.72rem !important;
        font-weight: 700 !important;
        letter-spacing: 0.08em !important;
        text-transform: uppercase !important;
        padding-inline: 0.5rem !important;
    }

    .fi-sidebar-group-collapse-btn {
        color: #6F83A6 !important;
    }

    /* Links normales (inactivos) */
    .fi-sidebar-item-btn {
        border-radius: 14px !important;
        padding: 12px 14px !important;
        color: #9FB3D1 !important;
        font-weight: 500 !important;
        font-size: 0.9rem !important;
        transition: all 0.15s ease-in-out !important;
    }

    .fi-sidebar-item-btn:hover {
        background-color: #16294D !important;
        background: #16294D !important;
        color: #FFFFFF !important;
    }

    /* Iconos inactivos */
    .fi-sidebar-item-btn .fi-icon {
        color: #9FB3D1 !important;
        opacity: 1 !important;
        width: 1.25rem !important;
        height: 1.25rem !important;
        transition: transform 0.15s ease, color 0.15s ease !important;
    }

    .fi-sidebar-item-btn:hover .fi-icon {
        color: #FFFFFF !important;
        transform: scale(1.05) !important;
    }

    .fi-sidebar-item-label {
        color: #9FB3D1 !important;
        font-weight: 500 !important;
    }

    .fi-sidebar-item-btn:hover .fi-sidebar-item-label {
        color: #FFFFFF !important;
    }

    /* Módulo seleccionado (activo) */
    .fi-sidebar-item.fi-active .fi-sidebar-item-btn,
    .fi-sidebar-item-btn.fi-active {
        background: linear-gradient(90deg, #0A74B8, #0EA5E9) !important;
        color: #FFFFFF !important;
        box-shadow: 0 10px 22px rgba(14, 165, 233, 0.35) !important;
        border-radius: 14px !important;
        padding: 12px 14px !important;
    }

    .fi-sidebar-item.fi-active .fi-sidebar-item-btn .fi-icon,
    .fi-sidebar-item-btn.fi-active .fi-icon {
        color: #FFFFFF !important;
        opacity: 1 !important;
    }

    .fi-sidebar-item.fi-active .fi-sidebar-item-label,
    .fi-sidebar-item-btn.fi-active .fi-sidebar-item-label {
        font-weight: 600 !important;
        color: #FFFFFF !important;
    }

    /* Refinamiento específico para Modo Oscuro (.dark) */
    .dark .fi-sidebar {
        background-color: #071122 !important;
        background: #071122 !important;
    }

    @media (min-width: 1024px) {
        .dark .fi-body-has-topbar .fi-sidebar {
            background-color: #081326 !important;
            border: 1px solid rgba(255, 255, 255, 0.08) !important;
            box-shadow: 0 16px 44px rgba(0, 0, 0, 0.7), 0 0 0 1px rgba(255, 255, 255, 0.05) !important;
        }

        .dark .fi-sidebar-header-ctn {
            background-color: #081326 !important;
            border-bottom: 1px solid rgba(255, 255, 255, 0.08) !important;
        }
    }

    .dark .fi-sidebar-item-btn {
        color: #9FB3D1 !important;
    }

    .dark .fi-sidebar-item-btn .fi-icon {
        color: #9FB3D1 !important;
    }

    .dark .fi-sidebar-item-btn:hover {
        background-color: #142545 !important;
        background: #142545 !important;
        color: #FFFFFF !important;
    }

    .dark .fi-sidebar-item-btn:hover .fi-icon,
    .dark .fi-sidebar-item-btn:hover .fi-sidebar-item-label {
        color: #FFFFFF !important;
    }

    .dark .fi-sidebar-item.fi-active .fi-sidebar-item-btn,
    .dark .fi-sidebar-item-btn.fi-active {
        background: linear-gradient(90deg, #0A74B8, #0EA5E9) !important;
        color: #FFFFFF !important;
        box-shadow: 0 10px 24px rgba(14, 165, 233, 0.45) !important;
    }

    .dark .fi-sidebar-group-label,
    .dark .fi-sidebar-group-collapse-btn {
        color: #6F83A6 !important;
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
            border-radius: 14px !important;
            color: #9FB3D1 !important;
        }

        .fi-sidebar:not(.fi-sidebar-open) .fi-sidebar-item-btn:hover {
            background-color: #16294D !important;
            color: #FFFFFF !important;
        }

        .fi-sidebar:not(.fi-sidebar-open) .fi-sidebar-item-btn .fi-icon {
            color: #9FB3D1 !important;
            margin: 0 auto !important;
        }

        .fi-sidebar:not(.fi-sidebar-open) .fi-sidebar-item-btn:hover .fi-icon {
            color: #FFFFFF !important;
        }

        /* Ítem Activo en modo colapsado */
        .fi-sidebar:not(.fi-sidebar-open) .fi-sidebar-item.fi-active .fi-sidebar-item-btn,
        .fi-sidebar:not(.fi-sidebar-open) .fi-sidebar-item-btn.fi-active {
            border-radius: 14px !important;
            background: linear-gradient(90deg, #0A74B8, #0EA5E9) !important;
            color: #FFFFFF !important;
            box-shadow: 0 10px 22px rgba(14, 165, 233, 0.35) !important;
        }

        .fi-sidebar:not(.fi-sidebar-open) .fi-sidebar-item.fi-active .fi-sidebar-item-btn .fi-icon,
        .fi-sidebar:not(.fi-sidebar-open) .fi-sidebar-item-btn.fi-active .fi-icon {
            color: #FFFFFF !important;
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
       FILA 5: TABLA DE CITAS (8 COLS) Y PANEL DE ESPECIALISTAS (4 COLS) EN LA MISMA FILA
       -------------------------------------------------------------------------- */
    @media (min-width: 1024px) {
        .fi-wi-appointments-feed,
        .fi-wi:has(.fi-wi-appointments-feed),
        .fi-wi:has(.aura-feed-container),
        .fi-wi > div:has(.aura-feed-container) {
            grid-column: span 8 / span 8 !important;
            width: 100% !important;
            max-width: 100% !important;
        }

        .fi-wi-today-specialists,
        .fi-wi:has(.fi-wi-today-specialists),
        .fi-wi:has(.aura-specialists-card),
        .fi-wi > div:has(.aura-specialists-card) {
            grid-column: span 4 / span 4 !important;
            width: 100% !important;
            max-width: 100% !important;
        }

        /* Si el widget de especialistas no existe (rol dentista), la tabla ocupa las 12 columnas */
        .fi-page:not(:has(.aura-specialists-card)) .fi-wi:has(.fi-wi-appointments-feed),
        .fi-page:not(:has(.aura-specialists-card)) .fi-wi:has(.aura-feed-container),
        .fi-wi:has(.fi-wi-appointments-feed):last-child {
            grid-column: 1 / -1 !important;
        }
    }

    @media (max-width: 1023px) {
        .fi-wi-appointments-feed,
        .fi-wi:has(.fi-wi-appointments-feed),
        .fi-wi:has(.aura-feed-container),
        .fi-wi:has(.fi-wi-today-specialists),
        .fi-wi:has(.aura-specialists-card) {
            grid-column: 1 / -1 !important;
            width: 100% !important;
        }
    }

    /* ==========================================================================
       AURA DENTAL - CHARTS ROW (DEMAND 1.5fr & DISTRIBUTION 1fr)
       ========================================================================== */
    .aura-chart-card {
        background: #ffffff !important;
        border-radius: 28px !important;
        padding: 24px !important;
        box-shadow: 0 10px 34px rgba(10, 23, 48, 0.07) !important;
        border: 1px solid rgba(230, 236, 244, 0.7) !important;
        box-sizing: border-box !important;
        position: relative;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        min-height: 340px;
        container-type: inline-size;
    }

    .dark .aura-chart-card {
        background: #0c182d !important;
        border-color: rgba(255, 255, 255, 0.08) !important;
        box-shadow: 0 10px 34px rgba(0, 0, 0, 0.3) !important;
    }

    .aura-chart-title {
        font-size: 18px !important;
        font-weight: 800 !important;
        color: #0A1730 !important;
        letter-spacing: -0.02em !important;
        margin: 0 0 14px 0 !important;
        line-height: 1.3 !important;
    }

    .dark .aura-chart-title {
        color: #ffffff !important;
    }

    /* Demand Chart Legend */
    .aura-demand-legend {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 24px;
        margin-top: 14px;
        padding-top: 12px;
        border-top: 1px solid #F1F5F9;
    }

    .dark .aura-demand-legend {
        border-top-color: rgba(255, 255, 255, 0.08);
    }

    .aura-demand-legend-item {
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }

    .aura-demand-dot {
        width: 10px;
        height: 10px;
        border-radius: 3px;
        flex-shrink: 0;
    }

    .aura-demand-legend-text {
        font-size: 12px;
        font-weight: 600;
        color: #5B6B86;
    }

    .dark .aura-demand-legend-text {
        color: #94A3B8;
    }

    /* Demand empty overlay */
    .aura-demand-empty-overlay {
        position: absolute;
        inset: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        pointer-events: none;
    }

    .aura-demand-empty-badge {
        background: rgba(241, 245, 249, 0.95);
        color: #64748B;
        font-size: 13px;
        font-weight: 600;
        padding: 6px 14px;
        border-radius: 9999px;
        border: 1px solid #E2E8F0;
    }

    .dark .aura-demand-empty-badge {
        background: rgba(15, 23, 42, 0.95);
        color: #94A3B8;
        border-color: rgba(255, 255, 255, 0.1);
    }

    /* Donut Chart Layout */
    .aura-donut-body {
        display: flex;
        flex-direction: row;
        align-items: center;
        justify-content: space-around;
        gap: 16px;
        width: 100%;
        flex: 1;
    }

    @container (max-width: 440px) {
        .aura-donut-body {
            flex-direction: column !important;
            gap: 16px !important;
        }
    }

    .aura-donut-empty-overlay {
        position: absolute;
        inset: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        text-align: center;
        padding: 0 28px;
        pointer-events: none;
    }

    .aura-donut-empty-text {
        font-size: 13px;
        font-weight: 600;
        color: #64748B;
        line-height: 1.4;
    }

    .dark .aura-donut-empty-text {
        color: #94A3B8;
    }

    .aura-donut-legend {
        display: flex;
        flex-direction: column;
        gap: 10px;
        min-width: 0;
        flex: 1;
    }

    .aura-donut-legend-item {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 13px;
        min-width: 0;
    }

    .aura-donut-legend-box {
        width: 12px;
        height: 12px;
        border-radius: 3px;
        flex-shrink: 0;
    }

    .aura-donut-legend-name {
        font-weight: 600;
        color: #1E293B;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        min-width: 0;
        flex-shrink: 1;
    }

    .dark .aura-donut-legend-name {
        color: #E2E8F0;
    }

    .aura-donut-legend-sep {
        color: #94A3B8;
        flex-shrink: 0;
    }

    .aura-donut-legend-pct {
        font-weight: 700;
        color: #0A1730;
        flex-shrink: 0;
    }

    .dark .aura-donut-legend-pct {
        color: #38BDF8;
    }

    .aura-donut-empty-legend-hint {
        font-size: 12px;
        color: #94A3B8;
        font-style: italic;
    }

    /* Stack charts when container is below 760px */
    @container (max-width: 760px) {
        .fi-wi:has(.aura-demand-widget-container),
        .fi-wi:has(.aura-donut-widget-container) {
            grid-column: 1 / -1 !important;
        }
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
       WELCOME BANNER WIDGET (SECTION 1 - HERO BANNER)
       -------------------------------------------------------------------------- */
    .fi-wi-welcome-banner,
    .fi-wi:has(.fi-wi-welcome-banner) {
        grid-column: 1 / -1 !important;
        width: 100% !important;
        max-width: 100% !important;
    }

    .fi-dashboard-page > .fi-header,
    .fi-page-dashboard > .fi-header {
        display: none !important;
    }

    .aura-welcome-banner {
        font-family: 'Manrope', -apple-system, BlinkMacSystemFont, sans-serif;
        width: 100%;
        border-radius: 32px;
        padding: 30px 34px;
        background: linear-gradient(120deg, var(--aura-navy, #0A1730) 30%, #0B4F80);
        color: #ffffff;
        box-shadow: 0 18px 40px rgba(10, 23, 48, 0.25);
        display: flex;
        flex-direction: row;
        flex-wrap: wrap;
        align-items: center;
        justify-content: space-between;
        gap: 24px;
        box-sizing: border-box;
    }

    .aura-welcome-left {
        display: flex;
        flex-direction: column;
        flex: 1 1 480px;
        min-width: 0;
    }

    .aura-welcome-title {
        font-size: 34px !important;
        font-weight: 800 !important;
        letter-spacing: -0.02em !important;
        line-height: 1.2 !important;
        color: #ffffff !important;
        margin: 0 !important;
        display: flex;
        align-items: baseline;
        flex-wrap: wrap;
        gap: 0.35rem;
    }

    .aura-welcome-name {
        color: var(--aura-sky, #38BDF8) !important;
        display: inline-block;
        max-width: 480px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        vertical-align: bottom;
    }

    .aura-welcome-subtitle {
        font-size: 15px !important;
        color: var(--aura-muted, #9FB3D1) !important;
        max-width: 520px;
        margin-top: 8px !important;
        margin-bottom: 0 !important;
        line-height: 1.5 !important;
        font-weight: 500 !important;
    }

    .aura-welcome-actions {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-top: 24px;
        flex-wrap: wrap;
    }

    .aura-welcome-btn-primary {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background-color: var(--aura-cyan, #0EA5E9);
        color: #0F172A !important;
        font-weight: 800;
        font-size: 14px;
        min-height: 48px;
        padding: 0 24px;
        border-radius: 9999px;
        box-shadow: 0 10px 24px rgba(14, 165, 233, 0.40);
        text-decoration: none;
        transition: transform 0.15s ease, box-shadow 0.15s ease, background-color 0.15s ease;
        box-sizing: border-box;
    }

    .aura-welcome-btn-primary:hover {
        background-color: #38bdf8;
        transform: translateY(-1px);
        box-shadow: 0 14px 28px rgba(14, 165, 233, 0.50);
    }

    .aura-welcome-btn-primary:active {
        transform: scale(0.98);
    }

    .aura-welcome-btn-secondary {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border: 1.5px solid var(--aura-sky, #38BDF8);
        background-color: transparent;
        color: #ffffff !important;
        font-weight: 700;
        font-size: 14px;
        min-height: 48px;
        padding: 0 24px;
        border-radius: 9999px;
        text-decoration: none;
        transition: background-color 0.15s ease, transform 0.15s ease, border-color 0.15s ease;
        box-sizing: border-box;
    }

    .aura-welcome-btn-secondary:hover {
        background-color: rgba(56, 189, 248, 0.15);
        border-color: #ffffff;
        transform: translateY(-1px);
    }

    .aura-welcome-btn-secondary:active {
        transform: scale(0.98);
    }

    .aura-welcome-cards {
        display: flex;
        align-items: center;
        gap: 16px;
        flex-shrink: 0;
        flex-wrap: wrap;
    }

    .aura-welcome-card {
        background: rgba(255, 255, 255, 0.10);
        backdrop-filter: blur(8px);
        -webkit-backdrop-filter: blur(8px);
        border: 1px solid rgba(255, 255, 255, 0.08);
        border-radius: 22px;
        padding: 16px 22px;
        min-width: 110px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        text-align: center;
        box-sizing: border-box;
    }

    .aura-welcome-card-num {
        font-size: 30px;
        font-weight: 800;
        line-height: 1.1;
        color: #ffffff;
    }

    .aura-welcome-card-num.aura-highlight {
        color: var(--aura-amber, #FDE68A);
    }

    .aura-welcome-card-label {
        font-size: 13px;
        color: var(--aura-muted, #9FB3D1);
        margin-top: 4px;
        text-transform: lowercase;
        font-weight: 600;
    }

    @media (max-width: 768px) {
        .aura-welcome-banner {
            padding: 24px 20px;
            border-radius: 24px;
            gap: 20px;
        }

        .aura-welcome-title {
            font-size: 26px !important;
        }

        .aura-welcome-cards {
            width: 100%;
            justify-content: flex-start;
        }

        .aura-welcome-card {
            flex: 1 1 calc(50% - 8px);
            min-width: 0;
        }
    }

    /* --------------------------------------------------------------------------
       KPI CARDS WIDGET (SECTION 2 - 4 CARDS WITH 4-ZONE LAYOUT & CONTAINER QUERIES)
       -------------------------------------------------------------------------- */
    .fi-wi-kpi-cards,
    .fi-wi:has(.fi-wi-kpi-cards) {
        grid-column: 1 / -1 !important;
        width: 100% !important;
        max-width: 100% !important;
    }

    .aura-kpi-container {
        container-type: inline-size;
        width: 100%;
        box-sizing: border-box;
    }

    .aura-kpi-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 16px;
        width: 100%;
        box-sizing: border-box;
    }

    /* Container Queries para adaptarse fielmente con sidebar abierto o cerrado */
    @container (max-width: 999px) {
        .aura-kpi-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @container (max-width: 559px) {
        .aura-kpi-grid {
            grid-template-columns: minmax(0, 1fr);
        }
    }

    /* Fallback con media queries por compatibilidad */
    @media (max-width: 1024px) {
        .aura-kpi-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 560px) {
        .aura-kpi-grid {
            grid-template-columns: minmax(0, 1fr);
        }
    }

    /* Estructura común para las 4 tarjetas: Flex column sin space-between global */
    .aura-kpi-card {
        border-radius: 28px;
        padding: 22px;
        display: flex;
        flex-direction: column;
        justify-content: flex-start;
        align-items: stretch;
        gap: 12px;
        min-height: 240px;
        height: 100%;
        box-sizing: border-box;
        overflow: visible;
        transition: transform 0.15s ease, box-shadow 0.15s ease;
        position: relative;
    }

    .aura-kpi-card,
    .aura-kpi-card * {
        min-width: 0;
    }

    .aura-kpi-card:hover {
        transform: translateY(-2px);
    }

    /* ZONA A: Cabecera fija a 40px sin solapamientos */
    .aura-kpi-zone-a {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 12px;
        min-height: 40px;
        width: 100%;
        flex-shrink: 0;
        position: static !important;
        margin: 0 !important;
    }

    .aura-kpi-label {
        font-size: 12px !important;
        font-weight: 800 !important;
        letter-spacing: 0.06em !important;
        text-transform: uppercase !important;
        line-height: 1.3 !important;
        flex: 1 1 auto;
        min-width: 0;
        white-space: normal !important;
        overflow-wrap: anywhere !important;
        word-break: normal;
    }

    /* ZONA B: Valor principal y subtexto con inicio uniforme en las 4 tarjetas */
    .aura-kpi-zone-b {
        display: flex;
        flex-direction: column;
        justify-content: flex-start;
        flex-shrink: 0;
    }

    .aura-kpi-num {
        font-size: 48px !important;
        font-weight: 800 !important;
        letter-spacing: -0.03em !important;
        line-height: 1 !important;
        display: block !important;
        font-variant-numeric: tabular-nums;
        font-feature-settings: "tnum" 1;
    }

    .aura-kpi-subtext {
        font-size: 13px !important;
        line-height: 1.3 !important;
        margin-top: 4px;
        display: block;
        white-space: normal;
        overflow-wrap: anywhere;
    }

    /* ZONA C: Contenido secundario (gráfico, botón, chips, anillo) */
    .aura-kpi-zone-c {
        display: flex;
        flex-direction: column;
        justify-content: flex-start;
        flex: 1 1 auto;
        width: 100%;
        min-width: 0;
    }

    /* ZONA D: Pie pegado al fondo */
    .aura-kpi-zone-d {
        margin-top: auto;
        display: flex;
        align-items: center;
        justify-content: flex-start;
        width: 100%;
        flex-shrink: 0;
    }

    /* -------------------------------------------------------------
       TARJETA 1: CITAS CONFIRMADAS HOY (Azul vibrante Aura)
       ------------------------------------------------------------- */
    .aura-kpi-card-1 {
        background: #0284c7;
        background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%);
        color: #ffffff;
        box-shadow: 0 14px 30px rgba(2, 132, 199, 0.35);
    }

    .aura-kpi-card-1 .aura-kpi-label {
        color: rgba(255, 255, 255, 0.95);
    }

    .aura-kpi-card-1 .aura-kpi-num {
        color: #ffffff;
    }

    .aura-kpi-card-1 .aura-kpi-subtext {
        color: rgba(255, 255, 255, 0.90);
    }

    .aura-kpi-icon-box {
        width: 32px;
        height: 32px;
        border-radius: 10px;
        background: rgba(255, 255, 255, 0.22);
        display: flex;
        align-items: center;
        justify-content: center;
        color: #ffffff;
        flex-shrink: 0;
        white-space: nowrap;
    }

    /* Píldoras redondeadas del mini-gráfico de 7 días */
    .aura-kpi-pills-trend {
        display: flex;
        align-items: flex-end;
        gap: 6px;
        height: 36px;
        width: 100%;
        margin-top: 8px;
    }

    .aura-kpi-pill-bar {
        width: 14px;
        border-radius: 9999px;
        background: rgba(255, 255, 255, 0.38);
        transition: height 0.2s ease, background 0.2s ease;
        flex-shrink: 0;
    }

    .aura-kpi-pill-bar.aura-kpi-pill-today {
        background: #ffffff !important;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
    }

    /* -------------------------------------------------------------
       TARJETA 2: POR CONFIRMAR
       ------------------------------------------------------------- */
    .aura-kpi-card-2 {
        background: #ffffff;
        color: #0f172a;
        box-shadow: 0 10px 34px rgba(10, 23, 48, 0.07);
        border: 1px solid rgba(226, 232, 240, 0.85);
    }

    .dark .aura-kpi-card-2 {
        background: #0c182d;
        color: #f8fafc;
        border-color: rgba(255, 255, 255, 0.08);
        box-shadow: 0 14px 32px rgba(0, 0, 0, 0.3);
    }

    .aura-kpi-card-2 .aura-kpi-label {
        color: #0f172a;
    }

    .dark .aura-kpi-card-2 .aura-kpi-label {
        color: #f8fafc;
    }

    .aura-kpi-card-2 .aura-kpi-num {
        color: #0f172a;
    }

    .dark .aura-kpi-card-2 .aura-kpi-num {
        color: #f8fafc;
    }

    .aura-kpi-card-2 .aura-kpi-subtext {
        color: #64748b;
    }

    .dark .aura-kpi-card-2 .aura-kpi-subtext {
        color: #94a3b8;
    }

    .aura-kpi-chip {
        padding: 4px 10px;
        border-radius: 9999px;
        font-size: 12px;
        font-weight: 700;
        letter-spacing: 0;
        text-transform: none;
        flex-shrink: 0;
        white-space: nowrap !important;
    }

    .aura-kpi-chip-amber {
        background-color: #FEF3C7;
        color: #B45309;
    }

    .aura-kpi-chip-green {
        background-color: #DCFCE7;
        color: #166534;
    }

    .aura-kpi-btn-amber {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        height: 40px;
        min-height: 40px;
        padding: 0 18px;
        border-radius: 9999px;
        background-color: #FEF3C7;
        color: #B45309 !important;
        font-size: 13px;
        font-weight: 800;
        text-decoration: none;
        transition: all 0.15s ease;
        width: fit-content;
        max-width: 100%;
        box-sizing: border-box;
        margin-top: 6px;
    }

    .aura-kpi-btn-amber:hover {
        background-color: #FDE68A;
        transform: translateY(-1px);
    }

    .aura-kpi-discreet-text {
        font-size: 12px;
        font-weight: 500;
        color: #94a3b8;
        display: block;
        line-height: 1.4;
        margin-top: 6px;
    }

    .dark .aura-kpi-discreet-text {
        color: #64748b;
    }

    /* -------------------------------------------------------------
       TARJETA 3: SERVICIOS CLÍNICOS
       ------------------------------------------------------------- */
    .aura-kpi-card-3 {
        background: #ffffff;
        color: #0f172a;
        box-shadow: 0 10px 34px rgba(10, 23, 48, 0.07);
        border: 1px solid rgba(226, 232, 240, 0.85);
    }

    .dark .aura-kpi-card-3 {
        background: #0c182d;
        color: #f8fafc;
        border-color: rgba(255, 255, 255, 0.08);
        box-shadow: 0 14px 32px rgba(0, 0, 0, 0.3);
    }

    .aura-kpi-card-3 .aura-kpi-label {
        color: #0f172a;
    }

    .dark .aura-kpi-card-3 .aura-kpi-label {
        color: #f8fafc;
    }

    .aura-kpi-card-3 .aura-kpi-num {
        color: #0f172a;
    }

    .dark .aura-kpi-card-3 .aura-kpi-num {
        color: #f8fafc;
    }

    .aura-kpi-card-3 .aura-kpi-subtext {
        color: #64748b;
    }

    .dark .aura-kpi-card-3 .aura-kpi-subtext {
        color: #94a3b8;
    }

    .aura-kpi-chip-blue {
        background-color: #E0F2FE;
        color: #0284c7;
    }

    /* Lista de chips de servicios en máximo 2 filas */
    .aura-kpi-services-zone {
        width: 100%;
    }

    .aura-kpi-services-list {
        display: flex;
        flex-direction: column;
        gap: 6px;
        width: 100%;
        margin-top: 6px;
    }

    .aura-kpi-services-row {
        display: flex;
        align-items: center;
        gap: 6px;
        width: 100%;
        min-width: 0;
    }

    .aura-kpi-services-row-1 {
        flex-wrap: nowrap;
    }

    .aura-kpi-services-row-2 {
        flex-wrap: nowrap;
    }

    .aura-kpi-service-pill {
        display: inline-block;
        padding: 5px 12px;
        border-radius: 9999px;
        background-color: #F1F5F9;
        color: #0F172A;
        font-size: 12px;
        font-weight: 700;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        border: 1px solid rgba(226, 232, 240, 0.9);
        box-sizing: border-box;
        max-width: 100%;
        min-width: 0;
        flex: 0 1 auto;
    }

    /* En la segunda fila, el nombre se acorta responsivamente de forma prioritaria para que el +N nunca se solape ni se oculte */
    .aura-kpi-service-pill-row2 {
        max-width: calc(100% - 44px);
        flex: 0 1 auto;
        flex-shrink: 1;
    }

    .dark .aura-kpi-service-pill {
        background-color: rgba(30, 41, 59, 0.7);
        color: #e2e8f0;
        border-color: rgba(255, 255, 255, 0.08);
    }

    .aura-kpi-service-pill-more {
        background-color: #E0F2FE;
        color: #0284c7;
        font-weight: 800;
        border-color: rgba(14, 165, 233, 0.25);
        flex-shrink: 0;
        cursor: default;
        padding: 5px 10px;
    }

    .dark .aura-kpi-service-pill-more {
        background-color: rgba(14, 165, 233, 0.15);
        color: #38bdf8;
    }

    /* -------------------------------------------------------------
       TARJETA 4: OCUPACIÓN DE LA AGENDA HOY
       (Gráfico a la izq, Número y % en el medio, Texto a la der)
       ------------------------------------------------------------- */
    .aura-kpi-card-4 {
        background: #0A1730;
        color: #ffffff;
        box-shadow: 0 14px 30px rgba(10, 23, 48, 0.25);
    }

    .aura-kpi-card-4 .aura-kpi-label {
        color: rgba(255, 255, 255, 0.92);
    }

    .aura-kpi-occupancy-zone {
        display: flex;
        flex-direction: column;
        justify-content: flex-start;
        width: 100%;
        margin-top: 0;
    }

    .aura-kpi-occupancy-row {
        display: flex;
        flex-direction: row;
        align-items: center;
        gap: 14px;
        width: 100%;
    }

    .aura-kpi-ring-wrapper {
        flex-shrink: 0;
        width: 88px;
        height: 88px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .aura-kpi-ring-svg {
        transform: rotate(-90deg);
        width: 88px;
        height: 88px;
    }

    /* Contenedor de datos: % grande + subtexto al lado */
    .aura-kpi-occupancy-data {
        display: flex;
        flex-direction: column;
        justify-content: center;
        flex: 1 1 auto;
        min-width: 0;
    }

    .aura-kpi-occupancy-val {
        font-size: 48px !important;
        font-weight: 800 !important;
        color: #ffffff !important;
        line-height: 1 !important;
        letter-spacing: -0.03em !important;
        font-variant-numeric: tabular-nums;
        font-feature-settings: "tnum" 1;
        display: block;
    }

    .aura-kpi-subtext-occupancy {
        font-size: 13px !important;
        color: #9FB3D1 !important;
        line-height: 1.25 !important;
        white-space: normal !important;
        overflow-wrap: anywhere !important;
        word-break: normal !important;
        margin-top: 4px !important;
        display: block;
    }

    .aura-kpi-free-text {
        font-size: 13px;
        color: #9FB3D1;
        letter-spacing: 0.01em;
    }

    .aura-kpi-free-text strong {
        color: #ffffff;
        weight: 800;
    }

    /* ==========================================================================
       SECCIÓN 5: ESPECIALISTAS Y ACCIONES DE CITAS POR ROL
       ========================================================================== */

    /* Panel de Especialistas */
    .aura-specialists-card {
        background: linear-gradient(170deg, #0A1730, #16294D) !important;
        color: #ffffff !important;
        border-radius: 32px !important;
        padding: 26px !important;
        box-shadow: 0 18px 40px rgba(10, 23, 48, 0.25) !important;
        box-sizing: border-box !important;
        display: flex !important;
        flex-direction: column !important;
        justify-content: space-between !important;
        min-height: 420px;
    }

    .aura-specialists-header {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 12px;
        margin-bottom: 20px;
    }

    .aura-specialists-title {
        font-size: 20px !important;
        font-weight: 800 !important;
        color: #ffffff !important;
        margin: 0 !important;
        line-height: 1.2 !important;
        letter-spacing: -0.02em !important;
    }

    .aura-specialists-subtitle {
        font-size: 13px !important;
        color: #9FB3D1 !important;
        margin: 4px 0 0 0 !important;
        line-height: 1.3 !important;
    }

    .aura-specialists-shift-chip {
        background: #DCFCE7 !important;
        color: #166534 !important;
        font-size: 12px !important;
        font-weight: 700 !important;
        padding: 4px 10px !important;
        border-radius: 9999px !important;
        white-space: nowrap !important;
        flex-shrink: 0 !important;
    }

    .aura-specialists-list {
        list-style: none !important;
        padding: 0 !important;
        margin: 0 !important;
        display: flex !important;
        flex-direction: column !important;
        gap: 12px !important;
    }

    .aura-specialists-list-scrollable {
        max-height: 380px !important;
        overflow-y: auto !important;
        padding-right: 4px !important;
    }

    .aura-specialist-item-row {
        background: #16294D !important;
        border-radius: 20px !important;
        padding: 12px !important;
        display: flex !important;
        align-items: center !important;
        gap: 12px !important;
        transition: background 0.15s ease, transform 0.15s ease !important;
        border: 1px solid rgba(255, 255, 255, 0.05) !important;
        box-sizing: border-box !important;
    }

    .aura-specialist-item-row:hover {
        background: #1C3360 !important;
    }

    .aura-specialist-item-row:focus-visible {
        outline: 2px solid #0EA5E9 !important;
        outline-offset: 2px !important;
    }

    .aura-specialist-avatar {
        width: 46px !important;
        height: 46px !important;
        border-radius: 9999px !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        flex-shrink: 0 !important;
        font-weight: 800 !important;
        font-size: 15px !important;
        color: #0F172A !important;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15) !important;
    }

    .aura-specialist-info {
        display: flex !important;
        flex-direction: column !important;
        min-width: 0 !important;
        flex: 1 1 auto !important;
        gap: 2px !important;
    }

    .aura-specialist-name {
        font-size: 14px !important;
        font-weight: 800 !important;
        color: #ffffff !important;
        white-space: nowrap !important;
        overflow: hidden !important;
        text-overflow: ellipsis !important;
        line-height: 1.25 !important;
    }

    .aura-specialist-specialty {
        font-size: 13px !important;
        color: #9FB3D1 !important;
        white-space: nowrap !important;
        overflow: hidden !important;
        text-overflow: ellipsis !important;
        line-height: 1.25 !important;
    }

    .aura-specialist-status-col {
        display: flex !important;
        flex-direction: column !important;
        align-items: flex-end !important;
        flex-shrink: 0 !important;
        gap: 4px !important;
        text-align: right !important;
    }

    .aura-specialist-chip {
        font-size: 11px !important;
        font-weight: 700 !important;
        padding: 3px 8px !important;
        border-radius: 9999px !important;
        white-space: nowrap !important;
        line-height: 1.2 !important;
    }

    .aura-specialist-citas-count {
        font-size: 12px !important;
        color: #9FB3D1 !important;
        line-height: 1 !important;
        font-weight: 500 !important;
    }

    .aura-specialists-see-all {
        margin-top: 10px;
        text-align: center;
    }

    .aura-specialists-see-all a {
        font-size: 12px;
        font-weight: 700;
        color: #38BDF8;
        text-decoration: underline;
    }

    .aura-specialists-footer {
        margin-top: 20px !important;
    }

    .aura-btn-specialists-action {
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        width: 100% !important;
        min-height: 48px !important;
        background: #0EA5E9 !important;
        color: #0F172A !important;
        font-size: 14px !important;
        font-weight: 800 !important;
        border-radius: 9999px !important;
        text-decoration: none !important;
        transition: background 0.15s ease, transform 0.15s ease !important;
        box-sizing: border-box !important;
    }

    .aura-btn-specialists-action:hover {
        background: #38BDF8 !important;
        transform: translateY(-1px) !important;
    }

    .aura-specialists-empty {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 40px 10px;
        text-align: center;
    }

    .aura-specialists-empty-icon {
        color: #64748B;
        margin-bottom: 12px;
    }

    .aura-specialists-empty-text {
        font-size: 14px;
        color: #9FB3D1;
        margin-bottom: 16px;
    }

    .aura-btn-specialist-add {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 8px 18px;
        background: #0EA5E9;
        color: #0F172A;
        font-weight: 700;
        font-size: 13px;
        border-radius: 9999px;
        text-decoration: none;
    }

    /* Acciones individuales en las filas de citas */
    .aura-feed-actions {
        display: flex;
        align-items: center;
        gap: 6px;
        flex-wrap: wrap;
        justify-content: flex-end;
    }

    .aura-row-btn {
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        font-size: 11px !important;
        font-weight: 700 !important;
        padding: 4px 10px !important;
        border-radius: 9999px !important;
        border: none !important;
        cursor: pointer !important;
        transition: all 0.15s ease !important;
        white-space: nowrap !important;
        line-height: 1.2 !important;
    }

    .aura-row-btn-confirm {
        background: #DCFCE7 !important;
        color: #166534 !important;
    }
    .aura-row-btn-confirm:hover {
        background: #BBF7D0 !important;
    }

    .aura-row-btn-start {
        background: #E0F2FE !important;
        color: #0A74B8 !important;
    }
    .aura-row-btn-start:hover {
        background: #BAE6FD !important;
    }

    .aura-row-btn-complete {
        background: #DCFCE7 !important;
        color: #166534 !important;
    }
    .aura-row-btn-complete:hover {
        background: #BBF7D0 !important;
    }

    .aura-row-btn-reschedule {
        background: #F1F5F9 !important;
        color: #475569 !important;
    }
    .aura-row-btn-reschedule:hover {
        background: #E2E8F0 !important;
    }

    .dark .aura-row-btn-reschedule {
        background: rgba(255, 255, 255, 0.1) !important;
        color: #CBD5E1 !important;
    }

    .aura-row-btn-cancel {
        background: #FEE2E2 !important;
        color: #991B1B !important;
    }
    .aura-row-btn-cancel:hover {
        background: #FECACA !important;
    }

    /* Modal de Reprogramación */
    .aura-reschedule-backdrop {
        position: fixed !important;
        inset: 0 !important;
        background: rgba(10, 23, 48, 0.6) !important;
        backdrop-filter: blur(4px) !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        z-index: 9999 !important;
    }

    .aura-reschedule-modal {
        background: #ffffff !important;
        border-radius: 24px !important;
        padding: 24px !important;
        max-width: 400px !important;
        width: 90% !important;
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.25) !important;
        box-sizing: border-box !important;
    }

    .dark .aura-reschedule-modal {
        background: #0f1c34 !important;
        color: #ffffff !important;
        border: 1px solid rgba(255, 255, 255, 0.1) !important;
    }

    .aura-reschedule-header {
        display: flex !important;
        align-items: center !important;
        justify-content: space-between !important;
        margin-bottom: 18px !important;
    }

    .aura-reschedule-title {
        font-size: 18px !important;
        font-weight: 800 !important;
        color: #0A1730 !important;
        margin: 0 !important;
    }

    .dark .aura-reschedule-title {
        color: #ffffff !important;
    }

    .aura-reschedule-close {
        background: none !important;
        border: none !important;
        font-size: 24px !important;
        color: #64748B !important;
        cursor: pointer !important;
        line-height: 1 !important;
    }

    .aura-reschedule-body {
        display: flex !important;
        flex-direction: column !important;
        gap: 14px !important;
        margin-bottom: 20px !important;
    }

    .aura-field-group {
        display: flex !important;
        flex-direction: column !important;
        gap: 6px !important;
    }

    .aura-label {
        font-size: 13px !important;
        font-weight: 600 !important;
        color: #475569 !important;
    }

    .dark .aura-label {
        color: #94A3B8 !important;
    }

    .aura-input {
        width: 100% !important;
        border-radius: 12px !important;
        border: 1px solid #CBD5E1 !important;
        padding: 8px 12px !important;
        font-size: 14px !important;
        box-sizing: border-box !important;
        background: #F8FAFC !important;
        color: #0F172A !important;
    }

    .dark .aura-input {
        background: #16294D !important;
        border-color: rgba(255, 255, 255, 0.15) !important;
        color: #ffffff !important;
    }

    .aura-reschedule-footer {
        display: flex !important;
        align-items: center !important;
        justify-content: flex-end !important;
        gap: 10px !important;
    }

    .aura-btn-modal-cancel {
        padding: 8px 16px !important;
        font-size: 13px !important;
        font-weight: 700 !important;
        background: #F1F5F9 !important;
        color: #475569 !important;
        border-radius: 9999px !important;
        border: none !important;
        cursor: pointer !important;
    }

    .dark .aura-btn-modal-cancel {
        background: rgba(255, 255, 255, 0.08) !important;
        color: #CBD5E1 !important;
    }

    .aura-btn-modal-save {
        padding: 8px 18px !important;
        font-size: 13px !important;
        font-weight: 800 !important;
        background: #0EA5E9 !important;
        color: #0F172A !important;
        border-radius: 9999px !important;
        border: none !important;
        cursor: pointer !important;
    }
</style>

