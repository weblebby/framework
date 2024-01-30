<?php

namespace Weblebby\Framework\Managers;

use Illuminate\Support\Collection;
use Weblebby\Framework\Items\NavigationLinkableItem;

class NavigationLinkableManager
{
    /**
     * @var Collection<int, NavigationLinkableItem>
     */
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

    /**
     * @return Collection<int, NavigationLinkableItem>
     */
    public function linkables(): Collection
    {
        return $this->linkables->sortBy('position');
    }
}
