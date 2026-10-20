<div>
    <!-- Slide-over Instantáneo de Usuarios (0ms con Skeleton Loader) -->
    @include('filament.resources.users.slide-over-drawer')

    <!-- Estilos para cursor en celdas interactivas -->
    <style>
        .user-row td:has(.fi-ta-actions) {
            cursor: default;
        }
        .user-row td:has(.fi-ta-actions) button {
            cursor: pointer;
        }
    </style>

    <!-- Interceptor para que cualquier clic en la fila abra el detalle del usuario, respetando las acciones (editar, eliminar, copiar correo) -->
    <script>
        (function() {
            function handleUserRowClick(event) {
                // Si el clic fue en un botón, enlace, input, toggle o menú de acciones, ignorar
                if (event.target.closest('button, a, input, select, textarea, label, [role="button"], [data-filament-dropdown-trigger], .fi-ta-actions, .fi-ta-action, .fi-copyable')) {
                    return;
                }

                // Si el clic fue dentro de una celda que contiene acciones o checkboxes, ignorar
                const cell = event.target.closest('td');
                if (cell && (cell.querySelector('.fi-ta-actions') || cell.classList.contains('fi-ta-selection-cell'))) {
                    return;
                }

                // Buscar la fila en la tabla
                const row = event.target.closest('tr');
                if (!row) return;

                // Extraer el ID desde la clase user-row-X
                const match = (row.className || '').match(/user-row-(\d+)/);
                const userId = match ? match[1] : null;
                if (!userId) return;

                window.dispatchEvent(new CustomEvent('open-user-drawer', {
                    detail: { id: parseInt(userId, 10) }
                }));
            }

            if (!window._userRowClickAttached) {
                window._userRowClickAttached = true;
                document.addEventListener('click', handleUserRowClick, true);
            }
        })();
    </script>
</div>
