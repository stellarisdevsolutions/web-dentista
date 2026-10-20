<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SiteSection extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'page',
        'section_key',
        'name',
        'icon',
        'order',
        'is_active',
        'content',
        'settings',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'order' => 'integer',
            'content' => 'array',
            'settings' => 'array',
        ];
    }

    /**
     * Scope a query to only include active sections.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to retrieve ordered sections for a specific page.
     */
    public function scopeForPage(Builder $query, string $page): Builder
    {
        return $query->where('page', $page)->orderBy('order');
    }

    /**
     * Convenient helper to get a content field with fallback.
     */
    public function field(string $key, mixed $default = null): mixed
    {
        $content = $this->content ?? [];

        return $content[$key] ?? $default;
    }
}
