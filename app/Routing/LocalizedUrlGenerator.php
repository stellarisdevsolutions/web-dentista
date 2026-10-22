<?php

namespace App\Routing;

use BackedEnum;
use Illuminate\Routing\UrlGenerator;

class LocalizedUrlGenerator extends UrlGenerator
{
    /**
     * Map of base route names to their localized variants.
     *
     * @var array<string, array<string, string>>
     */
    protected array $localizedMap = [
        'en' => [
            'home' => 'home.en',
            'about' => 'about.en',
            'services.index' => 'services.index.en',
            'services.show' => 'services.show.en',
            'contact' => 'contact.en',
            'contact.submit' => 'contact.submit.en',
            'blog.index' => 'blog.index.en',
            'blog.show' => 'blog.show.en',
            'schedule' => 'schedule.en',
            'privacy' => 'privacy.en',
            'terms' => 'terms.en',
            'patient-rights' => 'patient-rights.en',
        ],
        'es' => [
            'home.en' => 'home',
            'about.en' => 'about',
            'services.index.en' => 'services.index',
            'services.show.en' => 'services.show',
            'contact.en' => 'contact',
            'contact.submit.en' => 'contact.submit',
            'blog.index.en' => 'blog.index',
            'blog.show.en' => 'blog.show',
            'schedule.en' => 'schedule',
            'privacy.en' => 'privacy',
            'terms.en' => 'terms',
            'patient-rights.en' => 'patient-rights',
        ],
    ];

    /**
     * Get the URL to a named route, honoring active application locale.
     *
     * @param  BackedEnum|string  $name
     * @param  mixed  $parameters
     * @param  bool  $absolute
     */
    public function route($name, $parameters = [], $absolute = true): string
    {
        if ($name instanceof BackedEnum) {
            $name = $name->value;
        }

        $locale = app()->getLocale();

        if (isset($this->localizedMap[$locale][$name])) {
            $candidateName = $this->localizedMap[$locale][$name];
            if (! is_null($this->routes->getByName($candidateName))) {
                $name = $candidateName;
            }
        }

        return parent::route($name, $parameters, $absolute);
    }
}
