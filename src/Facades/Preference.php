<?php

namespace Feadmin\Facades;

use Feadmin\Items\PreferenceItem;
use Feadmin\Managers\PreferenceManager;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Facade;

/**
 * @method static self create(string $namespace, string $bag)
 * @method static self withNamespace(string $namespace)
 * @method static self withBag(string $bag)
 * @method static array|null namespaces(string $namespace = null)
 * @method static self add(PreferenceItem|array $field)
 * @method static self addMany(array $fields)
 * @method static mixed get(string $rawKey, mixed $default = null)
 * @method static array set(array $data)
 * @method static array find(string $rawKey)
 * @method static array|bool|null field(string $namespace, string $bag, string $key)
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
