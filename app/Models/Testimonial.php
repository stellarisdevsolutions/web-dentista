<?php

namespace App\Models;

use App\Models\Concerns\HasBilingualAttributes;
use Database\Factories\TestimonialFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Testimonial extends Model
{
    /** @use HasFactory<TestimonialFactory> */
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
            'rating' => 'integer',
            'is_verified' => 'boolean',
            'is_active' => 'boolean',
            'order' => 'integer',
        ];
    }

    public function getTreatmentAttribute(): string
    {
        return $this->getLocalized('treatment');
    }

    public function getCommentAttribute(): string
    {
        return $this->getLocalized('comment');
    }
}
