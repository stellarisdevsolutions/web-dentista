<?php

namespace App\Services;

use App\Enums\AppointmentStatus;
use App\Models\Appointment;
use App\Models\Dentist;
use App\Models\User;
use Carbon\Carbon;

class DashboardSpecialistsService
{
    /**
     * Paleta de colores fija para los avatares asignada deterministamente por ID.
     */
    protected const AVATAR_PALETTE = [
        '#0EA5E9',
        '#34D399',
        '#A78BFA',
        '#FB923C',
        '#38BDF8',
        '#F472B6',
    ];

    /**
     * Obtiene los datos consolidados de especialistas para el widget de hoy.
     */
    public function getSpecialistsSummary(?User $user): TodaySpecialistsSummaryData
    {
        $today = Carbon::today()->toDateString();

        // 1. Obtener dentistas activos ordenados
        $dentists = Dentist::query()
            ->where('is_active', true)
            ->orderBy('order')
            ->orderBy('name')
            ->get();

        if ($dentists->isEmpty()) {
            return new TodaySpecialistsSummaryData(
                specialists: [],
                inShiftCount: 0,
                totalCount: 0,
                isEmpty: true,
            );
        }

        // 2. Consulta agrupada de citas de hoy excluyendo canceladas
        $todayAppointments = Appointment::query()
            ->whereDate('appointment_date', $today)
            ->whereNotIn('status', ['cancelled'])
            ->get()
            ->groupBy('dentist_id');

        $specialistItems = [];
        $inShiftCount = 0;

        foreach ($dentists as $dentist) {
            $appointments = $todayAppointments->get($dentist->id, collect());
            $appointmentsCount = $appointments->count();

            // Calcular estado según citas en curso y volumen de citas hoy
            $hasInConsultation = $appointments->contains(function (Appointment $app): bool {
                return $app->status === AppointmentStatus::InProgress || $app->status === 'in_progress';
            });

            if ($hasInConsultation) {
                $statusKey = 'in_consultation';
                $statusLabel = 'En consulta';
                $statusBgColor = '#E0F2FE';
                $statusTextColor = '#0A74B8';
                $inShiftCount++;
            } elseif ($appointmentsCount > 0) {
                $statusKey = 'available';
                $statusLabel = 'Disponible';
                $statusBgColor = '#DCFCE7';
                $statusTextColor = '#166534';
                $inShiftCount++;
            } else {
                $statusKey = 'no_appointments';
                $statusLabel = 'Sin citas hoy';
                $statusBgColor = '#E6ECF4';
                $statusTextColor = '#5B6B86';
            }

            // Iniciales: primera letra del nombre y primera letra del apellido
            $cleanName = trim($dentist->name);
            $nameParts = preg_split('/\s+/', $cleanName);
            if (count($nameParts) >= 2) {
                $initials = mb_substr($nameParts[0], 0, 1).mb_substr($nameParts[count($nameParts) - 1], 0, 1);
            } else {
                $initials = mb_substr($cleanName, 0, 2);
            }
            $initials = mb_strtoupper($initials);

            // Color del avatar determinista con id % 6
            $avatarBgColor = self::AVATAR_PALETTE[$dentist->id % count(self::AVATAR_PALETTE)];

            $specialty = ! empty($dentist->specialty) ? $dentist->specialty : 'Odontología general';
            $appointmentsText = $appointmentsCount === 1 ? '1 cita hoy' : "{$appointmentsCount} citas hoy";

            $specialistItems[] = new TodaySpecialistItemData(
                id: $dentist->id,
                name: $cleanName,
                specialty: $specialty,
                initials: $initials,
                avatarBgColor: $avatarBgColor,
                statusKey: $statusKey,
                statusLabel: $statusLabel,
                statusBgColor: $statusBgColor,
                statusTextColor: $statusTextColor,
                todayAppointmentsCount: $appointmentsCount,
                todayAppointmentsText: $appointmentsText,
            );
        }

        return new TodaySpecialistsSummaryData(
            specialists: $specialistItems,
            inShiftCount: $inShiftCount,
            totalCount: count($specialistItems),
            isEmpty: false,
        );
    }
}
