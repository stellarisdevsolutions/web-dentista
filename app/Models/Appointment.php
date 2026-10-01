<?php

namespace App\Models;

use Database\Factories\AppointmentFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Appointment extends Model
{
    /** @use HasFactory<AppointmentFactory> */
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
            'appointment_date' => 'date',
            'duration_minutes' => 'integer',
            'is_first_visit' => 'boolean',
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

    /**
     * Boot model to automatically generate appointment code.
     */
    protected static function booted(): void
    {
        static::creating(function (Appointment $appointment): void {
            if (empty($appointment->code)) {
                $appointment->code = self::generateUniqueCode();
            }
        });
    }

    /**
     * Generate unique booking reference code (e.g. CIT-2026-A1B2).
     */
    public static function generateUniqueCode(): string
    {
        do {
            $code = 'CIT-'.date('Y').'-'.strtoupper(substr(bin2hex(random_bytes(4)), 0, 5));
        } while (self::where('code', $code)->exists());

        return $code;
    }

    /**
     * WhatsApp URL to contact the patient directly.
     */
    public function getWhatsAppUrlAttribute(): string
    {
        $phone = preg_replace('/[^0-9]/', '', (string) $this->patient_phone);
        $message = "Hello {$this->patient_name}, regarding your appointment ({$this->code}) on {$this->appointment_date?->format('Y-m-d')} at {$this->appointment_time}.";

        return "https://wa.me/{$phone}?text=".urlencode($message);
    }
}
