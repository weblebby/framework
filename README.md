1. Şu komutu yazın.

```$ php artisan feadmin:install```

2. AppServiceProvider içine şunu yazın.

```php
Feadmin::create('admin')
    ->prefix('admin')
    ->as('admin::')
    ->middleware(['web', 'auth'])
    ->features([
        Features::translations(),
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
    Feadmin::usePanelRoutes();

    // ...
});
```

4. User modelinin extendini değiştirin.

```use Illuminate\Foundation\Auth\User as Authenticatable;``` yerine ```use Feadmin\Models\User as Authenticatable;```

5. Middleware

'web' grubu içerisine \Feadmin\Http\Middleware\Panel::class middleware ını ekleyin.

6. Eğer eklentileri kullanmak isterseniz.

```
{
  "autoload": {
    "psr-4": {
      "App\\": "app/",
      "Extensions\\": "extensions/"
    }
  }
}
```
Sonrasında ```composer dump-autoload``` yazmayı unutmayın.