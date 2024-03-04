<?php

namespace Weblebby\Framework\Concerns\Eloquent;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Enumerable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Spatie\MediaLibrary\MediaCollections\Exceptions\FileDoesNotExist;
use Spatie\MediaLibrary\MediaCollections\Exceptions\FileIsTooBig;
use Weblebby\Framework\Enums\FieldTypeEnum;
use Weblebby\Framework\Items\Field\Collections\FieldCollection;
use Weblebby\Framework\Items\Field\Contracts\FieldInterface;
use Weblebby\Framework\Items\Field\FieldValueItem;
use Weblebby\Framework\Models\Metafield;
use Weblebby\Framework\Models\Post;
use Weblebby\Framework\Models\Preference;
use Weblebby\Framework\Models\Taxonomy;
use Weblebby\Framework\Services\User\PostService;

trait HasMetafields
{
    protected ?FieldCollection $cachedFieldDefinitions = null;

    protected ?array $cachedMetafields = null;

    protected ?array $cachedMetafieldValues = null;

    protected static function bootHasMetafields(): void
    {
        static::deleting(function (self $model) {
            $model->metafields()->cursor()->each(fn (Metafield $metafield) => $metafield->delete());
        });
    }

    public function metafields(): MorphMany
    {
        return $this->morphMany(Metafield::class, 'metafieldable');
    }

    public function scopeByMetafield(Builder $builder, string $key, mixed $value): Builder
    {
        return $builder->whereHas('metafields', fn ($q) => $q->where('key', $key)->where('value', $value));
    }

    public function metafieldRelations(): array
    {
        return [
            'metafields' => fn ($q) => $q
                ->with([
                    'metafieldable',
                    'media',
                ])
                ->withTranslation()
                ->oldest('key'),
        ];
    }

    public function scopeWithMetafields(Builder $builder): Builder
    {
        return $builder->with($this->metafieldRelations());
    }

    public function loadMetafields(): void
    {
        $this->load($this->metafieldRelations());
    }

    public function getMetafield(string $key): ?Metafield
    {
        /** @var ?Metafield $metafield */
        $metafield = $this->metafields->where('key', $key)->first();

        return $metafield;
    }

    public function getMetafieldValues(
        ?FieldCollection $fieldDefinitions = null,
        ?string $locale = null,
        array $options = []
    ): array {
        if ($this->cachedMetafieldValues) {
            return $this->cachedMetafieldValues;
        }

        $values = [];

        $fieldDefinitions ??= $this->getCachedMetafieldDefinitions();
        $metafields = $this->getCachedMetafields();

        foreach ($metafields as $metafield) {
            if ($fieldDefinitions) {
                $field = $this->findFieldByMetafield($metafield, $fieldDefinitions);
            }

            $values[$metafield->key] = $metafield->toValue(
                $field ?? null,
                locale: $locale,
                options: $options
            );
        }

        return $this->cachedMetafieldValues = Arr::undot($values);
    }

    public function getMetafieldValue(
        string $key,
        ?string $default = null,
        ?string $locale = null,
    ): mixed {
        return data_get($this->getMetafieldValues(locale: $locale), $key, $default);
    }

    /**
     * @throws FileDoesNotExist
     * @throws FileIsTooBig
     */
    public function setMetafield(string $key, mixed $value, ?string $locale = null, array $options = []): ?Metafield
    {
        $metafield = $this->getMetafield($key);

        if ($value instanceof UploadedFile) {
            $uploadedFile = $value;
            $value = true;
        }

        if ($metafield && is_null($value)) {
            $metafield->delete();

            return null;
        }

        $isTranslatable = $options['is_translatable'] ?? false;
        $field = $options['field'] ?? null;

        if (($field['type'] ?? null) === FieldTypeEnum::TEL) {
            $phoneRule = collect($field['rules'])
                ->filter(fn ($rule) => is_string($rule) && str_starts_with($rule, 'phone:'))
                ->first();

            $phoneCountries = str($phoneRule)
                ->after('phone:')
                ->explode(',')
                ->filter()
                ->toArray();

            if (phone($value, $phoneCountries)->isValid()) {
                $value = phone($value, $phoneCountries)->formatE164();
            } else {
                $value = null;
            }
        }

        if (is_null($value)) {
            return null;
        }

        $data = ['original_value' => ! $isTranslatable ? $value : null];

        if ($isTranslatable) {
            $data[$locale ?? app()->getLocale()]['value'] = $value;
        }

        /** @var Metafield $metafield */
        $metafield = $this->metafields()->updateOrCreate(['key' => $key], $data);

        if (isset($uploadedFile)) {
            $metafield->addMedia($uploadedFile)->toMediaCollection(
                ($field['translatable'] ?? false) ? $locale : 'default',
            );
        }

        return $metafield;
    }

    /**
     * @throws FileIsTooBig
     * @throws FileDoesNotExist
     */
    public function setMetafieldWithSchema(
        string|array|Arrayable $key,
        ?FieldValueItem $fieldValue = null,
        ?string $locale = null
    ): array|Metafield|null {
        if ($key instanceof Enumerable) {
            $key = $key->all();
        } elseif ($key instanceof Arrayable) {
            $key = $key->toArray();
        }

        if (is_array($key)) {
            $saved = [];

            foreach ($key as $fKey => $item) {
                $saved[] = $this->setMetafieldWithSchema($fKey, $item, $locale);
            }

            return array_values(array_filter($saved));
        } elseif (is_null($fieldValue)) {
            return null;
        }

        $field = $fieldValue->field();
        $value = $fieldValue->value();

        $key = Str::after($key, 'fields.');

        return $this->setMetafield(
            key: $key,
            value: $value,
            locale: $locale,
            options: [
                'is_translatable' => $field['translatable'],
                'field' => $field,
            ]
        );
    }

    public function deleteMetafields(
        array|string|null $startsWith = null,
        array|string|null $equals = null,
    ): Collection {
        $deleted = collect();

        if (is_string($startsWith)) {
            $startsWith = [$startsWith];
        }

        if (is_string($equals)) {
            $equals = [$equals];
        }

        if ($startsWith) {
            $metafields = $this->metafields()->where(function (Builder $builder) use ($startsWith) {
                foreach ($startsWith as $value) {
                    $builder->orWhere('key', 'like', $value.'%');
                }
            })->get();

            foreach ($metafields as $metafield) {
                $deleted[] = tap($metafield)->delete();
            }
        }

        if ($equals) {
            $metafields = $this->metafields()->whereIn('key', $equals)->get();

            foreach ($metafields as $metafield) {
                $deleted[] = tap($metafield)->delete();
            }
        }

        info(['Deleted', $deleted]);

        return $deleted;
    }

    /**
     * If deleted metafield key contains indexes (e.g. images.0.title)
     * then we need to reset related metafield keys.
     *
     * e.g. images.1.title -> images.0.title
     *     images.2.title -> images.1.title
     *
     * And the key can be nested too. e.g. images.0.descriptions.0.title, images.0.descriptions.1.title
     */
    public function resetMetafieldKeys(): void
    {
        try {
            DB::beginTransaction();

            $metafields = $this->metafields()
                ->get()
                ->mapWithKeys(fn (Metafield $metafield) => [$metafield->key => $metafield])
                ->undot();

            $map = function ($metafield) use (&$map) {
                if (is_array($metafield) && collect($metafield)->keys()->every(fn ($key) => is_numeric($key))) {
                    return collect($metafield)->map($map)
                        ->sortBy(fn ($value, $key) => $key)
                        ->values()
                        ->all();
                }

                if (is_array($metafield)) {
                    return collect($metafield)->map($map)->all();
                }

                return $metafield;
            };

            $metafields = $metafields->map($map)->dot();

            foreach ($metafields as $key => $metafield) {
                if ($key !== $metafield->key) {
                    // Add "fields." prefix to key for avoid key conflicts.
                    $metafield->update(['key' => "fields.{$key}"]);
                }
            }

            foreach ($metafields as $key => $metafield) {
                if ($key !== $metafield->key) {
                    // Remove "fields." prefix from key.
                    $metafield->update(['key' => Str::replaceFirst('fields.', '', $metafield->key)]);
                }
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function reorderMetafields(array $reorderedFields): void
    {
        try {
            DB::beginTransaction();

            // Get reordered field keys from request and remove "fields." prefix.
            $reorderedFieldKeys = collect($reorderedFields)
                ->map(fn ($value, $key) => str_replace('fields.', '', $key))
                ->values()
                ->toArray();

            /**
             * Get metafields with reordered keys.
             *
             * @var Collection<int, Metafield> $metafields
             */
            $metafields = $this->metafields()
                ->whereIn('key', $reorderedFieldKeys)
                ->get();

            // Firstly, we update new keys with "fields." prefix for avoid key conflicts.
            foreach ($reorderedFields as $key => $value) {
                if (! str_starts_with($value, 'fields.')) {
                    $value = "fields.{$value}";
                }

                $key = str_replace('fields.', '', $key);
                $metafields->firstWhere('key', $key)?->updateQuietly(['key' => $value]);
            }

            // Secondly, we remove "fields." prefix from reordered keys.
            foreach ($metafields as $metafield) {
                $metafield->update(['key' => str_replace('fields.', '', $metafield->key)]);
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function findFieldByMetafield(Metafield $metafield, FieldCollection $fieldDefinitions): ?FieldInterface
    {
        if ($metafield->metafieldable instanceof Preference) {
            return $fieldDefinitions->findByName(
                sprintf(
                    'fields.%s->%s',
                    $metafield->metafieldable->getNamespaceAndBag(),
                    $metafield->key
                )
            );
        }

        return $fieldDefinitions->findByName(sprintf('fields.%s', $metafield->key));

    }

    public function getCachedMetafieldDefinitions(): ?FieldCollection
    {
        if ($this->cachedFieldDefinitions) {
            return $this->cachedFieldDefinitions;
        }

        if ($this instanceof Post) {
            /** @var PostService $postService */
            $postService = app(PostService::class);

            return $this->cachedFieldDefinitions = $postService->sections($this, $this->template)->allFields();
        }

        if ($this instanceof Taxonomy) {
            return $this->cachedFieldDefinitions = $this->item->fieldSections()?->allFields();
        }

        return null;
    }

    /**
     * @return array<int, Metafield>
     */
    public function getCachedMetafields(): array
    {
        if ($this->cachedMetafields) {
            return $this->cachedMetafields;
        }

        // TODO: Add ->sortBy('key')->values() if there is an error.
        return $this->cachedMetafields = $this->metafields->all();
    }
}
