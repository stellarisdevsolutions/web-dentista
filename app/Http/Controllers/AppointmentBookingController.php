<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAppointmentRequest;
use App\Models\Appointment;
use App\Models\ClinicSetting;
use App\Models\Dentist;
use App\Models\Service;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class AppointmentBookingController extends Controller
{
    /**
     * Render the dedicated online appointment scheduling page.
     */
    public function schedule(Request $request): View
    {
        $clinic = ClinicSetting::first() ?? new ClinicSetting([
            'clinic_name' => 'Aura Dental Studio',
            'tagline_es' => 'Odontología moderna, integral y sin dolor para sonrisas radiantes.',
            'phone' => '+1 (555) 234-5678',
            'whatsapp' => '+15552345678',
            'email' => 'hola@auradental.com',
            'address' => 'Av. Las Palmas 742, Piso 4, Distrito Salud',
            'emergency_phone' => '+1 (555) 911-DENT',
            'opening_hours_es' => "Lunes a Viernes: 8:00 AM - 7:00 PM\nSábado: 9:00 AM - 3:00 PM\nDomingo: Solo Urgencias",
        ]);

        $services = Service::where('is_active', true)->with('dentists')->orderBy('order')->get();
        $dentists = Dentist::where('is_active', true)->with('services')->orderBy('order')->get();
        $selectedServiceId = $request->query('service_id') ?? $request->query('service');
        $selectedDentistId = $request->query('dentist_id') ?? $request->query('dentist');

        return view('schedule', compact(
            'clinic',
            'services',
            'dentists',
            'selectedServiceId',
            'selectedDentistId'
        ));
    }

    /**
     * Store a newly created appointment from the online booking wizard.
     */
    public function store(StoreAppointmentRequest $request): JsonResponse|RedirectResponse
    {
        $validated = $request->validated();

        $service = Service::findOrFail($validated['service_id']);

        $date = $validated['appointment_date'];
        $time = $validated['appointment_time'];
        $duration = (int) ($service->duration_minutes ?? 45);

        // Intelligent Dentist Allocation & Load-Balancing Algorithm:
        $dentistId = $validated['dentist_id'] ?? null;

        if ($dentistId) {
            $dentist = Dentist::findOrFail($dentistId);

            if (! $dentist->isTimeSlotAvailable($date, $time, $duration)) {
                $errorResponse = [
                    'message' => 'El especialista seleccionado no tiene disponibilidad en este horario. Por favor selecciona otro turno.',
                    'errors' => ['appointment_time' => ['El horario ya no está disponible con este especialista.']],
                ];

                if ($request->wantsJson() || $request->ajax()) {
                    return response()->json($errorResponse, 422);
                }

                return back()->withErrors($errorResponse['errors'])->withInput();
            }
        } else {
            // Unified Booking (New Patient or Returning Patient choosing "Primer disponible"):
            // 1. Get qualified active dentists for this treatment
            $candidates = $service->dentists()->where('is_active', true)->get();
            if ($candidates->isEmpty()) {
                $candidates = Dentist::where('is_active', true)->get();
            }

            // 2. Filter dentists who have enough consecutive free time without shift/appointment collisions
            $availableCandidates = $candidates->filter(function (Dentist $doc) use ($date, $time, $duration) {
                return $doc->isTimeSlotAvailable($date, $time, $duration);
            });

            if ($availableCandidates->isEmpty()) {
                $errorResponse = [
                    'message' => 'Lo sentimos, este horario acaba de ser ocupado. Por favor elige otro horario disponible.',
                    'errors' => ['appointment_time' => ['El horario seleccionado ya no está disponible.']],
                ];

                if ($request->wantsJson() || $request->ajax()) {
                    return response()->json($errorResponse, 422);
                }

                return back()->withErrors($errorResponse['errors'])->withInput();
            }

            // 3. Intelligent Load Balancing:
            // Assign to the dentist with the fewest active appointments on that specific date
            $assignedDentist = $availableCandidates
                ->sortBy(function (Dentist $doc) use ($date) {
                    return $doc->appointments()
                        ->whereDate('appointment_date', $date)
                        ->whereIn('status', ['confirmed', 'pending'])
                        ->count();
                })
                ->first();

            $dentistId = $assignedDentist->id;
        }

        $appointment = Appointment::create([
            'service_id' => $service->id,
            'dentist_id' => $dentistId,
            'patient_name' => $validated['patient_name'],
            'patient_email' => $validated['patient_email'],
            'patient_phone' => $validated['patient_phone'],
            'appointment_date' => $validated['appointment_date'],
            'appointment_time' => $validated['appointment_time'],
            'duration_minutes' => $service->duration_minutes ?? 45,
            'is_first_visit' => (bool) ($validated['is_first_visit'] ?? false),
            'symptoms' => $validated['symptoms'] ?? null,
            'preferred_locale' => app()->getLocale(),
            'status' => 'pending',
        ]);

        $clinic = ClinicSetting::first();
        $dentist = $appointment->dentist;
        $dentistName = $dentist ? $dentist->name : 'Especialista de turno';

        // Prepare WhatsApp message for 1-click confirmation with clinic
        $clinicPhone = preg_replace('/[^0-9]/', '', (string) ($clinic?->whatsapp ?? '15552345678'));
        $formattedDate = Carbon::parse($appointment->appointment_date)->translatedFormat('d \d\e F, Y');

        $whatsappMessage = "¡Hola {$clinic?->clinic_name}! Acabo de agendar una cita por la web.\n\n"
            ."📌 *Código de reserva:* {$appointment->code}\n"
            ."👤 *Paciente:* {$appointment->patient_name}\n"
            ."🦷 *Tratamiento:* {$service->name}\n"
            ."👨‍⚕️ *Especialista:* {$dentistName}\n"
            ."📅 *Fecha:* {$formattedDate}\n"
            ."⏰ *Horario:* {$appointment->appointment_time}\n"
            ."📱 *Teléfono:* {$appointment->patient_phone}\n\n"
            .'¿Podrían confirmarme los detalles para mi consulta? ¡Muchas gracias!';

        $whatsappUrl = "https://wa.me/{$clinicPhone}?text=".urlencode($whatsappMessage);

        $responsePayload = [
            'success' => true,
            'message' => '¡Tu cita ha sido registrada con éxito! Te esperamos con la mejor atención.',
            'code' => $appointment->code,
            'whatsapp_url' => $whatsappUrl,
            'appointment' => [
                'code' => $appointment->code,
                'service' => $service->name,
                'dentist' => $dentistName,
                'date' => $formattedDate,
                'time' => $appointment->appointment_time,
                'duration' => $appointment->duration_minutes.' min',
                'patient_name' => $appointment->patient_name,
            ],
        ];

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json($responsePayload, 201);
        }

        return redirect()->route('schedule')->with('appointment_success', $responsePayload);
    }

    /**
     * Get specialists available for a specific service.
     */
    public function dentistsForService(Service $service): JsonResponse
    {
        $dentists = $service->dentists()
            ->where('is_active', true)
            ->orderBy('order')
            ->get();

        // If no specialists explicitly assigned, return all active specialists
        if ($dentists->isEmpty()) {
            $dentists = Dentist::where('is_active', true)->orderBy('order')->get();
        }

        $data = $dentists->map(function (Dentist $dentist): array {
            return [
                'id' => $dentist->id,
                'name' => $dentist->name,
                'title' => $dentist->title,
                'specialty' => $dentist->specialty,
                'license_number' => $dentist->license_number,
                'photo' => $dentist->photo,
                'working_days' => $dentist->working_days ?? ['monday', 'tuesday', 'wednesday', 'thursday', 'friday'],
                'working_hours_start' => substr((string) $dentist->working_hours_start, 0, 5),
                'working_hours_end' => substr((string) $dentist->working_hours_end, 0, 5),
            ];
        });

        return response()->json([
            'service_id' => $service->id,
            'service_name' => $service->name,
            'dentists' => $data,
        ]);
    }

    /**
     * Get available time slots for a given dentist and date, supporting Morning/Afternoon shifts.
     */
    public function availableSlots(Request $request): JsonResponse
    {
        $request->validate([
            'dentist_id' => ['nullable', 'exists:dentists,id'],
            'service_id' => ['nullable', 'exists:services,id'],
            'date' => ['required', 'date', 'after_or_equal:today'],
        ]);

        $dentistId = $request->query('dentist_id');
        $serviceId = $request->query('service_id');
        $date = $request->query('date');

        $service = $serviceId ? Service::find($serviceId) : null;
        $durationMinutes = $service?->duration_minutes ?? 45;

        if ($dentistId) {
            $dentist = Dentist::findOrFail($dentistId);
            $slots = $dentist->getSlotsForDate($date, $durationMinutes);
        } else {
            // Aggregate slots across specialists for this service or all active
            if ($service && $service->dentists()->where('is_active', true)->exists()) {
                $dentists = $service->dentists()->where('is_active', true)->get();
            } else {
                $dentists = Dentist::where('is_active', true)->get();
            }

            $aggregatedSlots = [];
            foreach ($dentists as $doc) {
                $docSlots = $doc->getSlotsForDate($date, $durationMinutes);
                foreach ($docSlots as $slot) {
                    $time = $slot['time'];
                    if (! isset($aggregatedSlots[$time])) {
                        $aggregatedSlots[$time] = $slot;
                    } elseif ($slot['available']) {
                        $aggregatedSlots[$time]['available'] = true;
                    }
                }
            }
            ksort($aggregatedSlots);
            $slots = array_values($aggregatedSlots);
        }

        $morningSlots = array_values(array_filter($slots, fn ($s) => ($s['shift'] ?? '') === 'morning'));
        $afternoonSlots = array_values(array_filter($slots, fn ($s) => ($s['shift'] ?? '') === 'afternoon'));

        return response()->json([
            'date' => $date,
            'dentist_id' => $dentistId,
            'slots' => $slots,
            'morning_slots' => $morningSlots,
            'afternoon_slots' => $afternoonSlots,
            'has_available_slots' => collect($slots)->contains('available', true),
        ]);
    }
}
