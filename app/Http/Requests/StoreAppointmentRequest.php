<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreAppointmentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'service_id' => ['required', 'exists:services,id'],
            'dentist_id' => ['nullable', 'exists:dentists,id'],
            'appointment_date' => ['required', 'date', 'after_or_equal:today'],
            'appointment_time' => ['required', 'string', 'regex:/^([0-1]?[0-9]|2[0-3]):[0-5][0-9]$/'],
            'patient_name' => ['required', 'string', 'max:150'],
            'patient_email' => ['required', 'email', 'max:150'],
            'patient_phone' => ['required', 'string', 'max:40'],
            'is_first_visit' => ['nullable', 'boolean'],
            'symptoms' => ['nullable', 'string', 'max:1000'],
        ];
    }

    /**
     * Custom error messages in Spanish for user convenience.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'service_id.required' => 'Por favor, selecciona un tratamiento o servicio dental.',
            'service_id.exists' => 'El tratamiento seleccionado no es válido.',
            'dentist_id.exists' => 'El especialista seleccionado no es válido.',
            'appointment_date.required' => 'Por favor, indica la fecha de tu cita.',
            'appointment_date.date' => 'La fecha no tiene un formato válido.',
            'appointment_date.after_or_equal' => 'La fecha de la cita no puede ser anterior al día de hoy.',
            'appointment_time.required' => 'Por favor, selecciona un horario disponible.',
            'appointment_time.regex' => 'El formato del horario debe ser HH:MM (ej. 10:30).',
            'patient_name.required' => 'Por favor, ingresa tu nombre completo.',
            'patient_email.required' => 'Por favor, ingresa tu correo electrónico.',
            'patient_email.email' => 'El correo electrónico no es válido.',
            'patient_phone.required' => 'Por favor, ingresa tu número de teléfono o WhatsApp.',
        ];
    }
}
