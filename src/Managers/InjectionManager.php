<?php

namespace Weblebby\Framework\Managers;

use Closure;
use Illuminate\Support\HtmlString;
use Illuminate\View\View;
use Weblebby\Framework\Enums\InjectionTypeEnum;

class InjectionManager
{
    protected array $injections = [];

    /**
     * @param  string|array<int, string>  $name
     */
    public function add(InjectionTypeEnum|string|array $name, Closure $callable): void
    {
        if (is_array($name)) {
            foreach ($name as $item) {
                $this->add($item, $callable);
            }

            return;
        }

        if ($name instanceof \BackedEnum) {
            $name = $name->value;
        }

        $this->injections[$name] = [
            ...$this->injections[$name] ?? [],
            $callable,
        ];
    }

    public function call(InjectionTypeEnum|string $name, mixed $default = null): mixed
    {
        if ($name instanceof \BackedEnum) {
            $name = $name->value;
        }

        return array_map(function ($value) use ($default) {
            if (is_callable($value)) {
                $value = $value();
            }

            return $value ?? $default;
        }, $this->injections[$name] ?? []);
    }

    public function render(InjectionTypeEnum|string $name): HtmlString
    {
        if ($name instanceof \BackedEnum) {
            $name = $name->value;
        }

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
