<?php

namespace App\Providers;

use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        // Deteksi jika folder parent adalah public_html (lingkungan cPanel)
        $parentDir = dirname(base_path());
        if (basename($parentDir) === 'public_html') {
            $this->app->bind('path.public', function() use ($parentDir) {
                return $parentDir;
            });
        }
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        if(env(key:'APP_ENV') !== 'local'){
            URL::forceScheme( scheme: 'https' );
        }
    }
}
