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
            Section::make(__('Brand Identity & Logos'))
                ->description(__('Commercial name, tagline, official logo, and browser favicon.'))
                ->icon('heroicon-o-building-office-2')
                ->schema([
                    Grid::make(['default' => 1, 'sm' => 2])
                        ->schema([
                            TextInput::make('clinic_name')
                                ->label(__('Clinic Name'))
                                ->placeholder('Aura Dental Studio')
                                ->prefixIcon('heroicon-o-sparkles')
                                ->required()
                                ->maxLength(255)
                                ->helperText(__('Main name visible on header, footer, and metadata.')),

                            TextInput::make('tagline')
                                ->label(__('Tagline / Institutional Slogan'))
                                ->placeholder(__('e.g. Modern, comprehensive, and pain-free dentistry...'))
                                ->prefixIcon('heroicon-o-chat-bubble-bottom-center-text')
                                ->maxLength(255)
                                ->helperText(__('Representative tagline displayed on the homepage and footer.')),
                        ]),

                    Grid::make(['default' => 1, 'sm' => 2])
                        ->schema([
                            FileUpload::make('logo_url')
                                ->label(__('Official Clinic Logo'))
                                ->image()
                                ->directory('clinic')
                                ->disk('public')
                                ->maxSize(2048)
                                ->imagePreviewHeight('120')
                                ->nullable()
                                ->helperText(__('Logo in transparent PNG, SVG, or WebP. Displayed on website header and footer.')),

                            FileUpload::make('favicon_url')
                                ->label(__('Browser Tab Favicon'))
                                ->image()
                                ->directory('clinic')
                                ->disk('public')
                                ->maxSize(1024)
                                ->imagePreviewHeight('120')
                                ->nullable()
                                ->helperText(__('Square icon (PNG, ICO, or SVG, 32×32 or 64×64 px) appearing in patient browser tabs.')),
                        ]),
                ]),

            // ==========================================
            // SECCIÓN 2: CANALES DE CONTACTO Y URGENCIAS
            // ==========================================
            Section::make(__('Communication & Contact Channels'))
                ->description(__('Phone lines and official email for patient care and emergencies.'))
                ->icon('heroicon-o-phone')
                ->schema([
                    Grid::make(['default' => 1, 'sm' => 2])
                        ->schema([
                            TextInput::make('phone')
                                ->label(__('Main Phone'))
                                ->tel()
                                ->telRegex('/^[+0-9\s()\-.]+$/')
                                ->validationMessages([
                                    'regex' => __('Invalid phone format (numbers, spaces, +, -, () only).'),
                                ])
                                ->prefixIcon('heroicon-o-phone')
                                ->placeholder('+1 (555) 234-5678')
                                ->required()
                                ->helperText(__('Main telephone line for patient care and appointments.')),

                            TextInput::make('email')
                                ->label(__('Contact Email'))
                                ->email()
                                ->prefixIcon('heroicon-o-envelope')
                                ->placeholder('contacto@auradental.com')
                                ->required()
                                ->helperText(__('Mailbox for appointment confirmations and general inquiries.')),

                            TextInput::make('emergency_phone')
                                ->label(__('Emergency Phone (Optional)'))
                                ->tel()
                                ->telRegex('/^[+0-9\s()\-.]+$/')
                                ->validationMessages([
                                    'regex' => __('Invalid phone format (numbers, spaces, +, -, () only).'),
                                ])
                                ->prefixIcon('heroicon-o-shield-exclamation')
                                ->placeholder('+1 (555) 911-7775')
                                ->nullable()
                                ->helperText(__('Priority hotline for acute pain or after-hours emergencies.'))
                                ->columnSpanFull(),
                        ]),
                ]),

            // ==========================================
            // SECCIÓN 3: SEDE FÍSICA Y HORARIOS
            // ==========================================
            Section::make(__('Location & Operating Hours'))
                ->description(__('Physical premises address, map link, and schedule availability.'))
                ->icon('heroicon-o-map-pin')
                ->schema([
                    TextInput::make('address')
                        ->label(__('Physical Address'))
                        ->prefixIcon('heroicon-o-map-pin')
                        ->placeholder('742 Evergreen Terrace, Suite 100, Beverly Hills, CA 90210')
                        ->required()
                        ->maxLength(255),

                    TextInput::make('google_maps_url')
                        ->label(__('Google Maps Link (URL)'))
                        ->prefixIcon('heroicon-o-map')
                        ->placeholder('https://maps.google.com/?q=...')
                        ->url()
                        ->nullable()
                        ->helperText(__('Allows patients to open GPS navigation directions directly on mobile.')),

                    Textarea::make('opening_hours')
                        ->label(__('Opening Hours'))
                        ->rows(3)
                        ->placeholder(__("Monday to Friday: 8:00 AM - 7:00 PM\nSaturday: 9:00 AM - 3:00 PM\nSunday: Emergencies Only"))
                        ->helperText(__('Write one schedule per line for clear display on the website.'))
                        ->required(),
                ]),

            // ==========================================
            // SECCIÓN 4: PRESENCIA WEB Y REDES SOCIALES
            // ==========================================
            Section::make(__('Digital Presence & Social Media'))
                ->description(__('Official links to the clinic social media profiles.'))
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
