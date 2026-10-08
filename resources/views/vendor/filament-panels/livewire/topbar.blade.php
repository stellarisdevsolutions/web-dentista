@php
    use Filament\Enums\GlobalSearchPosition;
    use Filament\Enums\UserMenuPosition;
    use Filament\Livewire\GlobalSearch;
    use Filament\Support\Facades\FilamentView;
    use Filament\View\PanelsRenderHook;
@endphp

<div class="fi-topbar-ctn">
    @php
        $hasNavigation = filament()->hasNavigation();
    @endphp

    <nav
        aria-label="{{ __('filament-panels::layout.topbar.label') }}"
        class="fi-topbar"
    >
        {{ FilamentView::renderHook(PanelsRenderHook::TOPBAR_START) }}

        <div class="fi-topbar-start">
            {{-- 1. Botón de Hamburguesa para Colapsar/Expandir el Sidebar --}}
            @if ($hasNavigation)
                <button
                    type="button"
                    aria-label="{{ __('filament-panels::layout.actions.sidebar.expand.label') }}"
                    x-cloak
                    x-data="{}"
                    aria-controls="fi-main-sidebar"
                    x-bind:aria-expanded="$store.sidebar.isOpen"
                    x-on:click="$store.sidebar.isOpen ? $store.sidebar.close() : $store.sidebar.open()"
                    class="fi-topbar-hamburger-btn group"
                    title="Alternar menú lateral"
                >
                    <svg class="fi-topbar-hamburger-icon" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                    </svg>
                </button>
            @endif

            {{-- 2. Buscador Global posicionado inmediatamente después de la hamburguesa --}}
            @if (filament()->isGlobalSearchEnabled() && filament()->getGlobalSearchPosition() === GlobalSearchPosition::Topbar)
                <div class="fi-topbar-search-ctn">
                    @livewire(GlobalSearch::class)
                </div>
            @endif
        </div>

        <div class="fi-topbar-end">
            {{-- 3. Menú de Usuario (Avatar + Nombre + Dropdown) --}}
            @if (filament()->auth()->check() && filament()->hasUserMenu() && filament()->getUserMenuPosition() === UserMenuPosition::Topbar)
                <x-filament-panels::user-menu />
            @endif
        </div>

        {{ FilamentView::renderHook(PanelsRenderHook::TOPBAR_END) }}
    </nav>

    <x-filament-actions::modals />
</div>
