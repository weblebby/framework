<?php

namespace Feadmin\Services;

use Feadmin\Enums\ExtensionCategoryEnum;
use Feadmin\Facades\Extension;
use Feadmin\Items\ExtensionItem;
use Feadmin\Models\Navigation;
use Illuminate\Support\Collection;

class NavigationService
{
    /**
     * @return Collection<int, Navigation>
     */
    public function getForListing(): Collection
    {
        return Navigation::query()->select('id', 'title')->get();
    }
}
