1. Şu komutu yazın.

```$ php artisan feadmin:install```

2. AppServiceProvider içine şunu yazın.

```php
Panel::create('admin')
    ->prefix('admin')
    ->as('admin::')
    ->middleware(['web', 'auth'])
    ->features([
        Features::preferences(),
        Features::users(),
        Features::roles(),
        Features::extensions(),
        Features::navigations(),
    ]);
```

3. RouteServiceProvider içine şunu yazın.

```php
$this->routes(function () {
    Panel::useRoutes();

    // ...
});
```

4. User modelinin extendini değiştirin.

```use Illuminate\Foundation\Auth\User as Authenticatable;``` yerine ```use Feadmin\Models\User as Authenticatable;```

5. Middleware

'web' grubu içerisine \Feadmin\Http\Middleware\Panel::class middleware ını ekleyin.

6. Yetikler

Varsayılan yetkilerin yönetimini panele eklemek isterseniz yeni bir middleware oluşturup bu kodu yazın. İstediğiniz
kısımları "false" yaparak listeden kaldırabilirsiniz.

Önemli: Bu middleware in Panel middleware inden sonra gelmesine dikkat edin.

````
panel()->permission()->defaults(
    preferences: true,
    users: true,
    roles: true,
    extensions: true,
    navigations: true,
);
```
