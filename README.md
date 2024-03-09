<img width="1440" alt="weblebby-panel" src="https://github.com/weblebby/framework/assets/18721207/59aae33a-d472-4b85-8feb-bbd7a0738950">

# Important Notes

Weblebby Framework requires PHP 8.2+ and Laravel 10+.

Also, this framework may not work properly when added to existing projects. It is recommended to be used for new
projects.

# Installation

```
composer require weblebby/framework
```

After including the Weblebby Framework in your library, there are a few more things to do.
Let's see what should be done step by step.

# Usage

## AppServiceProvider

Firstly, let's create a new panel in a service provider. **We can use AppServiceProvider for this.**

```php
use App\Http\Middleware\AdminPanel;
use Weblebby\Framework\Facades\Panel;
use Weblebby\Framework\Support\Features;
```

```php
/**
 * Create a new panel named "admin".
 */
Panel::create('admin')
    ->prefix('admin')
    ->as('admin::')
    ->middleware(AdminPanel::class) // We will create this middleware later.
    ->features([
        /**
         * You can delete the features you want.
         */
        Features::users(),
        Features::roles(),
        Features::registration(),
        Features::preferences(),
        Features::navigations(),
        Features::extensions(),
        Features::themes(),
        Features::appearance(),
        Features::setup(),
    ]);

/**
 * Set as main panel.
 */
Panel::setMainPanel('admin');
```

## User model

In the User model, instead of using the default Authenticatable class, we should use the class provided by the Weblebby
Framework.

```php
<?php

// app/Models/User.php

// remove
- use Illuminate\Foundation\Auth\User as Authenticatable;

// add
+ use Weblebby\Framework\Models\User as Authenticatable;
```

After making that change, we need to include the `authorizedPanels()` method in the User model to handle accessibility
management.

```php
public function authorizedPanels(): array|bool
{
    if ($this->hasRole('Super Admin')) {
        // Grant access to all panels.
        return true;
    }
    
    if ($this->hasRole('Reseller')) {
        // Grant access to specific panels.
        return ['reseller'];
    }
    
    // Deny all access.
    return false;
}
```

## Creating a middleware

We must create a middleware for each panel. Let's create for "admin" panel.

```shell
php artisan make:middleware AdminPanel
```

```php
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Weblebby\Framework\Facades\Panel;
use Weblebby\Framework\Items\MenuItem;
use Weblebby\Framework\Support\Features;

class AdminPanel
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        /**
         * We must set "admin" panel as current.
         */
        Panel::setCurrentPanel('admin');

        /**
         * Initialize menu for "admin" panel.
         */
        panel()->menu('sidebar')
            ->withCategory('general')
            ->addMany([
                MenuItem::create(__('Users'))
                    ->withUrl(route('admin::users.index'))
                    ->withActive($request->routeIs('admin::users.*'))
                    ->withIcon('people')
                    ->withAbility([panel()->supports(Features::users()), 'user:read']),

                MenuItem::create(__('Preferences'))
                    ->withUrl(route('admin::preferences.index'))
                    ->withActive($request->routeIs('admin::preferences.*'))
                    ->withIcon('sliders')
                    ->withAbility([
                        panel()->supports(Features::preferences()),
                        fn () => auth()->user()->getAllPermissions()
                            ->pluck('name')
                            ->intersect(panel()->preference('default')->toPermissions())
                            ->isNotEmpty(),
                    ]),
            ]);

        /**
         * You can create menu items for custom categories.
         */
        panel()->menu('sidebar')
            ->withCategory('content', __('Content'))
            ->addMany([
                MenuItem::create(__('Cars'))
                    ->withUrl(route('admin::preferences.index'))
                    ->withActive($request->routeIs('admin::cars.*'))
                    ->withIcon('people')
                    ->withAbility('car:read'),
            ]);

        /**
         * Set preference sections for "admin" panel.
         * 
         * We will set the fields of these bags within a service provider later.
         */
        panel()
            ->preference('default')
            ->withBag('general', __('General settings'))
            ->withBag('email', __('Email settings'));

        /**
         * Set default permissions for "Role Permissions" page.
         */
        panel()->permission()->defaults(
            navigations: true,
            users: true,
            extensions: true,
            appearance: true,
            preferences: true,
            roles: true,
        );

        /**
         * You can create custom permissions.
         */
        panel()->permission()
            ->withGroup('car')
            ->withTitle(__('Cars'))
            ->withPermissions([
                'create' => __('Can create cars'),
                'read' => __('Can view cars'),
                'update' => __('Can edit cars'),
                'delete' => __('Can delete cars'),
            ]);

        return $next($request);
    }
}
```

## Routing

We must include route helpers to RouteServiceProvider.php

```php
use Weblebby\Framework\Facades\Panel;

$this->routes(function () {
    Panel::useRoutes();
    Panel::useFortifyRoutes();

    // ...
});
```

## Preferences

Preferences work like the `config(...)` method in Laravel. But you can update the preferences from the admin panel and
call them with the `preference(...)` method where you want.

In order to manage preferences through the panel, you must first define the fields you want. Let's make an example.

```php
<?php

namespace App\Providers;

use Weblebby\Framework\Facades\Preference;
use Weblebby\Framework\Items\Field\FieldItem;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // ...
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Preference::create('default', 'general')->addMany([
            FieldItem::text('site_name')
                ->label(__('Site name'))
                ->hint(__('Enter your site name.'))
                ->rules(['required', 'string', 'max:191']),
        
            FieldItem::text('site_url')
                ->label(__('Site URL'))
                ->rules(['required', 'string', 'url', 'max:191']),

            
            /**
             * For select field items, the `in` rule is automatically created using the options available.
             * You can also include required or nullable rules if needed.
             */
            FieldItem::select('ssl')
                ->label(__('SSL'))
                ->options([
                    'yes' => __('Yes!'),
                    'no' => __('No'),
                ]),
        
            FieldItem::image('site_logo')
                ->label(__('Site Logo'))
                ->rules(['nullable', 'image', 'max:2048']),
        ]);
    }
}
```

Finally, we run `weblebby:install` to configure the database and create the admin user.

```shell
php artisan weblebby:install
```
