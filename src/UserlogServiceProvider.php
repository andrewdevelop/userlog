<?php 

namespace Andrewdevelop\Userlog;

use Illuminate\Support\ServiceProvider;

class UserlogServiceProvider extends ServiceProvider {

    /**
     * Perform post-registration booting of services.
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'userlog.php' => config_path('userlog.php'),
        ]);
    }

    /**
     * Register.
     * @return void
     */
    public function register()
    {
        // Init configuration
        $this->mergeConfigFrom(__DIR__.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'userlog.php', 'userlog');
        
        // Init event listener
        $this->app->events->subscribe('Andrewdevelop\Userlog\EventListener');

        // Bind our validation service
        # $this->app->singleton('Components\Customer\Contracts\ValidationInterface', 'Components\Customer\Repositories\Validator');

    }


}