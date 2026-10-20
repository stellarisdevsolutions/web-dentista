<?php

namespace App\Filament\Resources\ClinicSettings\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ClinicSettingForm
{
    /**
     * Devuelve el esquema estructurado para el formulario o modal de configuración.
     *
     * @return array<int, Component>
     */
    public static function schema(): array
    {
        return [
            // ==========================================
            // SECCIÓN 1: IDENTIDAD, MARCA Y LOGOTIPOS
            // ==========================================
            Section::make('Identidad, Marca y Logotipos')
                ->description('Nombre comercial, eslogan, logotipo oficial y favicon para las pestañas del navegador.')
                ->icon('heroicon-o-building-office-2')
                ->schema([
                    Grid::make(['default' => 1, 'sm' => 2])
                        ->schema([
                            TextInput::make('clinic_name')
                                ->label('Nombre de la Clínica')
                                ->placeholder('ej. Aura Dental Studio')
                                ->prefixIcon('heroicon-o-sparkles')
                                ->required()
                                ->maxLength(255)
                                ->helperText('Nombre principal visible en la cabecera, pie de página y metadatos.'),

                            TextInput::make('tagline')
                                ->label('Lema / Eslogan Institucional')
                                ->placeholder('ej. Odontología moderna, integral y sin dolor...')
                                ->prefixIcon('heroicon-o-chat-bubble-bottom-center-text')
                                ->maxLength(255)
                                ->helperText('Frase representativa mostrada en la página de inicio y el pie de página.'),
                        ]),

                    Grid::make(['default' => 1, 'sm' => 2])
                        ->schema([
                            FileUpload::make('logo_url')
                                ->label('Logo Oficial de la Clínica')
                                ->image()
                                ->directory('clinic')
                                ->disk('public')
                                ->maxSize(2048)
                                ->imagePreviewHeight('120')
                                ->nullable()
                                ->helperText('Logotipo en formato PNG transparente, SVG o WebP. Se muestra en la cabecera y pie de página de la web.'),

                            FileUpload::make('favicon_url')
                                ->label('Favicon para Pestaña del Navegador')
                                ->image()
                                ->directory('clinic')
                                ->disk('public')
                                ->maxSize(1024)
                                ->imagePreviewHeight('120')
                                ->nullable()
                                ->helperText('Icono cuadrado (PNG, ICO o SVG, 32×32 o 64×64 px) que aparece en la pestaña del navegador de los pacientes.'),
                        ]),
                ]),

            // ==========================================
            // SECCIÓN 2: CANALES DE CONTACTO Y URGENCIAS
            // ==========================================
            Section::make('Canales de Comunicación y Contacto')
                ->description('Líneas telefónicas y correo electrónico oficial para atención de pacientes y urgencias.')
                ->icon('heroicon-o-phone')
                ->schema([
                    Grid::make(['default' => 1, 'sm' => 2])
                        ->schema([
                            TextInput::make('phone')
                                ->label('Teléfono Principal')
                                ->tel()
                                ->telRegex('/^[+0-9\s()\-.]+$/')
                                ->validationMessages([
                                    'regex' => 'El formato del teléfono no es válido (solo números, espacios, +, -, ()).',
                                ])
                                ->prefixIcon('heroicon-o-phone')
                                ->placeholder('+1 (555) 234-5678')
                                ->required()
                                ->helperText('Línea telefónica principal de atención al paciente y reservas.'),

                            TextInput::make('email')
                                ->label('Correo Electrónico de Contacto')
                                ->email()
                                ->prefixIcon('heroicon-o-envelope')
                                ->placeholder('contacto@auradental.com')
                                ->required()
                                ->helperText('Buzón para confirmaciones de citas y consultas generales.'),

                            TextInput::make('emergency_phone')
                                ->label('Teléfono de Urgencias (Opcional)')
                                ->tel()
                                ->telRegex('/^[+0-9\s()\-.]+$/')
                                ->validationMessages([
                                    'regex' => 'El formato del teléfono no es válido (solo números, espacios, +, -, ()).',
                                ])
                                ->prefixIcon('heroicon-o-shield-exclamation')
                                ->placeholder('+1 (555) 911-7775')
                                ->nullable()
                                ->helperText('Línea prioritaria destacada para dolores agudos o urgencias fuera de hora.')
                                ->columnSpanFull(),
                        ]),
                ]),

            // ==========================================
            // SECCIÓN 3: SEDE FÍSICA Y HORARIOS
            // ==========================================
            Section::make('Ubicación y Horarios de Atención')
                ->description('Dirección de las instalaciones físicas, enlace cartográfico y disponibilidad horaria.')
                ->icon('heroicon-o-map-pin')
                ->schema([
                    TextInput::make('address')
                        ->label('Dirección Física')
                        ->prefixIcon('heroicon-o-map-pin')
                        ->placeholder('ej. 742 Evergreen Terrace, Suite 100, Beverly Hills, CA 90210')
                        ->required()
                        ->maxLength(255),

                    TextInput::make('google_maps_url')
                        ->label('Enlace de Google Maps (URL)')
                        ->prefixIcon('heroicon-o-map')
                        ->placeholder('https://maps.google.com/?q=...')
                        ->url()
                        ->nullable()
                        ->helperText('Permite a los pacientes abrir la ruta de navegación GPS directamente en su móvil.'),

                    Textarea::make('opening_hours')
                        ->label('Horario de Atención al Público')
                        ->rows(3)
                        ->placeholder("Lunes a Viernes: 8:00 AM - 7:00 PM\nSábado: 9:00 AM - 3:00 PM\nDomingo: Solo Urgencias")
                        ->helperText('Escribe un horario por línea para que se organice con claridad en la presentación web.')
                        ->required(),
                ]),

            // ==========================================
            // SECCIÓN 4: PRESENCIA WEB Y REDES SOCIALES
            // ==========================================
            Section::make('Presencia Digital y Redes Sociales')
                ->description('Enlaces oficiales a los perfiles de redes sociales de la clínica.')
                ->icon('heroicon-o-globe-alt')
                ->schema([
                    Grid::make(['default' => 1, 'sm' => 3])
                        ->schema([
                            TextInput::make('social_links.instagram')
                                ->label('Instagram')
                                ->prefixIcon('heroicon-o-camera')
                                ->placeholder('https://instagram.com/auradental')
                                ->url()
                                ->nullable(),

                            TextInput::make('social_links.facebook')
                                ->label('Facebook')
                                ->prefixIcon('heroicon-o-user-group')
                                ->placeholder('https://facebook.com/auradental')
                                ->url()
                                ->nullable(),

                            TextInput::make('social_links.tiktok')
                                ->label('TikTok')
                                ->prefixIcon('heroicon-o-video-camera')
                                ->placeholder('https://tiktok.com/@auradental')
                                ->url()
                                ->nullable(),
                        ]),
                ]),
        ];
    }

    public static function configure(Schema $schema): Schema
    {
        return $schema->components(static::schema());
    }
}
