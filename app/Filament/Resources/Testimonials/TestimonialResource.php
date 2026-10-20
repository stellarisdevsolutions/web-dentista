<?php

namespace App\Filament\Resources\Testimonials;

use App\Filament\Resources\Testimonials\Pages\CreateTestimonial;
use App\Filament\Resources\Testimonials\Pages\EditTestimonial;
use App\Filament\Resources\Testimonials\Pages\ListTestimonials;
use App\Filament\Resources\Testimonials\Schemas\TestimonialForm;
use App\Filament\Resources\Testimonials\Tables\TestimonialsTable;
use App\Models\Testimonial;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class TestimonialResource extends Resource
{
    protected static ?string $model = Testimonial::class;

    protected static bool $shouldRegisterNavigation = false;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedChatBubbleBottomCenterText;

    protected static ?int $navigationSort = 2;

    protected static ?string $recordTitleAttribute = 'patient_name';

    public static function getGloballySearchableAttributes(): array
    {
        return ['patient_name', 'treatment', 'comment'];
    }

    public static function getGlobalSearchResultTitle(Model $record): string
    {
        /** @var Testimonial $record */
        return $record->patient_name;
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        /** @var Testimonial $record */
        $isSpanish = app()->getLocale() === 'es';

        return [
            $isSpanish ? 'Tratamiento' : 'Treatment' => $record->treatment,
            $isSpanish ? 'Calificación' : 'Rating' => str_repeat('⭐', (int) $record->rating),
        ];
    }

    public static function getNavigationGroup(): ?string
    {
        return __('Smile Gallery & Reviews');
    }

    public static function getNavigationLabel(): string
    {
        return __('Testimonials');
    }

    public static function getModelLabel(): string
    {
        return __('Testimonial');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Testimonials');
    }

    public static function form(Schema $schema): Schema
    {
        return TestimonialForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TestimonialsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListTestimonials::route('/'),
            'create' => CreateTestimonial::route('/create'),
            'edit' => EditTestimonial::route('/{record}/edit'),
        ];
    }
}
