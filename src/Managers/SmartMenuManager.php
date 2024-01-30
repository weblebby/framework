<?php

namespace Weblebby\Framework\Managers;

use Illuminate\Support\Collection;
use Weblebby\Framework\Items\SmartMenuItem;

class SmartMenuManager
{
    /**
     * @var Collection<int, SmartMenuItem>
     */
    protected Collection $items;

    public function __construct()
    {
        $this->items = collect();
    }

    public function add(SmartMenuItem $item): self
    {
        if (is_null($item->position())) {
            $item->setPosition(count($this->items) * 10);
        }

        $this->items[] = $item;

        return $this;
    }

    /**
     * @return Collection<int, SmartMenuItem>
     */
    public function items(): Collection
    {
        return $this->items->sortBy('position');
    }
}
