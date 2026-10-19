<div>
    <!-- Slide-over Instantáneo de Especialistas (0ms con Skeleton Loader) -->
    @include('filament.resources.dentists.slide-over-drawer')

    <!-- Interceptor para que cualquier clic en la fila abra el detalle del especialista, respetando las acciones (editar, eliminar, toggle) -->
    <script>
        (function() {
            function handleDentistRowClick(event) {
                // Si el clic fue en un botón, enlace, input, toggle o menú de acciones, ignorar
                if (event.target.closest('button, a, input, select, textarea, .fi-ta-actions, [data-filament-dropdown-trigger], .fi-ta-toggle-column')) {
                    return;
                }

                // Buscar la fila en la tabla
                const row = event.target.closest('tr');
                if (!row) return;

                // Extraer el ID desde la clase dentist-row-X
                const match = (row.className || '').match(/dentist-row-(\d+)/);
                const dentistId = match ? match[1] : null;
                if (!dentistId) return;

                window.dispatchEvent(new CustomEvent('open-dentist-drawer', {
                    detail: { id: parseInt(dentistId, 10) }
                }));
            }

            if (!window._dentistRowClickAttached) {
                window._dentistRowClickAttached = true;
                document.addEventListener('click', handleDentistRowClick, true);
            }
        })();
    </script>
</div>
