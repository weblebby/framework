<?php

namespace Weblebby\Framework\Services;

use Illuminate\Support\Collection;
use Weblebby\Framework\Models\Navigation;

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
