<?php

namespace App\Models;

use Database\Factories\ClinicSettingFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClinicSetting extends Model
{
    /** @use HasFactory<ClinicSettingFactory> */
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
            'show_language_switcher' => 'boolean',
            'social_links' => 'array',
        ];
    }

    public function getLogoAssetUrlAttribute(): ?string
    {
        if (empty($this->logo_url)) {
            return null;
        }

        if (str_starts_with($this->logo_url, 'http://') || str_starts_with($this->logo_url, 'https://')) {
            return $this->logo_url;
        }

        return asset('storage/'.ltrim($this->logo_url, '/'));
    }

    public function getFaviconAssetUrlAttribute(): ?string
    {
        if (empty($this->favicon_url)) {
            return null;
        }

        if (str_starts_with($this->favicon_url, 'http://') || str_starts_with($this->favicon_url, 'https://')) {
            return $this->favicon_url;
        }

        return asset('storage/'.ltrim($this->favicon_url, '/'));
    }
}
