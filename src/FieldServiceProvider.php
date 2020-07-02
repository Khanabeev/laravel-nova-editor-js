<?php

namespace Khanabeev\Editor;

use Illuminate\Support\ServiceProvider;
use Laravel\Nova\Events\ServingNova;
use Laravel\Nova\Nova;

class FieldServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Nova::serving(function (ServingNova $event) {
            Nova::script('editor', __DIR__ . '/../dist/js/field.js');
            Nova::style('editor', __DIR__ . '/../dist/css/field.css');
        });

        $this->publishes([
            __DIR__ . '/config/editor.php' => config_path('editor.php'),
        ]);

        /**
         * Controllers
         */
        $this->publishes([
            __DIR__ . '/Controllers/VideoController.php'
            => app_path('Http/Controllers/Editor/VideoController.php'),
        ]);

        $this->publishes([
            __DIR__ . '/Controllers/ImageUploadController.php'
            => app_path('Http/Controllers/Editor/ImageUploadController.php'),
        ]);

        /**
         * Tools
         */
        $this->publishes([
            __DIR__ . '/Helpers/Rutube.php' => app_path('Tools/Editor/Helpers/Rutube.php'),
        ]);

        $this->publishes([
            __DIR__ . '/Helpers/Vimeo.php' => app_path('Tools/Editor/Helpers/Vimeo.php'),
        ]);

        $this->publishes([
            __DIR__ . '/Helpers/Youtube.php' => app_path('Tools/Editor/Helpers/Youtube.php'),
        ]);


        $this->loadRoutesFrom(__DIR__ . '/routes/web.php');

    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {

    }
}
