<x-filament-widgets::widget class="fi-wi-welcome-banner">
    <div class="aura-welcome-banner">
        <!-- Columna Izquierda: Saludo, Subtítulo y Botones -->
        <div class="aura-welcome-left">
            <h1 class="aura-welcome-title">
                {{ $greeting }}, <span class="aura-welcome-name" title="{{ $userName }}">{{ $userName }}</span>
            </h1>

            <p class="aura-welcome-subtitle">
                {{ $subtitle }}
            </p>

            <div class="aura-welcome-actions">
                <a href="{{ $primaryButton['url'] }}" class="aura-welcome-btn-primary">
                    <span>{{ $primaryButton['label'] }}</span>
                </a>

                <a href="{{ $secondaryButton['url'] }}" class="aura-welcome-btn-secondary">
                    <span>{{ $secondaryButton['label'] }}</span>
                </a>
            </div>
        </div>

        <!-- Columna Derecha: Tarjetas de Cifras -->
        <div class="aura-welcome-cards">
            @foreach($cards as $card)
                <div class="aura-welcome-card">
                    <span class="aura-welcome-card-num {{ !empty($card['isHighlight']) ? 'aura-highlight' : '' }}">
                        {{ $card['value'] }}
                    </span>
                    <span class="aura-welcome-card-label">
                        {{ $card['label'] }}
                    </span>
                </div>
            @endforeach
        </div>
    </div>
</x-filament-widgets::widget>
