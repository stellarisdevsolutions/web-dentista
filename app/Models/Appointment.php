<?php

namespace App\Models;

use App\Enums\AppointmentStatus;
use Carbon\Carbon;
use Database\Factories\AppointmentFactory;
use Illuminate\Database\Eloquent\Builder;
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
            'status' => AppointmentStatus::class,
        ];
    }

    /**
     * Scope query to appointments for a specific date.
     */
    public function scopeForDate(Builder $query, Carbon|string $date): void
    {
        $dateString = $date instanceof Carbon ? $date->toDateString() : $date;
        $query->whereDate('appointment_date', $dateString);
    }

    /**
     * Scope query to active appointments (excluding cancelled and no-show).
     */
    public function scopeActive(Builder $query): void
    {
        $query->whereNotIn('status', [AppointmentStatus::Cancelled, AppointmentStatus::NoShow]);
    }

    /**
     * Scope query to appointments belonging to a specific dentist.
     */
    public function scopeForDentist(Builder $query, int $dentistId): void
    {
        $query->where('dentist_id', $dentistId);
    }

    /**
     * Check if a time slot has collisions with another active appointment for a given dentist.
     */
    public static function hasConflict(int $dentistId, string|Carbon $date, string $time, ?int $ignoreAppointmentId = null): bool
    {
        $dateString = $date instanceof Carbon ? $date->toDateString() : $date;
        $timeString = trim($time);

        return self::query()
            ->where('dentist_id', $dentistId)
            ->when($ignoreAppointmentId, fn (Builder $q) => $q->where('id', '!=', $ignoreAppointmentId))
            ->whereDate('appointment_date', $dateString)
            ->where('appointment_time', $timeString)
            ->active()
            ->exists();
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
     * WhatsApp URL to contact the patient directly in Spanish.
     */
    public function getWhatsAppUrlAttribute(): string
    {
        $phone = preg_replace('/[^0-9]/', '', (string) $this->patient_phone);
        $dateFormatted = $this->appointment_date ? $this->appointment_date->format('d/m/Y') : '';
        $serviceName = $this->service?->name ?? 'Consulta Odontológica';
        $clinicName = config('app.name', 'Clínica Dental Aura');

        $message = "Hola {$this->patient_name}, le saludamos de {$clinicName}. Le escribimos para coordinar su cita de {$serviceName} (Ref: {$this->code}) programada para el {$dateFormatted} a las {$this->appointment_time}. ¿Nos confirma su asistencia?";

        return "https://wa.me/{$phone}?text=".urlencode($message);
    }
}
