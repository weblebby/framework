<?php

namespace Feadmin\Managers;

use Feadmin\Contracts\Eloquent\PostInterface;
use Feadmin\Items\TaxonomyItem;

class PostModelsManager
{
    /**
     * @var array<string, PostInterface>
     */
    protected array $models = [];

    public function register(string|array $model): void
    {
        if (is_array($model)) {
            foreach ($model as $item) {
                $this->register($item);
            }

            return;
        }

        $this->models[$model::getModelName()] = new $model;
    }

    /**
     * @return array<string, PostInterface>
     */
    public function get(): array
    {
        return $this->models;
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
     * @return array<string, TaxonomyItem>
     */
    public function taxonomies(): array
    {
        return collect($this->models)
            ->map(fn ($model) => $model::getTaxonomies())
            ->flatten()
            ->all();
    }

    public function taxonomy(?string $taxonomy): ?TaxonomyItem
    {
        if (is_null($taxonomy)) {
            return null;
        }

        return collect($this->taxonomies())->first(fn (TaxonomyItem $item) => $item->name() === $taxonomy);
    }
}
