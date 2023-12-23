<?php

namespace Feadmin\Concerns\Eloquent;

use Feadmin\Items\Field\Contracts\UploadableFieldInterface;
use Feadmin\Items\Field\FieldValueItem;
use Feadmin\Items\Field\TextFieldItem;
use Feadmin\Models\Metafield;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Spatie\MediaLibrary\MediaCollections\Exceptions\FileDoesNotExist;
use Spatie\MediaLibrary\MediaCollections\Exceptions\FileIsTooBig;

trait HasMetafields
{
    protected static function bootHasMetafields(): void
    {
        static::deleting(function (self $model) {
            $model->metafields()->cursor()->each(fn(Metafield $metafield) => $metafield->delete());
        });
    }

    public function metafields(): MorphMany
    {
        return $this->morphMany(Metafield::class, 'metafieldable');
    }

    public function scopeByMetafield(Builder $builder, string $key, mixed $value): Builder
    {
        return $builder->whereHas('metafields', fn($q) => $q->where('key', $key)->where('value', $value));
    }

    public function getMetafield(string $key): ?Metafield
    {
        /** @var ?Metafield $metafield */
        $metafield = $this->metafields()->where('key', $key)->first();

        return $metafield;
    }

    public function getMetafieldValues(): array
    {
        $values = [];
        $fieldDefinitions = $this::getPostSections()->withTemplateSections($this, $this->template)->allFields();

        foreach ($this->metafields as $metafield) {
            $field = $fieldDefinitions->findByName(sprintf('metafields.%s', $metafield->key));

            if ($field instanceof UploadableFieldInterface) {
                $values[$metafield->key] = $metafield->getFirstMediaUrl();

                continue;
            }

            if ($field instanceof TextFieldItem) {
                $values[$metafield->key] = $metafield->value ?? $metafield->original_value;

                continue;
            }
        }

        return Arr::undot($values);
    }

    /**
     * @throws FileDoesNotExist
     * @throws FileIsTooBig
     */
    public function setMetafield(string $key, mixed $value, bool $isTranslatable = false, string $locale = null): ?Metafield
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

        if (is_null($value)) {
            return null;
        }

        $data = ['original_value' => !$isTranslatable ? $value : null];

        if ($isTranslatable) {
            $data[$locale ?? app()->getLocale()]['value'] = $value;
        }

        /** @var Metafield $metafield */
        $metafield = $this->metafields()->updateOrCreate(['key' => $key], $data);

        if (isset($uploadedFile)) {
            $metafield->addMedia($uploadedFile)->toMediaCollection();
        }

        return $metafield;
    }

    /**
     * @throws FileIsTooBig
     * @throws FileDoesNotExist
     */
    public function setMetafieldWithSchema(string|array $key, FieldValueItem $fieldValue = null, string $locale = null): array|Metafield|null
    {
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

        return $this->setMetafield(
            key: $key,
            value: $value,
            isTranslatable: $field['translatable'],
            locale: $locale
        );
    }

    public function deleteMetafields(
        array|string $startsWith = null,
        array|string $equals = null,
    ): Collection
    {
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
                    $builder->orWhere('key', 'like', $value . '%');
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

        $this->resetMetafieldKeys();

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
        $metafields = $this->metafields()->get();

        foreach ($metafields as $metafield) {
            $key = $metafield->key;

            if (!Str::contains($key, '.')) {
                continue;
            }

            $keyParts = explode('.', $key);
            $lastKeyPart = array_pop($keyParts);

            if (!is_numeric($lastKeyPart)) {
                continue;
            }

            $keyParts[] = $lastKeyPart - 1;

            $metafield->update(['key' => implode('.', $keyParts)]);
        }
    }
}
