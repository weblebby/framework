<?php

namespace Feadmin\Models;

use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Feadmin\Concerns\Eloquent\HasMetafields;
use Feadmin\Concerns\Eloquent\HasOwner;
use Feadmin\Concerns\Eloquent\HasPosition;
use Feadmin\Concerns\Eloquent\HasPost;
use Feadmin\Concerns\Eloquent\Translatable;
use Feadmin\Contracts\Eloquent\PostInterface;
use Feadmin\Enums\HasOwnerEnum;
use Feadmin\Enums\PostStatusEnum;
use Feadmin\Items\Field\FieldItem;
use Feadmin\Items\FieldSectionsItem;
use Feadmin\Items\TaxonomyItem;
use Feadmin\Support\HtmlSanitizer;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Image\Exceptions\InvalidManipulation;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Post extends Model implements HasMedia, PostInterface, TranslatableContract
{
    use HasFactory,
        HasMetafields,
        HasOwner,
        HasPosition,
        HasPost,
        InteractsWithMedia,
        Translatable;

    protected $table = 'posts';

    public $translationForeignKey = 'post_id';

    protected $fillable = [
        'status',
        'template',
        'position',
    ];

    public $translatedAttributes = [
        'title',
        'slug',
        'content',
    ];

    protected $casts = [
        'status' => PostStatusEnum::class,
        'published_at' => 'datetime',
    ];

    public array $userTouches = [
        HasOwnerEnum::CREATED_BY,
        HasOwnerEnum::UPDATED_BY,
    ];

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (Post $post) {
            if ($post->status === PostStatusEnum::PUBLISHED && is_null($post->published_at)) {
                $post->published_at = now();
            }

            if (is_null($post->type)) {
                $post->type = static::class;
            }
        });
    }

    protected static function booted(): void
    {
        static::addGlobalScope('type', function (Builder $builder) {
            $builder->where('type', static::class);
        });
    }

    public function resolveRouteBinding($value, $field = null): Model
    {
        $isTranslatableField = is_string($field) && $this->isTranslationAttribute($field);
        $column = $field ?? $this->getRouteKeyName();

        $post = $this->newQueryWithoutScopes()
            ->select('id', 'type')
            ->when(! $isTranslatableField, fn ($q) => $q->where($column, $value))
            ->when($isTranslatableField, fn ($q) => $q->whereTranslation($column, $value))
            ->firstOrFail();

        abort_unless(class_exists($post->type), 404);

        if ($isTranslatableField) {
            $firstBindingKey = head(array_keys(request()->route()->bindingFields()));
            $route = $this->translatedRoute($post, $value, $column, $firstBindingKey);

            abort_if($route, redirect()->to($route));
        }

        return $post->type::query()
            ->when(! $isTranslatableField, fn ($q) => $q->where($column, $value))
            ->when($isTranslatableField, fn ($q) => $q->whereTranslation($column, $value))
            ->firstOrFail();
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('featured')->singleFile();
    }

    /**
     * @throws InvalidManipulation
     */
    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('lg')->width(1920)->height(1080);
        $this->addMediaConversion('sm')->width(400)->height(225);
    }

    public function getMaxPosition(): int
    {
        return (int) static::query()->max('position');
    }

    public static function getSingularName(): string
    {
        return __('Yazı');
    }

    public static function getPluralName(): string
    {
        return __('Yazılar');
    }

    public static function getTaxonomies(): array
    {
        return [
            TaxonomyItem::make('post_category')
                ->withSingularName(__('Yazı kategorisi'))
                ->withPluralName(__('Yazı kategorileri'))
                ->withFieldSections(
                    FieldSectionsItem::make()
                        ->add('default', 'Genel', [
                            FieldItem::richText('description')
                                ->translatable()
                                ->label(__('Açıklama'))
                                ->hint(__('Kategori açıklaması'))
                                ->rules(['nullable', 'string', 'max:50000']),

                            FieldItem::image('image')
                                ->label(__('Resim'))
                                ->hint(__('Kategori resmi'))
                                ->rules(['nullable', 'image']),
                        ])
                ),

            TaxonomyItem::make('post_tag')
                ->withSingularName(__('Yazı etiketi'))
                ->withPluralName(__('Yazı etiketleri'))
                ->withFieldSections(
                    FieldSectionsItem::make()
                        ->add('default', 'Genel', [
                            FieldItem::richText('description')
                                ->translatable()
                                ->label(__('Açıklama'))
                                ->hint(__('Kategori açıklaması')),
                        ])
                ),
        ];
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(static::class);
    }

    public function scopeTyped(Builder $query, string $type): Builder
    {
        return $query->where('type', $type);
    }

    protected function htmlContent(): Attribute
    {
        return Attribute::get(fn () => app(HtmlSanitizer::class)->sanitizeToHtml($this->content));
    }
}
