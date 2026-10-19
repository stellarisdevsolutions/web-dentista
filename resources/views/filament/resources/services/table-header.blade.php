<div>
    <!-- Slide-over Instantáneo de Tratamientos (0ms con Skeleton Loader) -->
    @include('filament.resources.services.slide-over-drawer')

    <!-- Estilos para cursor en celdas interactivas -->
    <style>
        .service-row td:has(.fi-ta-toggle),
        .service-row td:has(.fi-ta-actions) {
            cursor: default;
        }
        .service-row td:has(.fi-ta-toggle) .fi-toggle,
        .service-row td:has(.fi-ta-actions) button {
            cursor: pointer;
        }
    </style>

    <!-- Interceptor para que cualquier clic en la fila abra el detalle del tratamiento, respetando las acciones (editar, eliminar, toggle) -->
    <script>
        (function() {
            function handleServiceRowClick(event) {
                // Si el clic fue en un botón, enlace, input, toggle, switch o menú de acciones, ignorar
                if (event.target.closest('button, a, input, select, textarea, label, [role="button"], [role="switch"], [data-filament-dropdown-trigger], .fi-ta-actions, .fi-ta-action, .fi-ta-toggle, .fi-toggle, .fi-copyable, [x-data*="toggleTableColumn"]')) {
                    return;
                }

                // Si el clic fue dentro de una celda que contiene un toggle o acciones, ignorar
                const cell = event.target.closest('td');
                if (cell && (cell.querySelector('.fi-ta-toggle, .fi-ta-checkbox, .fi-ta-select, .fi-ta-text-input, .fi-ta-actions') || cell.classList.contains('fi-ta-selection-cell'))) {
                    return;
                }

                // Buscar la fila en la tabla
                const row = event.target.closest('tr');
                if (!row) return;

                // Extraer el ID desde la clase service-row-X
                const match = (row.className || '').match(/service-row-(\d+)/);
                const serviceId = match ? match[1] : null;
                if (!serviceId) return;

                window.dispatchEvent(new CustomEvent('open-service-drawer', {
                    detail: { id: parseInt(serviceId, 10) }
                }));
            }

            if (!window._serviceRowClickAttached) {
                window._serviceRowClickAttached = true;
                document.addEventListener('click', handleServiceRowClick, true);
            }
        })();
    </script>
</div>
