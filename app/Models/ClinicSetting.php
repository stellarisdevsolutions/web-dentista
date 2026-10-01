<?php

namespace App\Models;

use App\Models\Concerns\HasBilingualAttributes;
use Database\Factories\ClinicSettingFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClinicSetting extends Model
{
    /** @use HasFactory<ClinicSettingFactory> */
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
            'show_language_switcher' => 'boolean',
            'social_links' => 'array',
        ];
    }

    public function getTaglineAttribute(): string
    {
        return $this->getLocalized('tagline');
    }

    public function getOpeningHoursAttribute(): string
    {
        return $this->getLocalized('opening_hours');
    }
}
