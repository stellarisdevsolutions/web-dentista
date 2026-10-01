<?php

namespace App\Models;

use App\Models\Concerns\HasBilingualAttributes;
use Database\Factories\ServiceFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Service extends Model
{
    /** @use HasFactory<ServiceFactory> */
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
            'price' => 'decimal:2',
            'duration_minutes' => 'integer',
            'allows_online_booking' => 'boolean',
            'is_featured' => 'boolean',
            'is_active' => 'boolean',
            'order' => 'integer',
        ];
    }

    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class);
    }

    public function smileCases(): HasMany
    {
        return $this->hasMany(SmileCase::class);
    }

    public function dentists(): BelongsToMany
    {
        return $this->belongsToMany(Dentist::class)->withTimestamps();
    }

    public function getNameAttribute(): string
    {
        return $this->getLocalized('name');
    }

    public function getCategoryAttribute(): string
    {
        return $this->getLocalized('category');
    }

    public function getShortDescriptionAttribute(): string
    {
        return $this->getLocalized('short_description');
    }

    public function getDescriptionAttribute(): string
    {
        return $this->getLocalized('description');
    }

    public function getImageUrlAttribute(): string
    {
        if (! empty($this->image)) {
            return str_starts_with($this->image, 'http://') || str_starts_with($this->image, 'https://')
                ? $this->image
                : asset('storage/'.$this->image);
        }

        $fallbacks = [
            'comprehensive-exam-cleaning' => 'https://images.unsplash.com/photo-1588776814546-1ffcf47267a5?auto=format&fit=crop&w=800&q=80',
            'laser-teeth-whitening' => 'https://images.unsplash.com/photo-1606811841689-23dfddce3e95?auto=format&fit=crop&w=800&q=80',
            'clear-aligners-orthodontics' => 'https://images.unsplash.com/photo-1598256989800-fe5f95da9787?auto=format&fit=crop&w=800&q=80',
            'dental-implants' => 'https://images.unsplash.com/photo-1629909613654-28e377c37b09?auto=format&fit=crop&w=800&q=80',
            'porcelain-veneers' => 'https://images.unsplash.com/photo-1579684385127-1ef15d508118?auto=format&fit=crop&w=800&q=80',
            'emergency-dental-care' => 'https://images.unsplash.com/photo-1559839734-2b71ea197ec2?auto=format&fit=crop&w=800&q=80',
        ];

        return $fallbacks[$this->slug] ?? 'https://images.unsplash.com/photo-1588776814546-1ffcf47267a5?auto=format&fit=crop&w=800&q=80';
    }
}
