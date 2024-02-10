<?php

namespace Weblebby\Framework\Models;

use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;
use Spatie\Image\Exceptions\InvalidManipulation;
use Spatie\Image\Manipulations;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Weblebby\Framework\Concerns\Eloquent\HasMetafields;
use Weblebby\Framework\Concerns\Eloquent\HasOwner;
use Weblebby\Framework\Concerns\Eloquent\HasPosition;
use Weblebby\Framework\Concerns\Eloquent\HasPost;
use Weblebby\Framework\Concerns\Eloquent\Translatable;
use Weblebby\Framework\Contracts\Eloquent\PostInterface;
use Weblebby\Framework\Enums\HasOwnerEnum;
use Weblebby\Framework\Enums\PostStatusEnum;
use Weblebby\Framework\Items\Field\FieldItem;
use Weblebby\Framework\Items\FieldSectionsItem;
use Weblebby\Framework\Items\TaxonomyItem;
use Weblebby\Framework\Support\HtmlSanitizer;

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
        $this->addMediaConversion('lg')->fit(Manipulations::FIT_MAX, 1920, 1080);
        $this->addMediaConversion('md')->fit(Manipulations::FIT_MAX, 720, 720);
        $this->addMediaConversion('sm')->fit(Manipulations::FIT_MAX, 360, 360);
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

    public function makeSeo(): void
    {
        seo()->title($this->getMetafieldValue('seo_title') ?? $this->title);
        seo()->description($this->getMetafieldValue('seo_description') ?? Str::limit(strip_tags($this->content), 150));

        if ($this->hasMedia('featured')) {
            seo()->image($this->getFirstMediaUrl('featured', 'md'));
        }
    }
}
