<?php

namespace App\Models;

use App\Models\Concerns\HasBilingualAttributes;
use Database\Factories\PostFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    /** @use HasFactory<PostFactory> */
    use HasBilingualAttributes, HasFactory;

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

    public function getTitleAttribute(): string
    {
        return $this->getLocalized('title');
    }

    public function getCategoryAttribute(): string
    {
        return $this->getLocalized('category');
    }

    public function getExcerptAttribute(): string
    {
        return $this->getLocalized('excerpt');
    }

    public function getContentAttribute(): string
    {
        return $this->getLocalized('content');
    }
}
