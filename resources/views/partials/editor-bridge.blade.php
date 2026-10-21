<style>
    [data-cms-section] {
        position: relative !important;
        cursor: pointer !important;
        transition: outline 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
    }
    
    /* Hover: Borde verde esmeralda punteado suave */
    [data-cms-section]:hover {
        outline: 2px dashed #10b981 !important;
        outline-offset: -2px;
    }

    /* Activo: Borde verde esmeralda sólido de 3px */
    [data-cms-section].is-editor-active {
        outline: 3px solid #10b981 !important;
        outline-offset: -3px;
        box-shadow: inset 0 0 0 3px rgba(16, 185, 129, 0.15) !important;
    }

    /* Badge flotante */
    .cms-floating-badge {
        position: absolute;
        top: 10px;
        left: 10px;
        background: #10b981;
        color: #ffffff;
        font-family: ui-sans-serif, system-ui, sans-serif;
        font-size: 11px;
        font-weight: 700;
        padding: 4px 10px;
        border-radius: 8px;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.2);
        z-index: 99999;
        pointer-events: none;
        letter-spacing: 0.05em;
        text-transform: uppercase;
        display: flex;
        align-items: center;
        gap: 5px;
    }

    /* En la previsualización del editor, deshabilitar navegación de enlaces en el header y footer */
    header a, footer a {
        cursor: default !important;
    }
</style>

<script>
    (function () {
        if (!window.location.search.includes('editor_preview=1')) return;

        let activeBadge = null;

        // 1. Interceptar clics para evitar salir del editor y destacar secciones
        document.addEventListener('click', function (e) {
            // Evitar navegación externa en enlaces <a> (header, footer y secciones) para no salir del lienzo del editor
            const anchor = e.target.closest('a');
            if (anchor) {
                const href = anchor.getAttribute('href') || anchor.getAttribute(':href');
                if (anchor.closest('header') || anchor.closest('footer') || (href && !href.startsWith('#') && !href.startsWith('javascript:'))) {
                    e.preventDefault();

                    // Si el usuario intentó navegar desde el header o footer, informar al panel editor
                    if ((anchor.closest('header') || anchor.closest('footer')) && window.parent && window.parent !== window) {
                        window.parent.postMessage({
                            type: 'CMS_NAVBAR_CLICKED'
                        }, '*');
                    }
                }
            }

            const section = e.target.closest('[data-cms-section]');
            if (section) {
                const sectionKey = section.getAttribute('data-cms-section');
                highlightSection(sectionKey);

                if (window.parent && window.parent !== window) {
                    window.parent.postMessage({
                        type: 'CMS_SECTION_CLICKED',
                        sectionKey: sectionKey
                    }, '*');
                }

                // NOTA: No llamamos a e.stopPropagation() para permitir que los botones interactivos
                // internos (como < y > del slider de testimonios, acordeones de FAQ, etc.) ejecuten sus handlers de Alpine.js
            }
        }, true);

        function highlightSection(sectionKey) {
            document.querySelectorAll('[data-cms-section]').forEach(el => {
                el.classList.remove('is-editor-active');
            });

            if (activeBadge) {
                activeBadge.remove();
                activeBadge = null;
            }

            const target = document.querySelector(`[data-cms-section="${sectionKey}"]`);
            if (target) {
                target.classList.add('is-editor-active');
                
                activeBadge = document.createElement('div');
                activeBadge.className = 'cms-floating-badge';
                activeBadge.innerHTML = '<svg style="width:12px;height:12px;" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-width="2.5" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg> ' + (target.getAttribute('data-cms-name') || sectionKey);
                target.appendChild(activeBadge);

                target.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
        }

        // 2. Escuchar mensajes del Panel Editor
        window.addEventListener('message', function (event) {
            if (!event.data || !event.data.type) return;

            if (event.data.type === 'SELECT_SECTION') {
                highlightSection(event.data.sectionKey);
            }

            if (event.data.type === 'DESELECT_SECTION') {
                document.querySelectorAll('[data-cms-section]').forEach(el => {
                    el.classList.remove('is-editor-active');
                });
                if (activeBadge) {
                    activeBadge.remove();
                    activeBadge = null;
                }
            }

            if (event.data.type === 'TOGGLE_VISIBILITY') {
                const target = document.querySelector(`[data-cms-section="${event.data.sectionKey}"]`);
                if (target) {
                    target.style.display = event.data.isActive ? '' : 'none';
                }
            }

            // Actualización atómica en tiempo real campo por campo (ultra rápido al teclear)
            if (event.data.type === 'UPDATE_FIELD') {
                const { sectionKey, fieldKey, value } = event.data;
                const section = document.querySelector(`[data-cms-section="${sectionKey}"]`);
                if (section) {
                    const nodes = section.querySelectorAll(`[data-cms-field="${fieldKey}"]`);
                    nodes.forEach(node => {
                        if (node.tagName === 'IMG') {
                            if (value) node.src = value;
                        } else {
                            node.textContent = value;
                        }
                    });

                    // Sincronizar visibilidad de filas condicionales (beneficios, checkmarks, pasos)
                    const row = section.querySelector(`[data-cms-row="${fieldKey}"]`);
                    if (row) {
                        const hasContent = Boolean(value && String(value).trim().length > 0);
                        row.style.display = hasContent ? '' : 'none';

                        const group = row.closest('[data-cms-group]');
                        if (group) {
                            const allRows = group.querySelectorAll('[data-cms-row]');
                            const anyVisible = Array.from(allRows).some(r => r.style.display !== 'none');
                            group.style.display = anyVisible ? '' : 'none';
                        }
                    }
                }
            }

            // Actualización masiva de la sección
            if (event.data.type === 'UPDATE_SECTION_CONTENT') {
                const { sectionKey, content } = event.data;
                const section = document.querySelector(`[data-cms-section="${sectionKey}"]`);
                if (!section || !content) return;

                Object.keys(content).forEach(key => {
                    const value = content[key];
                    if (Array.isArray(value)) {
                        window.dispatchEvent(new CustomEvent(`cms-${sectionKey}-${key}-updated`, {
                            detail: value
                        }));
                    } else {
                        const nodes = section.querySelectorAll(`[data-cms-field="${key}"]`);
                        nodes.forEach(node => {
                            if (node.tagName === 'IMG') {
                                if (value) node.src = value;
                            } else {
                                node.textContent = value;
                            }
                        });

                        const row = section.querySelector(`[data-cms-row="${key}"]`);
                        if (row) {
                            const hasContent = Boolean(value && String(value).trim().length > 0);
                            row.style.display = hasContent ? '' : 'none';

                            const group = row.closest('[data-cms-group]');
                            if (group) {
                                const allRows = group.querySelectorAll('[data-cms-row]');
                                const anyVisible = Array.from(allRows).some(r => r.style.display !== 'none');
                                group.style.display = anyVisible ? '' : 'none';
                            }
                        }
                    }
                });

                window.dispatchEvent(new CustomEvent(`cms-${sectionKey}-updated`, {
                    detail: content
                }));
            }

            // Previsualizar un índice específico (ej. cambiar al testimonio N al seleccionarlo en el editor)
            if (event.data.type === 'PREVIEW_TESTIMONIAL_INDEX') {
                window.dispatchEvent(new CustomEvent('cms-preview-testimonial-index', {
                    detail: event.data.index
                }));
            }

            // Previsualizar un índice de FAQ (abrir el acordeón seleccionado)
            if (event.data.type === 'PREVIEW_FAQ_INDEX') {
                window.dispatchEvent(new CustomEvent('cms-preview-faq-index', {
                    detail: event.data.index
                }));
            }
        });

        // Notificar al editor que el iframe está listo
        if (window.parent && window.parent !== window) {
            window.parent.postMessage({ type: 'CMS_BRIDGE_READY' }, '*');
        }
    })();
</script>
