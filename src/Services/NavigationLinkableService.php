<?php

namespace Feadmin\Services;

use Illuminate\Support\Collection;

class NavigationLinkableService
{
    protected Collection $linkables;

    public function __construct()
    {
        $this->linkables = collect();
    }

    public function add(array $data): self
    {
        $this->linkables[] = [
            'position' => is_null($data['position'] ?? null)
                ? count($this->linkables) * 10
                : $data['position'],
            ...$data,
        ];

        return $this;
    }

    public function linkables(): Collection
    {
        return $this->linkables->sortBy('position');
    }
}
