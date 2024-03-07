<?php

namespace App\Providers;

use App\Services\AI\GoogleAIService;
use App\Services\AI\LLMService;
use App\Services\AI\LocalAIService;
use App\Services\AI\OpenAIService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        match (config('services.ai.use_service')) {
            'google' => $this->app->bind( LLMService::class, GoogleAIService::class),
            'local' => $this->app->bind( LLMService::class, LocalAIService::class),
            'openai' => $this->app->bind( LLMService::class, OpenAIService::class),
            default => $this->app->bind( LLMService::class, GoogleAIService::class),
        };
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Model::preventLazyLoading(! $this->app->isProduction());
        Model::preventSilentlyDiscardingAttributes(! $this->app->isProduction());
    }
}
