<?php

namespace Feadmin\Managers;

use Feadmin\Items\SmartMenuItem;
use Illuminate\Support\Collection;

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
