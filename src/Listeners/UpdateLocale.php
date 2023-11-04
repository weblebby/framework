<?php

namespace Feadmin\Listeners;

use Feadmin\Facades\Localization;
use Feadmin\Managers\LocalizationManager;
use Illuminate\Foundation\Events\LocaleUpdated;
use Locale;

class UpdateLocale
{
    public function handle(LocaleUpdated $event): void
    {
        $localization = resolve(LocalizationManager::class);

        $preferredLocale = $localization->getPreferredLocale([
            ['code' => $event->locale],
            ['is_default' => true],
            [],
        ]) ?? $localization->getDefaultLocale();

        if ($preferredLocale->code !== $event->locale) {
            app()->setLocale($preferredLocale->code);

            return;
        }

        Locale::setDefault($preferredLocale->code);
        Localization::setCurrentLocale($preferredLocale);
        Localization::loadAllLocales();
    }
}
