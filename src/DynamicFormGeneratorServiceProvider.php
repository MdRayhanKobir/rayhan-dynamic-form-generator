<?php

namespace Rayhan\DynamicFormGenerator;

use Illuminate\Support\ServiceProvider;

class DynamicFormGeneratorServiceProvider extends ServiceProvider
{
    public function boot()
    {
        \Blade::directive('rayhanDynamicForm', function ($expression) {
            return "<?php echo app('Rayhan\\DynamicFormGenerator\\DynamicFormGenerator', [$expression])->render(); ?>";
        });
    }

    public function register()
    {
        $this->app->bind(DynamicFormGenerator::class, function ($app, $params) {
            return new DynamicFormGenerator($params[0] ?? []);
        });
    }
}