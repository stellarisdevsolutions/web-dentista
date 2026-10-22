<?php

namespace App\Filament\Resources\Posts\Pages;

use App\Filament\Resources\Posts\PostResource;
use App\Models\Post;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Livewire\Attributes\Renderless;

class ListPosts extends ListRecords
{
    protected static string $resource = PostResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('+ '.__('New Article'))
                ->extraAttributes([
                    'style' => 'border-radius: 9999px !important; background-color: #0EA5E9 !important; color: #0F172A !important; font-weight: 800 !important; min-height: 48px !important; padding: 0 24px !important; box-shadow: 0 10px 24px rgba(14, 165, 233, 0.40) !important; border: none !important; font-size: 14px !important;',
                ]),
        ];
    }

    /**
     * Fetch article details for the instant slide-over drawer.
     *
     * @return array<string, mixed>|null
     */
    #[Renderless]
    public function getPostDetails(int $id): ?array
    {
        try {
            /** @var Post|null $post */
            $post = Post::find($id);

            if (! $post) {
                return null;
            }

            $appUrl = config('app.url', 'http://localhost:8000');
            $host = preg_replace('#^https?://#', '', rtrim((string) $appUrl, '/'));
            $displayUrl = $host.'/blog/'.$post->slug;

            return [
                'id' => $post->id,
                'title' => $post->title,
                'slug' => $post->slug,
                'category' => $post->category ?? __('Uncategorized'),
                'excerpt' => $post->excerpt,
                'content' => $post->content,
                'cover_image_url' => $post->cover_image_url,
                'is_published' => (bool) $post->is_published,
                'published_at' => $post->published_at?->format('d M, Y H:i'),
                'published_at_human' => $post->published_at?->locale(app()->getLocale())->diffForHumans(),
                'reading_time' => $post->reading_time,
                'public_url' => url('/blog/'.$post->slug),
                'display_url' => $displayUrl,
                'edit_url' => PostResource::getUrl('edit', ['record' => $post->id]),
            ];
        } catch (\Throwable $e) {
            report($e);

            return null;
        }
    }
}
