<?php

namespace App\Models;

use Database\Factories\ServiceFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Service extends Model
{
    /** @use HasFactory<ServiceFactory> */
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

    public function getFormattedPriceAttribute(): string
    {
        return $this->price ? '$'.number_format((float) $this->price, 2) : 'A consultar';
    }

    protected static function booted(): void
    {
        static::saving(function (Service $service) {
            if (blank($service->slug)) {
                $service->slug = static::generateUniqueSlug($service->name ?? 'tratamiento', $service->id);
            } elseif ($service->isDirty('slug')) {
                $service->slug = static::generateUniqueSlug($service->slug, $service->id);
            }
        });
    }

    /**
     * Generate a clean, URL-safe, accent-free, and collision-free unique slug.
     * E.g. "Blanqueamiento Láser" -> "blanqueamiento-laser", and if collision -> "blanqueamiento-laser-1".
     */
    public static function generateUniqueSlug(string $name, ?int $ignoreId = null): string
    {
        $baseSlug = Str::slug($name);
        if (empty($baseSlug)) {
            $baseSlug = 'tratamiento';
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
