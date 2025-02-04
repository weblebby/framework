<?php

namespace Weblebby\Framework\Facades;

use Illuminate\Support\Facades\Facade;
use Weblebby\Framework\Items\Field\Collections\FieldCollection;
use Weblebby\Framework\Items\Field\Contracts\FieldInterface;
use Weblebby\Framework\Managers\PreferenceManager;

/**
 * @method static PreferenceManager loadPreferences()
 * @method static PreferenceManager create(string $namespace, string $bag)
 * @method static PreferenceManager withNamespace(string $namespace)
 * @method static PreferenceManager withBag(string $bag)
 * @method static array|null namespaces(string $namespace = null)
 * @method static PreferenceManager add(FieldInterface $field)
 * @method static PreferenceManager addMany(array $fields)
 * @method static mixed get(string $rawKey, mixed $default = null, string $locale = null)
 * @method static array set(array $data, string $locale = null, array $options = [])
 * @method static array find(string $rawKey)
 * @method static FieldInterface|null field(string $namespace, string $bag, string $key)
 * @method static FieldCollection allFieldsIn(string $namespace)
 * @method static FieldCollection fields(string $namespace, string $bag)
 *
 * @see PreferenceManager
 */
class Preference extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return PreferenceManager::class;
    }
}
