<?php

namespace Sysborg\ChatGPT\Providers;

use Illuminate\Support\ServiceProvider;
use Sysborg\ChatGPT\ChatGPTClient;

class ChatGPTServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/chatgpt.php',
            'chatgpt'
        );

        $this->app->singleton('chatgpt', function ($app) {
            return new ChatGPTClient($app['config']['chatgpt']);
        });

        $this->app->alias('chatgpt', ChatGPTClient::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../../config/chatgpt.php' => config_path('chatgpt.php'),
            ], 'chatgpt-config');
        }
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides(): array
    {
        return ['chatgpt', ChatGPTClient::class];
    }
}