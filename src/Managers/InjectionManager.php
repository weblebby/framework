<?php

namespace Weblebby\Framework\Managers;

use Closure;
use Illuminate\Support\HtmlString;
use Illuminate\View\View;

class InjectionManager
{
    protected array $injections = [];

    /**
     * @param string|array<int, string>
     */
    public function add(string|array $name, Closure $callable): void
    {
        if (is_array($name)) {
            foreach ($name as $item) {
                $this->add($item, $callable);
            }

            return;
        }

        $this->injections[$name] = [
            ...$this->injections[$name] ?? [],
            $callable,
        ];
    }

    public function render(string $name): HtmlString
    {
        $string = collect($this->injections[$name] ?? [])
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
