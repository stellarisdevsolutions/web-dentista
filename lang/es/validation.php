<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Líneas de Lenguaje de Validación
    |--------------------------------------------------------------------------
    |
    | Las siguientes líneas contienen los mensajes de error predeterminados
    | utilizados por la clase validadora.
    |
    */

    'accepted' => 'El campo :attribute debe ser aceptado.',
    'accepted_if' => 'El campo :attribute debe ser aceptado cuando :other es :value.',
    'active_url' => 'El campo :attribute debe ser una URL válida.',
    'after' => 'El campo :attribute debe ser una fecha posterior a :date.',
    'after_or_equal' => 'El campo :attribute debe ser una fecha posterior o igual a :date.',
    'alpha' => 'El campo :attribute solo puede contener letras.',
    'alpha_dash' => 'El campo :attribute solo puede contener letras, números, guiones y guiones bajos.',
    'alpha_num' => 'El campo :attribute solo puede contener letras y números.',
    'array' => 'El campo :attribute debe ser un conjunto.',
    'before' => 'El campo :attribute debe ser una fecha anterior a :date.',
    'before_or_equal' => 'El campo :attribute debe ser una fecha anterior o igual a :date.',
    'between' => [
        'array' => 'El campo :attribute debe tener entre :min y :max elementos.',
        'file' => 'El archivo :attribute debe pesar entre :min y :max kilobytes.',
        'numeric' => 'El campo :attribute debe estar entre :min y :max.',
        'string' => 'El campo :attribute debe tener entre :min y :max caracteres.',
    ],
    'boolean' => 'El campo :attribute debe ser verdadero o falso.',
    'confirmed' => 'La confirmación de :attribute no coincide.',
    'date' => 'El campo :attribute debe ser una fecha válida.',
    'date_equals' => 'El campo :attribute debe ser una fecha igual a :date.',
    'date_format' => 'El campo :attribute debe coincidir con el formato :format.',
    'decimal' => 'El campo :attribute debe tener :decimal decimales.',
    'different' => 'Los campos :attribute y :other deben ser diferentes.',
    'digits' => 'El campo :attribute debe tener :digits dígitos.',
    'digits_between' => 'El campo :attribute debe tener entre :min y :max dígitos.',
    'email' => 'El campo :attribute debe ser un correo electrónico válido.',
    'ends_with' => 'El campo :attribute debe finalizar con uno de los siguientes valores: :values.',
    'enum' => 'El valor seleccionado en :attribute es inválido.',
    'exists' => 'El valor seleccionado en :attribute es inválido.',
    'file' => 'El campo :attribute debe ser un archivo.',
    'filled' => 'El campo :attribute debe tener un valor.',
    'image' => 'El campo :attribute debe ser una imagen.',
    'in' => 'El campo :attribute seleccionado no es válido.',
    'integer' => 'El campo :attribute debe ser un número entero.',
    'max' => [
        'array' => 'El campo :attribute no debe tener más de :max elementos.',
        'file' => 'El archivo :attribute no debe pesar más de :max kilobytes.',
        'numeric' => 'El campo :attribute no debe ser mayor a :max.',
        'string' => 'El campo :attribute no debe ser mayor a :max caracteres.',
    ],
    'min' => [
        'array' => 'El campo :attribute debe tener al menos :min elementos.',
        'file' => 'El archivo :attribute debe pesar al menos :min kilobytes.',
        'numeric' => 'El campo :attribute debe ser al menos :min.',
        'string' => 'El campo :attribute debe tener al menos :min caracteres.',
    ],
    'numeric' => 'El campo :attribute debe ser un número.',
    'regex' => 'El formato del campo :attribute no es válido.',
    'required' => 'El campo :attribute es obligatorio.',
    'required_if' => 'El campo :attribute es obligatorio cuando :other es :value.',
    'required_unless' => 'El campo :attribute es obligatorio a menos que :other esté en :values.',
    'required_with' => 'El campo :attribute es obligatorio cuando :values está presente.',
    'required_with_all' => 'El campo :attribute es obligatorio cuando :values están presentes.',
    'required_without' => 'El campo :attribute es obligatorio cuando :values no está presente.',
    'required_without_all' => 'El campo :attribute es obligatorio cuando ninguno de los campos :values está presente.',
    'same' => 'El campo :attribute y :other deben coincidir.',
    'size' => [
        'array' => 'El campo :attribute debe contener :size elementos.',
        'file' => 'El archivo :attribute debe pesar :size kilobytes.',
        'numeric' => 'El campo :attribute debe ser :size.',
        'string' => 'El campo :attribute debe tener :size caracteres.',
    ],
    'string' => 'El campo :attribute debe ser una cadena de texto.',
    'unique' => 'El campo :attribute ya ha sido registrado.',
    'url' => 'El campo :attribute debe ser una URL válida.',

    /*
    |--------------------------------------------------------------------------
    | Atributos de Validación Personalizados
    |--------------------------------------------------------------------------
    */
    'attributes' => [
        'clinic_name' => 'nombre de la clínica',
        'phone' => 'teléfono',
        'whatsapp' => 'WhatsApp',
        'email' => 'correo electrónico',
        'emergency_phone' => 'teléfono de urgencias',
        'address' => 'dirección',
        'patient_name' => 'nombre del paciente',
        'patient_phone' => 'teléfono del paciente',
        'patient_email' => 'correo del paciente',
        'appointment_date' => 'fecha de la cita',
        'appointment_time' => 'hora de la cita',
    ],

];
