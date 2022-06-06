<?php

namespace Zipferot3000\LaravelFilepondAdapter;

use Illuminate\Support\ServiceProvider;

class FPAdapterServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->publishes([
            __DIR__.'/fp_adapter.php' => config_path('fp_adapter.php'),
        ]);

        if ($this->app->runningInConsole()) {
            $this->commands([
                ClearTemporaryFiles::class,
            ]);
        }
    }

    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/fp_adapter.php', 'fp_adapter');
        $this->app->config['filesystems.disks.temporary'] = [
            'driver' => 'local',
            'root' => storage_path('app/temporary/fp_adapter'),
        ];
    }
}