<?php

namespace App\Models\Concerns;

trait HasBilingualAttributes
{
    /**
     * Get a localized attribute dynamically based on active locale and fallback.
     */
    public function getLocalized(string $attribute): string
    {
        $currentLocale = app()->getLocale();
        $fallbackLocale = config('app.fallback_locale', 'es');
        $alternateLocale = $currentLocale === 'es' ? 'en' : 'es';

        $currentVal = $this->{"{$attribute}_{$currentLocale}"} ?? null;
        if (! empty($currentVal)) {
            return (string) $currentVal;
        }

        $fallbackVal = $this->{"{$attribute}_{$fallbackLocale}"} ?? null;
        if (! empty($fallbackVal)) {
            return (string) $fallbackVal;
        }

        $alternateVal = $this->{"{$attribute}_{$alternateLocale}"} ?? null;
        if (! empty($alternateVal)) {
            return (string) $alternateVal;
        }

        return '';
    }
}
