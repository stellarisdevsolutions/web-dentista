<div>
    <!-- Slide-over Instantáneo de Tratamientos (0ms con Skeleton Loader) -->
    @include('filament.resources.services.slide-over-drawer')

    <!-- Interceptor para que cualquier clic en la fila abra el detalle del tratamiento, respetando las acciones (editar, eliminar, toggle) -->
    <script>
        (function() {
            function handleServiceRowClick(event) {
                // Si el clic fue en un botón, enlace, input, toggle o menú de acciones, ignorar
                if (event.target.closest('button, a, input, select, textarea, .fi-ta-actions, [data-filament-dropdown-trigger], .fi-ta-toggle-column')) {
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
