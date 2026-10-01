<?php

namespace App\Filament\Resources\Posts;

use App\Filament\Resources\Posts\Pages\CreatePost;
use App\Filament\Resources\Posts\Pages\EditPost;
use App\Filament\Resources\Posts\Pages\ListPosts;
use App\Filament\Resources\Posts\Schemas\PostForm;
use App\Filament\Resources\Posts\Tables\PostsTable;
use App\Models\Post;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class PostResource extends Resource
{
    protected static ?string $model = Post::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentText;

    protected static ?int $navigationSort = 2;

    public static function getRecordTitleAttribute(): ?string
    {
        return app()->getLocale() === 'es' ? 'title_es' : 'title_en';
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['title_es', 'title_en', 'excerpt_es', 'excerpt_en', 'category_es', 'category_en', 'slug'];
    }

    public static function getGlobalSearchResultTitle(Model $record): string
    {
        /** @var Post $record */
        return $record->title;
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        /** @var Post $record */
        $isSpanish = app()->getLocale() === 'es';

        return [
            $isSpanish ? 'Categoría' : 'Category' => $record->category,
        ];
    }

    public static function getNavigationGroup(): ?string
    {
        return __('Web Content & CMS');
    }

    public static function getNavigationLabel(): string
    {
        return __('Oral Health Blog');
    }

    public static function getModelLabel(): string
    {
        return __('Article');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Articles');
    }

    public static function form(Schema $schema): Schema
    {
        return PostForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PostsTable::configure($table);
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
            'index' => ListPosts::route('/'),
            'create' => CreatePost::route('/create'),
            'edit' => EditPost::route('/{record}/edit'),
        ];
    }
}
