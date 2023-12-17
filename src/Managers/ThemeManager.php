<?php

namespace Feadmin\Managers;

use Exception;
use Feadmin\Abstracts\Theme\Theme;
use Illuminate\Support\Collection;

class ThemeManager
{
    protected Collection $themes;

    protected string $activeThemeName;

    public function __construct()
    {
        $this->themes = collect();
    }

    /**
     * @throws Exception
     */
    public function register(Theme|string $theme): self
    {
        if (is_string($theme)) {
            $theme = new $theme();
        }

        if ($this->themes->where('name', $theme->name())->isNotEmpty()) {
            throw new Exception(sprintf('Theme [%s] already registered.', $theme->name()));
        }

        $this->themes->push($theme);

        return $this;
    }

    public function activate(string $themeName): self
    {
        if ($this->themes->where('name', $themeName)->isEmpty()) {
            throw new Exception(sprintf("Theme [%s] doesn't exists.", $themeName));
        }

        $this->activeThemeName = $themeName;

        return $this;
    }

    /**
     * @return Collection<int, Theme>
     */
    public function get(): Collection
    {
        return $this->themes;
    }

    public function find(string $themeName): ?Theme
    {
        if ($themeName === '__ACTIVE') {
            return $this->active();
        }

        return $this->themes->firstWhere('name', $themeName);
    }

    public function active(): ?Theme
    {
        return $this->themes->firstWhere('name', $this->activeThemeName);
    }
}
