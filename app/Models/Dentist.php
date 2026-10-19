<?php

namespace App\Models;

use Carbon\Carbon;
use Database\Factories\DentistFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Dentist extends Model
{
    /** @use HasFactory<DentistFactory> */
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
            'working_days' => 'array',
            'weekly_schedule' => 'array',
            'is_active' => 'boolean',
            'order' => 'integer',
        ];
    }

    /**
     * Check if dentist works on a given day of the week (e.g. 'monday', 'wednesday').
     */
    public function isAvailableOn(string $dayName): bool
    {
        $dayName = strtolower($dayName);

        if (! empty($this->weekly_schedule) && isset($this->weekly_schedule[$dayName])) {
            return (bool) ($this->weekly_schedule[$dayName]['is_working'] ?? false);
        }

        return in_array($dayName, $this->working_days ?? ['monday', 'tuesday', 'wednesday', 'thursday', 'friday'], true);
    }

    /**
     * Generate available time slots for a given date, supporting Morning and Afternoon shifts
     * and automatically excluding lunch breaks and booked appointments.
     *
     * @return array<int, array{time: string, shift: string, available: bool}>
     */
    public function getSlotsForDate(string $date, int $durationMinutes = 45, ?int $excludeAppointmentId = null): array
    {
        $carbonDate = Carbon::parse($date);
        $date = $carbonDate->toDateString();
        $dayName = strtolower($carbonDate->format('l'));

        if (! $this->isAvailableOn($dayName)) {
            return [];
        }

        // Get already booked appointments for this dentist on this date with their time boundaries
        $existingAppointments = $this->appointments()
            ->whereDate('appointment_date', $date)
            ->whereNotIn('status', ['cancelled', 'no_show'])
            ->when($excludeAppointmentId, fn ($q) => $q->where('id', '!=', $excludeAppointmentId))
            ->get()
            ->map(function (Appointment $app) use ($date) {
                $start = Carbon::createFromFormat('Y-m-d H:i', "{$date} ".substr((string) $app->appointment_time, 0, 5));
                $dur = (int) ($app->duration_minutes ?: 45);

                return [
                    'start' => $start,
                    'end' => $start->copy()->addMinutes($dur),
                ];
            })
            ->all();

        $slots = [];

        // Check if day has detailed shift schedule
        if (! empty($this->weekly_schedule) && isset($this->weekly_schedule[$dayName])) {
            $dayConfig = $this->weekly_schedule[$dayName];

            // 1. Morning Shift (e.g. 09:00 to 13:00)
            if (! empty($dayConfig['morning_start']) && ! empty($dayConfig['morning_end'])) {
                $this->generateShiftSlots(
                    $slots,
                    $dayConfig['morning_start'],
                    $dayConfig['morning_end'],
                    'morning',
                    $durationMinutes,
                    $existingAppointments,
                    $date
                );
            }

            // 2. Afternoon Shift (e.g. 14:00 to 18:00)
            // Note: The gap between morning_end and afternoon_start is the natural lunch break!
            if (! empty($dayConfig['afternoon_start']) && ! empty($dayConfig['afternoon_end'])) {
                $this->generateShiftSlots(
                    $slots,
                    $dayConfig['afternoon_start'],
                    $dayConfig['afternoon_end'],
                    'afternoon',
                    $durationMinutes,
                    $existingAppointments,
                    $date
                );
            }
        } else {
            // Traditional fallback: standard hours with default 13:00 - 14:00 lunch break
            $start = substr((string) ($this->working_hours_start ?? '09:00:00'), 0, 5);
            $end = substr((string) ($this->working_hours_end ?? '18:00:00'), 0, 5);

            $this->generateShiftSlots($slots, $start, '13:00', 'morning', $durationMinutes, $existingAppointments, $date);
            $this->generateShiftSlots($slots, '14:00', $end, 'afternoon', $durationMinutes, $existingAppointments, $date);
        }

        return $slots;
    }

    /**
     * Check if this dentist has sufficient consecutive free time for a treatment duration
     * at a specific date and time, without overflowing shift boundaries or overlapping appointments.
     */
    public function isTimeSlotAvailable(string $date, string $time, int $durationMinutes = 45, ?int $excludeAppointmentId = null): bool
    {
        if (! $this->is_active) {
            return false;
        }

        $carbonDate = Carbon::parse($date);
        $date = $carbonDate->toDateString();
        $dayName = strtolower($carbonDate->format('l'));

        if (! $this->isAvailableOn($dayName)) {
            return false;
        }

        $timeClean = substr($time, 0, 5);
        $slotStart = Carbon::createFromFormat('Y-m-d H:i', "{$date} {$timeClean}");
        $slotEnd = $slotStart->copy()->addMinutes($durationMinutes);

        // Determine shifts for this day
        $shifts = [];
        if (! empty($this->weekly_schedule) && isset($this->weekly_schedule[$dayName])) {
            $config = $this->weekly_schedule[$dayName];
            if (! empty($config['morning_start']) && ! empty($config['morning_end'])) {
                $shifts[] = [
                    'start' => Carbon::createFromFormat('Y-m-d H:i', "{$date} ".substr($config['morning_start'], 0, 5)),
                    'end' => Carbon::createFromFormat('Y-m-d H:i', "{$date} ".substr($config['morning_end'], 0, 5)),
                ];
            }
            if (! empty($config['afternoon_start']) && ! empty($config['afternoon_end'])) {
                $shifts[] = [
                    'start' => Carbon::createFromFormat('Y-m-d H:i', "{$date} ".substr($config['afternoon_start'], 0, 5)),
                    'end' => Carbon::createFromFormat('Y-m-d H:i', "{$date} ".substr($config['afternoon_end'], 0, 5)),
                ];
            }
        } else {
            $startStr = substr((string) ($this->working_hours_start ?? '09:00:00'), 0, 5);
            $endStr = substr((string) ($this->working_hours_end ?? '18:00:00'), 0, 5);
            $shifts[] = [
                'start' => Carbon::createFromFormat('Y-m-d H:i', "{$date} {$startStr}"),
                'end' => Carbon::createFromFormat('Y-m-d H:i', "{$date} 13:00"),
            ];
            $shifts[] = [
                'start' => Carbon::createFromFormat('Y-m-d H:i', "{$date} 14:00"),
                'end' => Carbon::createFromFormat('Y-m-d H:i', "{$date} {$endStr}"),
            ];
        }

        // Must fit entirely inside at least one shift (never straddle across lunch or past closing)
        $fitsInsideShift = false;
        foreach ($shifts as $shift) {
            if ($slotStart->gte($shift['start']) && $slotEnd->lte($shift['end'])) {
                $fitsInsideShift = true;
                break;
            }
        }

        if (! $fitsInsideShift) {
            return false;
        }

        // Check collision with existing active appointments
        $hasOverlap = $this->appointments()
            ->whereDate('appointment_date', $date)
            ->whereNotIn('status', ['cancelled', 'no_show'])
            ->when($excludeAppointmentId, fn ($q) => $q->where('id', '!=', $excludeAppointmentId))
            ->get()
            ->contains(function (Appointment $app) use ($date, $slotStart, $slotEnd) {
                $appStart = Carbon::createFromFormat('Y-m-d H:i', "{$date} ".substr((string) $app->appointment_time, 0, 5));
                $appDur = (int) ($app->duration_minutes ?: 45);
                $appEnd = $appStart->copy()->addMinutes($appDur);

                // Overlap condition: slotStart < appEnd && slotEnd > appStart
                return $slotStart->lt($appEnd) && $slotEnd->gt($appStart);
            });

        return ! $hasOverlap;
    }

    /**
     * Helper to generate time slot intervals for a single shift.
     *
     * @param  array<int, array{time: string, shift: string, available: bool, reason?: string|null}>  $slots
     * @param  array<int, array{start: Carbon, end: Carbon}>  $existingAppointments
     */
    private function generateShiftSlots(
        array &$slots,
        string $startTime,
        string $endTime,
        string $shift,
        int $durationMinutes,
        array $existingAppointments,
        string $date
    ): void {
        $current = Carbon::createFromFormat('Y-m-d H:i', "{$date} ".substr($startTime, 0, 5));
        $shiftEnd = Carbon::createFromFormat('Y-m-d H:i', "{$date} ".substr($endTime, 0, 5));
        $stepMinutes = 30;

        // Advance in 30-minute intervals as long as the treatment duration fits before the end of the shift
        while ($current->copy()->addMinutes($durationMinutes)->lte($shiftEnd)) {
            $timeStr = $current->format('H:i');
            $slotStart = $current->copy();
            $slotEnd = $current->copy()->addMinutes($durationMinutes);

            // Check collision with existing appointments across the full duration
            $isColliding = false;
            $reason = null;
            foreach ($existingAppointments as $app) {
                if ($slotStart->lt($app['end']) && $slotEnd->gt($app['start'])) {
                    $isColliding = true;
                    $is12h = config('app.time_format', '12h') === '12h';
                    $fmt = $is12h ? 'h:i A' : 'H:i';
                    if ($slotStart->gte($app['start']) && $slotStart->lt($app['end'])) {
                        $reason = "Cita ya agendada ({$app['start']->format($fmt)} a {$app['end']->format($fmt)})";
                    } else {
                        $reason = "No disponible: con {$durationMinutes} min terminaría a las {$slotEnd->format($fmt)}, chocando con la cita de las {$app['start']->format($fmt)}";
                    }
                    break;
                }
            }

            $slots[] = [
                'time' => $timeStr,
                'shift' => $shift,
                'available' => ! $isColliding,
                'reason' => $reason,
            ];

            $current->addMinutes($stepMinutes);
        }
    }

    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class);
    }

    public function smileCases(): HasMany
    {
        return $this->hasMany(SmileCase::class);
    }

    public function services(): BelongsToMany
    {
        return $this->belongsToMany(Service::class)->withTimestamps();
    }

    public function getPhotoUrlAttribute(): ?string
    {
        if (! empty($this->photo)) {
            if (str_starts_with($this->photo, 'http://') || str_starts_with($this->photo, 'https://')) {
                return $this->photo;
            }

            return asset('storage/'.ltrim($this->photo, '/'));
        }

        return null;
    }

    public function getInitialsAttribute(): string
    {
        $clean = preg_replace('/^(Dr\.|Dra\.|Doctor|Doctora)\s+/i', '', trim((string) $this->name));
        $clean = preg_replace('/(,\s*(DDS|DMD|PhD|MD|MS))$/i', '', (string) $clean);
        $words = preg_split('/\s+/', trim((string) $clean));
        if (empty($words) || empty($words[0])) {
            return 'DR';
        }
        $first = mb_substr($words[0], 0, 1, 'UTF-8');
        $last = count($words) > 1 ? mb_substr(end($words), 0, 1, 'UTF-8') : '';

        return mb_strtoupper($first.$last);
    }

    protected static function booted(): void
    {
        static::saving(function (Dentist $dentist) {
            if (blank($dentist->slug) && filled($dentist->name)) {
                $dentist->slug = static::generateUniqueSlug($dentist->name, $dentist->id);
            } elseif (filled($dentist->slug)) {
                $dentist->slug = static::generateUniqueSlug($dentist->slug, $dentist->id);
            }
        });
    }

    /**
     * Generate a clean, URL-safe, accent-free, and collision-free unique slug.
     * E.g. "Juan José" -> "juan-jose", and if it already exists -> "juan-jose-1", "juan-jose-2".
     */
    public static function generateUniqueSlug(string $name, ?int $ignoreId = null): string
    {
        $baseSlug = Str::slug($name);
        if (empty($baseSlug)) {
            $baseSlug = 'especialista';
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
