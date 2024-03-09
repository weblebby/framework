<?php

namespace Weblebby\Framework\Managers;

use Illuminate\Support\Collection;
use Weblebby\Framework\Contracts\Eloquent\PostInterface;
use Weblebby\Framework\Exceptions\InvalidTaxonomyNameException;
use Weblebby\Framework\Exceptions\PostTypeAlreadyRegisteredException;
use Weblebby\Framework\Items\TaxonomyItem;
use Weblebby\Framework\Support\Features;

class PostModelsManager
{
    /**
     * @var array<string, PostInterface>
     */
    protected array $models = [];

    /**
     * @param  class-string<int, PostInterface>|array<class-string<int, PostInterface>>  $model
     *
     * @throws InvalidTaxonomyNameException
     * @throws PostTypeAlreadyRegisteredException
     */
    public function register(string|array $model): void
    {
        if (is_array($model)) {
            foreach ($model as $item) {
                $this->register($item);
            }

            return;
        }

        $key = $model::getModelName();

        if (isset($this->models[$key])) {
            throw new PostTypeAlreadyRegisteredException($model::getModelName());
        }

        foreach ($model::getTaxonomies() as $taxonomy) {
            if (! str_starts_with($taxonomy->name(), sprintf('%s_', $model::getModelName()))) {
                throw new InvalidTaxonomyNameException($model);
            }
        }

        $this->models[$key] = new $model;
    }

    /**
     * @return array<string, PostInterface>
     */
    public function get(): array
    {
        if (panel()->supports(Features::posts())) {
            return $this->models;
        }

        return [];
    }

    public function find(string $key): ?PostInterface
    {
        return $this->models[$key] ?? null;
    }

    public function findOrFail(string $key): PostInterface
    {
        $model = $this->find($key);

        abort_if(is_null($model), 404);

        return $model;
    }

    /**
     * @return Collection<int, TaxonomyItem>
     */
    public function taxonomies(): Collection
    {
        return collect($this->models)
            ->map(fn ($model) => $model::getTaxonomies())
            ->flatten();
    }

    public function taxonomy(?string $taxonomy): ?TaxonomyItem
    {
        if (is_null($taxonomy)) {
            return null;
        }

        return collect($this->taxonomies())->first(fn (TaxonomyItem $item) => $item->name() === $taxonomy);
    }
}
