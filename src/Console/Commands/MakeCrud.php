<?php

namespace Feadmin\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class MakeCrud extends Command
{
    protected $files;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:crud {panel} {model} {--force}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Make crud for model.';

    public function __construct(Filesystem $files)
    {
        parent::__construct();

        $this->files = $files;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        $this->validatePanel();

        $this->createRequests();
        $this->createModel();
        $this->createController();
        $this->createViews();

        return self::SUCCESS;
    }

    protected function validatePanel(): bool
    {
        $panel = $this->argument('panel');

        if (panel($panel)) {
            return true;
        }

        $this->error('Panel not found.');
        exit(self::FAILURE);
    }

    protected function createRequests(): void
    {
        $model = $this->argument('model');

        $this->call('make:request', ['name' => "Store{$model}Request"]);
        $this->call('make:request', ['name' => "Update{$model}Request"]);
    }

    protected function createController(): void
    {
        $model = $this->argument('model');
        $panel = Str::studly($this->argument('panel'));
        $path = app_path("Http/Controllers/{$panel}/{$model}Controller.php");

        if ($this->files->exists($path) && !$this->option('force')) {
            $this->error("Controller already exists.");
            return;
        }

        $stub = $this->getStub('crud/controller.stub', $this->getStubVariables());

        $this->makeDirectory($path);
        $this->files->put($path, $stub);

        $this->info('Controller created successfully.');
    }

    protected function createModel(): void
    {
        $this->call('make:model', [
            'name' => $this->argument('model'),
            '--force' => $this->option('force'),
            '--migration' => true,
        ]);
    }

    protected function createViews(): void
    {
        $panel = $this->argument('panel');
        $replacements = $this->getStubVariables();

        foreach (['index', 'show', 'create', 'edit'] as $view) {
            $path = resource_path("views/{$panel}/{$replacements['{{ view }}']}/{$view}.blade.php");

            if ($this->files->exists($path) && !$this->option('force')) {
                $this->error("{$view} view already exists.");
                return;
            }

            $stub = $this->getStub("crud/{$view}.blade.stub", $replacements);

            $this->makeDirectory($path);
            $this->files->put($path, $stub);

            $this->info("{$view} view created successfully.");
        }
    }

    protected function getStub(string $path, array $replacements = []): string
    {
        $stub = file_get_contents(__DIR__ . '/../../../stubs/' . $path);

        foreach ($replacements as $search => $replace) {
            $stub = str_replace($search, $replace, $stub);
        }

        return $stub;
    }

    protected function getStubVariables(): array
    {
        $model = $this->argument('model');
        $panel = $this->argument('panel');

        $rootNamespace = $this->laravel->getNamespace();

        return [
            '{{ namespace }}' => "{$rootNamespace}Http\\Controllers\\" . str($panel)->studly()->toString(),
            '{{ namespacedModel }}' => "App\\Models\\{$model}",
            '{{ namespacedStoreRequest }}' => "{$rootNamespace}Http\\Requests\\Store{$model}Request",
            '{{ namespacedUpdateRequest }}' => "{$rootNamespace}Http\\Requests\\Update{$model}Request",
            '{{ rootNamespace }}' => $rootNamespace,
            '{{ class }}' => "{$model}Controller",
            '{{ policy }}' => Str::lower($model),
            '{{ view }}' => str($model)->kebab()->plural()->toString(),
            '{{ model }}' => $model,
            '{{ modelSpace }}' => str($model)->kebab()->slug(' ')->title()->toString(),
            '{{ modelPluralSpace }}' => str($model)->kebab()->slug(' ')->title()->plural()->toString(),
            '{{ storeRequest }}' => "Store{$model}Request",
            '{{ updateRequest }}' => "Update{$model}Request",
            '{{ modelVariable }}' => str($model)->camel()->toString(),
            '{{ modelPluralVariable }}' => str($model)->camel()->plural()->toString(),
            '{{ panel }}' => $panel,
            '{{ route }}' => str($model)->snake()->plural()->toString(),
        ];
    }

    protected function makeDirectory($path): string
    {
        if (!$this->files->isDirectory(dirname($path))) {
            $this->files->makeDirectory(dirname($path), 0777, true, true);
        }

        return $path;
    }
}
