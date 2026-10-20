@php
    $adminClinic = \App\Models\ClinicSetting::first();
@endphp

@if($adminClinic?->logo_asset_url)
    <div class="aura-clinic-logo-wrapper" style="display: flex; align-items: center; gap: 0.85rem; user-select: none;">
        <img src="{{ $adminClinic->logo_asset_url }}" alt="{{ $adminClinic->clinic_name }}" style="height: 38px; width: auto; max-width: 170px; object-fit: contain;" />
    </div>
@else
    <div class="aura-clinic-logo-wrapper" style="display: flex; align-items: center; gap: 0.85rem; user-select: none;">
        <!-- Medical Cyan/Blue Squircle Badge -->
        <div class="aura-logo-badge" style="display: flex; align-items: center; justify-content: center; width: 42px; height: 42px; min-width: 42px; border-radius: 14px; background: linear-gradient(135deg, #38bdf8 0%, #0284c7 100%); box-shadow: 0 4px 16px rgba(14, 165, 233, 0.45); flex-shrink: 0;">
            <svg style="width: 22px; height: 22px; min-width: 22px; min-height: 22px; stroke: #ffffff; fill: none; stroke-width: 2.5; stroke-linecap: round; stroke-linejoin: round;" viewBox="0 0 24 24">
                <path d="M22 12h-4l-3 9L9 3l-3 9H2" />
            </svg>
        </div>

        <!-- Clinic Typography -->
        <div class="brand-text-title" style="display: flex; flex-direction: column; line-height: 1.15;">
            <div style="font-size: 18px; font-weight: 800; letter-spacing: -0.02em; display: flex; align-items: center;">
                <span style="color: #ffffff; font-weight: 900;">Aura</span><span style="color: #38bdf8; font-weight: 900; margin-left: 1px;">Dental</span>
            </div>
            <span style="font-size: 10px; font-weight: 700; color: #94a3b8; letter-spacing: 0.12em; text-transform: uppercase; margin-top: 3px;">STUDIO CLÍNICO</span>
        </div>
    </div>
@endif
