<?php

namespace Weblebby\Framework\Managers;

use Illuminate\Support\HtmlString;
use Illuminate\View\View;
use Weblebby\Framework\Enums\InjectionTypeEnum;

class InjectionManager
{
    protected array $injections = [];

    public function has(InjectionTypeEnum|string $key): bool
    {
        if ($key instanceof \BackedEnum) {
            $key = $key->value;
        }

        return isset($this->injections[$key]);
    }

    /**
     * @param  string|array<int, string>  $key
     */
    public function add(InjectionTypeEnum|string|array $key, mixed $value): void
    {
        if (is_array($key)) {
            foreach ($key as $item) {
                $this->add($item, $value);
            }

            return;
        }

        if ($key instanceof \BackedEnum) {
            $key = $key->value;
        }

        $this->injections[$key] = [
            ...$this->injections[$key] ?? [],
            $value,
        ];
    }

    public function call(InjectionTypeEnum|string $key, mixed $default = null): mixed
    {
        if ($key instanceof \BackedEnum) {
            $key = $key->value;
        }

        return array_map(function ($value) use ($default) {
            if (is_callable($value)) {
                $value = $value();
            }

            return $value ?? $default;
        }, $this->injections[$key] ?? []);
    }

    public function render(InjectionTypeEnum|string $key): HtmlString
    {
        if ($key instanceof \BackedEnum) {
            $key = $key->value;
        }

        $string = collect($this->injections[$key] ?? [])
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
