@php
    $currentClinic = $clinic ?? \App\Models\ClinicSetting::first() ?? new \App\Models\ClinicSetting([
        'clinic_name' => config('app.name', 'Dental Clinic'),
        'tagline' => 'Odontología moderna, integral y sin dolor para toda la familia.',
    ]);

    $currentLocale = app()->getLocale();
    $metaTitle = $title ?? ($currentClinic->clinic_name . ' | ' . __('Odontología Avanzada y Cuidado Dental'));
    $metaDescription = $description ?? ($currentClinic->tagline ?? __('Cuidado dental suave, integral e indoloro para toda la familia.'));
    $metaOgType = $ogType ?? 'website';
    $metaCanonical = $canonicalUrl ?? url()->current();

    $metaImage = $ogImage ?? ($currentClinic->logo_asset_url ?? asset('images/aura-logo.svg'));
    if (!empty($metaImage) && !str_starts_with($metaImage, 'http://') && !str_starts_with($metaImage, 'https://')) {
        $metaImage = url($metaImage);
    }

    $alternateEs = \App\Services\LocalizationService::getAlternateUrl('es');
    $alternateEn = \App\Services\LocalizationService::getAlternateUrl('en');
@endphp

<title>{{ $metaTitle }}</title>
<meta name="description" content="{{ $metaDescription }}">
<meta name="robots" content="{{ $robots ?? 'index, follow' }}">
<link rel="canonical" href="{{ $metaCanonical }}">

{{-- Hreflang Alternates for Search Engines --}}
<link rel="alternate" hreflang="es" href="{{ $alternateEs }}">
<link rel="alternate" hreflang="en" href="{{ $alternateEn }}">
<link rel="alternate" hreflang="x-default" href="{{ $alternateEs }}">

{{-- Open Graph / Facebook / WhatsApp --}}
<meta property="og:site_name" content="{{ $currentClinic->clinic_name }}">
<meta property="og:title" content="{{ $metaTitle }}">
<meta property="og:description" content="{{ $metaDescription }}">
<meta property="og:type" content="{{ $metaOgType }}">
<meta property="og:url" content="{{ $metaCanonical }}">
<meta property="og:locale" content="{{ $currentLocale === 'en' ? 'en_US' : 'es_ES' }}">
<meta property="og:locale:alternate" content="{{ $currentLocale === 'en' ? 'es_ES' : 'en_US' }}">
@if(!empty($metaImage))
<meta property="og:image" content="{{ $metaImage }}">
<meta property="og:image:secure_url" content="{{ $metaImage }}">
<meta property="og:image:alt" content="{{ $currentClinic->clinic_name }}">
@endif

{{-- Twitter Cards --}}
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="{{ $metaTitle }}">
<meta name="twitter:description" content="{{ $metaDescription }}">
@if(!empty($metaImage))
<meta name="twitter:image" content="{{ $metaImage }}">
@endif

{{-- Schema.org Structured Data --}}
@include('partials.schema-org', [
    'clinic' => $currentClinic,
    'service' => $service ?? null,
    'post' => $post ?? null,
])
