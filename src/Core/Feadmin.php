<?php

namespace Feadmin;

use Feadmin\Hooks\MenuHook;

class Feadmin
{
    private MenuHook $menu;

    public function __construct()
    {
        $this->menu = new MenuHook();
    }

    public function menu(): MenuHook
    {
        return $this->menu;
    }
}
