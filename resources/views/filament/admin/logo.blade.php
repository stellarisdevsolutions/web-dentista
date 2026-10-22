@php
    $adminClinic = \App\Models\ClinicSetting::first();
    $clinicName = trim($adminClinic?->clinic_name ?? config('app.name', 'Aura Dental Studio'));
    $words = preg_split('/\s+/', $clinicName, 2);
    $firstWord = $words[0] ?? $clinicName;
    $remainingWords = $words[1] ?? '';
@endphp

<div class="aura-clinic-logo-wrapper" style="display: flex; align-items: center; gap: 0.85rem; user-select: none;">
    @if($adminClinic?->logo_asset_url)
        <!-- Uploaded Clinic Logo Icon Badge -->
        <div class="aura-logo-badge" style="display: flex; align-items: center; justify-content: center; width: 42px; height: 42px; min-width: 42px; border-radius: 12px; background: rgba(255, 255, 255, 0.06); border: 1px solid rgba(255, 255, 255, 0.12); overflow: hidden; flex-shrink: 0;">
            <img src="{{ $adminClinic->logo_asset_url }}" alt="{{ $clinicName }}" style="max-height: 32px; max-width: 32px; width: auto; height: auto; object-fit: contain;" />
        </div>
    @else
        <!-- Medical Cyan/Blue Squircle Badge -->
        <div class="aura-logo-badge" style="display: flex; align-items: center; justify-content: center; width: 42px; height: 42px; min-width: 42px; border-radius: 14px; background: linear-gradient(135deg, #38bdf8 0%, #0284c7 100%); box-shadow: 0 4px 16px rgba(14, 165, 233, 0.45); flex-shrink: 0;">
            <svg style="width: 22px; height: 22px; min-width: 22px; min-height: 22px; stroke: #ffffff; fill: none; stroke-width: 2.5; stroke-linecap: round; stroke-linejoin: round;" viewBox="0 0 24 24">
                <path d="M22 12h-4l-3 9L9 3l-3 9H2" />
            </svg>
        </div>
    @endif

    <!-- Clinic Typography: Only clinic name, 2 lines max with ellipsis and contrasting color -->
    <div class="brand-text-title" style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; text-overflow: ellipsis; max-width: 170px; line-height: 1.15; font-size: 15px; letter-spacing: -0.02em; word-break: break-word;">
        <span style="color: #ffffff; font-weight: 900;">{{ $firstWord }}</span>@if(!empty($remainingWords))<span style="color: #38bdf8; font-weight: 800; margin-left: 4px;">{{ $remainingWords }}</span>@endif
    </div>
</div>
