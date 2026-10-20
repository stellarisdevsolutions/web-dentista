<?php

namespace App\Models;

use Database\Factories\PostFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Post extends Model
{
    /** @use HasFactory<PostFactory> */
    use HasFactory;

    protected $guarded = ['id'];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'published_at' => 'datetime',
            'is_published' => 'boolean',
        ];
    }

    public function getCoverImageUrlAttribute(): string
    {
        if (! empty($this->cover_image)) {
            return str_starts_with($this->cover_image, 'http://') || str_starts_with($this->cover_image, 'https://')
                ? $this->cover_image
                : asset('storage/'.$this->cover_image);
        }

        return 'https://images.unsplash.com/photo-1588776814546-1ffcf47267a5?auto=format&fit=crop&w=800&q=80';
    }

    public function getReadingTimeAttribute(): int
    {
        $words = str_word_count(strip_tags((string) ($this->content ?? $this->excerpt ?? '')));

        return max(1, (int) ceil($words / 200));
    }

    protected static function booted(): void
    {
        static::saving(function (Post $post) {
            if (blank($post->slug)) {
                $post->slug = static::generateUniqueSlug($post->title ?? 'articulo', $post->id);
            } elseif ($post->isDirty('slug')) {
                $post->slug = static::generateUniqueSlug($post->slug, $post->id);
            }
        });
    }

    /**
     * Generate a clean, URL-safe, accent-free, and collision-free unique slug.
     * E.g. "Cuidado Bucal Infantil" -> "cuidado-bucal-infantil", and if collision -> "cuidado-bucal-infantil-1".
     */
    public static function generateUniqueSlug(string $title, ?int $ignoreId = null): string
    {
        $baseSlug = Str::slug($title);
        if (empty($baseSlug)) {
            $baseSlug = 'articulo';
        }

        $slug = $baseSlug;
        $counter = 1;

        while (static::where('slug', $slug)
            ->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))
            ->exists()) {
            $slug = "{$baseSlug}-{$counter}";
            $counter++;
        }

        return $slug;
    }
}
