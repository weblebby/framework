<?php

namespace Feadmin\Managers;

use Closure;
use Illuminate\Support\HtmlString;
use Illuminate\View\View;

class InjectionManager
{
    protected array $injections = [];

    public function add(string $name, Closure $callable): void
    {
        $this->injections[$name] = [
            ...$this->injections[$name] ?? [],
            $callable,
        ];
    }

    public function render(string $name): HtmlString
    {
        $string = collect($this->injections[$name] ??  [])
            ->map(function ($callable) {
                $callable = $callable();

                if ($callable instanceof View) {
                    return $callable->render();
                }

                return $callable;
            })
            ->implode(PHP_EOL);

        return new HtmlString($string);
    }
}
