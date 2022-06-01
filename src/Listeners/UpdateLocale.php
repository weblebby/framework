<?php

namespace Feadmin\Listeners;

use Feadmin\Facades\Localization;
use Feadmin\Services\LocalizationService;
use Illuminate\Foundation\Events\LocaleUpdated;
use Locale;

class UpdateLocale
{
    public function handle(LocaleUpdated $event)
    {
        $localization = resolve(LocalizationService::class);

        $preferredLocale = $localization->getPreferredLocale([
            ['code' => $event->locale],
            ['is_default' => true],
            [],
        ]) ?? $localization->getDefaultLocale();

        if ($preferredLocale->code !== $event->locale) {
            app()->setLocale($preferredLocale->code);

            return;
        }

        Localization::setCurrentLocale($preferredLocale);
        Locale::setDefault($preferredLocale->code);
    }
}
