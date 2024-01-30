<?php

namespace Weblebby\Framework\Listeners;

use Spatie\MediaLibrary\Conversions\Events\ConversionHasBeenCompleted;

class DeleteOriginalMedia
{
    public function handle(ConversionHasBeenCompleted $event): void
    {
        $conversions = $event->media->getMediaConversionNames();
        $currentConversion = $event->conversion->getName();

        if (end($conversions) === $currentConversion) {
            unlink($event->media->getPath());
        }
    }
}
