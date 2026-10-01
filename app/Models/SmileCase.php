<?php

namespace App\Models;

use App\Models\Concerns\HasBilingualAttributes;
use Database\Factories\SmileCaseFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SmileCase extends Model
{
    /** @use HasFactory<SmileCaseFactory> */
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
            'is_featured' => 'boolean',
            'is_active' => 'boolean',
            'order' => 'integer',
        ];
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function dentist(): BelongsTo
    {
        return $this->belongsTo(Dentist::class);
    }

    public function getTitleAttribute(): string
    {
        return $this->getLocalized('title');
    }

    public function getDescriptionAttribute(): string
    {
        return $this->getLocalized('description');
    }

    public function getPatientTestimonialAttribute(): string
    {
        return $this->getLocalized('patient_testimonial');
    }
}
