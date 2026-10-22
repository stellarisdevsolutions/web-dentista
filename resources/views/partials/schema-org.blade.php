@php
    $currentClinic = $clinic ?? \App\Models\ClinicSetting::first() ?? new \App\Models\ClinicSetting([
        'clinic_name' => config('app.name', 'Dental Clinic'),
        'phone' => '+1 (555) 234-5678',
        'email' => 'contacto@auradental.com',
        'address' => 'Av. Principal 123',
    ]);

    $logoUrl = $currentClinic->logo_asset_url ?? asset('images/aura-logo.svg');
    if (!empty($logoUrl) && !str_starts_with($logoUrl, 'http://') && !str_starts_with($logoUrl, 'https://')) {
        $logoUrl = url($logoUrl);
    }

    $hoursLines = array_values(array_filter(array_map('trim', explode("\n", $currentClinic->opening_hours ?? ''))));

    $sameAs = [];
    if (!empty($currentClinic->social_links) && is_array($currentClinic->social_links)) {
        $sameAs = array_values(array_filter($currentClinic->social_links));
    }

    $clinicSchema = [
        '@context' => 'https://schema.org',
        '@type' => ['Dentist', 'MedicalClinic', 'LocalBusiness'],
        '@id' => url('/') . '#clinic',
        'name' => $currentClinic->clinic_name,
        'description' => $currentClinic->tagline ?? __('Cuidado dental suave, integral e indoloro.'),
        'url' => url('/'),
        'telephone' => $currentClinic->phone,
        'email' => $currentClinic->email,
        'address' => [
            '@type' => 'PostalAddress',
            'streetAddress' => $currentClinic->address,
        ],
        'priceRange' => '$$',
        'currenciesAccepted' => 'USD, EUR',
        'paymentAccepted' => 'Cash, Credit Card, Debit Card',
        'medicalSpecialty' => [
            'Dentistry',
            'Orthodontics',
            'Periodontics',
            'Endodontics',
            'Prosthodontics',
            'OralSurgery',
            'PediatricDentistry',
        ],
    ];

    if (!empty($logoUrl)) {
        $clinicSchema['image'] = $logoUrl;
        $clinicSchema['logo'] = $logoUrl;
    }

    if (!empty($currentClinic->emergency_phone)) {
        $clinicSchema['emergencyTelephone'] = $currentClinic->emergency_phone;
    }

    if (!empty($currentClinic->google_maps_url)) {
        $clinicSchema['hasMap'] = $currentClinic->google_maps_url;
    }

    if (!empty($hoursLines)) {
        $clinicSchema['openingHours'] = $hoursLines;
    }

    if (!empty($sameAs)) {
        $clinicSchema['sameAs'] = $sameAs;
    }
@endphp

<script type="application/ld+json">
{!! json_encode($clinicSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) !!}
</script>

@if(!empty($service))
    @php
        $serviceSchema = [
            '@context' => 'https://schema.org',
            '@type' => 'MedicalProcedure',
            'name' => $service->name ?? $service->title,
            'description' => strip_tags($service->short_description ?? $service->summary ?? $service->description ?? ''),
            'procedureType' => 'NonSurgicalProcedure',
            'medicalSpecialty' => 'Dentistry',
            'provider' => [
                '@id' => url('/') . '#clinic',
            ],
        ];

        if (!empty($service->price)) {
            $serviceSchema['offers'] = [
                '@type' => 'Offer',
                'price' => (string) $service->price,
                'priceCurrency' => 'USD',
                'availability' => 'https://schema.org/InStock',
            ];
        }
    @endphp
    <script type="application/ld+json">
    {!! json_encode($serviceSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) !!}
    </script>
@endif

@if(!empty($post))
    @php
        $postImageUrl = $post->cover_image_url ?? $logoUrl;
        if (!empty($postImageUrl) && !str_starts_with($postImageUrl, 'http://') && !str_starts_with($postImageUrl, 'https://')) {
            $postImageUrl = url($postImageUrl);
        }

        $postSchema = [
            '@context' => 'https://schema.org',
            '@type' => 'BlogPosting',
            'headline' => $post->title,
            'description' => strip_tags($post->excerpt ?? \Illuminate\Support\Str::limit($post->content ?? '', 160)),
            'image' => $postImageUrl,
            'datePublished' => $post->published_at?->toIso8601String() ?? $post->created_at?->toIso8601String(),
            'dateModified' => $post->updated_at?->toIso8601String() ?? now()->toIso8601String(),
            'author' => [
                '@type' => 'Organization',
                'name' => $currentClinic->clinic_name,
            ],
            'publisher' => [
                '@id' => url('/') . '#clinic',
            ],
            'mainEntityOfPage' => [
                '@type' => 'WebPage',
                '@id' => url()->current(),
            ],
        ];
    @endphp
    <script type="application/ld+json">
    {!! json_encode($postSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) !!}
    </script>
@endif
