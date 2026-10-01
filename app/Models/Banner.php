<?php

namespace App\Models;

use App\Models\Concerns\HasBilingualAttributes;
use Database\Factories\BannerFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Banner extends Model
{
    /** @use HasFactory<BannerFactory> */
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
            'is_active' => 'boolean',
            'order' => 'integer',
        ];
    }

    public function getBadgeAttribute(): string
    {
        return $this->getLocalized('badge');
    }

    public function getTitleAttribute(): string
    {
        return $this->getLocalized('title');
    }

    public function getSubtitleAttribute(): string
    {
        return $this->getLocalized('subtitle');
    }

    public function getButtonTextAttribute(): string
    {
        return $this->getLocalized('button_text');
    }
}
