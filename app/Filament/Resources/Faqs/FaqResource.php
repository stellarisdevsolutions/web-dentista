<?php

namespace App\Filament\Resources\Faqs;

use App\Filament\Resources\Faqs\Pages\CreateFaq;
use App\Filament\Resources\Faqs\Pages\EditFaq;
use App\Filament\Resources\Faqs\Pages\ListFaqs;
use App\Filament\Resources\Faqs\Schemas\FaqForm;
use App\Filament\Resources\Faqs\Tables\FaqsTable;
use App\Models\Faq;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class FaqResource extends Resource
{
    protected static ?string $model = Faq::class;

    protected static bool $shouldRegisterNavigation = false;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedQuestionMarkCircle;

    protected static ?int $navigationSort = 3;

    public static function getRecordTitleAttribute(): ?string
    {
        return 'question';
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['question', 'answer', 'category'];
    }

    public static function getGlobalSearchResultTitle(Model $record): string
    {
        /** @var Faq $record */
        return $record->question;
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        /** @var Faq $record */
        return [
            __('Category') => $record->category,
        ];
    }

    public static function getNavigationGroup(): ?string
    {
        return __('Web Content & CMS');
    }

    public static function getNavigationLabel(): string
    {
        return __('FAQs');
    }

    public static function getModelLabel(): string
    {
        return __('FAQ');
    }

    public static function getPluralModelLabel(): string
    {
        return __('FAQs');
    }

    public static function form(Schema $schema): Schema
    {
        return FaqForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return FaqsTable::configure($table);
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
            'index' => ListFaqs::route('/'),
            'create' => CreateFaq::route('/create'),
            'edit' => EditFaq::route('/{record}/edit'),
        ];
    }
}
