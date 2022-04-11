<?php

namespace Feadmin\Services;

use Feadmin\Facades\Extension;
use Feadmin\Models\Navigation;
use Illuminate\Support\Collection;

class NavigationService
{
    public function getForListing(): Collection
    {
        return Navigation::select('id', 'title')->get();
    }

    public function smartMenuItems(): Collection
    {
        return Extension::enabled()->where('category', 'content');
    }
}
