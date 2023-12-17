<?php

namespace Feadmin\Concerns\Eloquent;

use Feadmin\Items\Field\FieldValueItem;
use Feadmin\Items\Field\ImageFieldItem;
use Feadmin\Items\Field\TextFieldItem;
use Feadmin\Items\Field\UploadableFieldItem;
use Feadmin\Models\Metafield;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Http\UploadedFile;
use Spatie\MediaLibrary\MediaCollections\Exceptions\FileDoesNotExist;
use Spatie\MediaLibrary\MediaCollections\Exceptions\FileIsTooBig;

trait HasMetafields
{
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
            $field = $fieldDefinitions->firstWhere('key', 'metafields.' . $metafield->key);

            if ($field instanceof TextFieldItem) {
                $values[$metafield->key] = $metafield->value ?? $metafield->original_value;
            }
        }

        return $values;
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

    public function deleteMetafield(string $key): bool
    {
        return $this->metafields()->where('key', $key)->delete();
    }
}
