<?php

namespace Feadmin\Managers;

use Feadmin\Items\NavigationLinkableItem;
use Illuminate\Support\Collection;

class NavigationLinkableManager
{
    protected Collection $linkables;

    public function __construct()
    {
        $this->linkables = collect();
    }

    public function add(NavigationLinkableItem $item): self
    {
        if (is_null($item->position())) {
            $item->setPosition(count($this->linkables) * 10);
        }

        $this->linkables[] = $item;

        return $this;
    }

    public function linkables(): Collection
    {
        return $this->linkables->sortBy('position');
    }
}