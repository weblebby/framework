<?php

namespace Feadmin\Facades;

use Feadmin\Items\Field\Contracts\FieldInterface;
use Feadmin\Managers\PreferenceManager;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Facade;

/**
 * @method static PreferenceManager loadPreferences()
 * @method static PreferenceManager create(string $namespace, string $bag)
 * @method static PreferenceManager withNamespace(string $namespace)
 * @method static PreferenceManager withBag(string $bag)
 * @method static array|null namespaces(string $namespace = null)
 * @method static PreferenceManager add(FieldInterface $field)
 * @method static PreferenceManager addMany(array $fields)
 * @method static mixed get(string $rawKey, mixed $default = null)
 * @method static array set(array $data)
 * @method static array find(string $rawKey)
 * @method static FieldInterface|null field(string $namespace, string $bag, string $key)
 * @method static Collection fields(string $namespace, string $bag)
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
