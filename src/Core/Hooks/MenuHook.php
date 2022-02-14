<?php

namespace Feadmin\Hooks;

class MenuHook
{
    private string $lastLocation;

    private array $items;

    public function add(array $item)
    {
        $this->items[$this->lastLocation][] = $item;
    }
}
