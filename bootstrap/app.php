<?php

// Custom error handler to suppress E_DEPRECATED errors during rendering
// This prevents the tempnam() deprecation in PHP 8.3 from breaking error pages
set_error_handler(function (int $errno, string $errstr, string $errfile, int $errline): bool {
    if ($errno === E_DEPRECATED || $errno === E_USER_DEPRECATED) {
        // Silently ignore deprecation warnings - they don't affect functionality
        error_log("Suppressed deprecation: $errstr in $errfile:$errline");
        return true;
    }
    // Let PHP handle other errors normally
    return false;
}, E_DEPRECATED | E_USER_DEPRECATED);

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class,
            'role' => \Spatie\Permission\Middleware\RoleMiddleware::class,
        ]);
    })
    ->withProviders([
        \Illuminate\Auth\AuthServiceProvider::class,
        \Illuminate\Broadcasting\BroadcastServiceProvider::class,
        \Illuminate\Bus\BusServiceProvider::class,
        \Illuminate\Cache\CacheServiceProvider::class,
        \Illuminate\Foundation\Providers\ConsoleSupportServiceProvider::class,
        \Illuminate\Cookie\CookieServiceProvider::class,
        \Illuminate\Database\DatabaseServiceProvider::class,
        \Illuminate\Encryption\EncryptionServiceProvider::class,
        \Illuminate\Filesystem\FilesystemServiceProvider::class,
        \Illuminate\Foundation\Providers\FoundationServiceProvider::class,
        \Illuminate\Hashing\HashServiceProvider::class,
        \Illuminate\Mail\MailServiceProvider::class,
        \Illuminate\Notifications\NotificationServiceProvider::class,
        \Illuminate\Pagination\PaginationServiceProvider::class,
        \Illuminate\Pipeline\PipelineServiceProvider::class,
        \Illuminate\Queue\QueueServiceProvider::class,
        \Illuminate\Redis\RedisServiceProvider::class,
        \Illuminate\Auth\Passwords\PasswordResetServiceProvider::class,
        \Illuminate\Session\SessionServiceProvider::class,
        \Illuminate\Translation\TranslationServiceProvider::class,
        \Illuminate\Validation\ValidationServiceProvider::class,
        \Illuminate\View\ViewServiceProvider::class,
        \Spatie\Permission\PermissionServiceProvider::class,
        \NunoMaduro\Collision\Adapters\Laravel\CollisionServiceProvider::class,
        \App\Providers\AppServiceProvider::class,
    ], withBootstrapProviders: false)
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*'),
        );

        // Register error handler again in exception context
        set_error_handler(function (int $errno, string $errstr, string $errfile, int $errline): bool {
            if ($errno === E_DEPRECATED || $errno === E_USER_DEPRECATED) {
                return true;
            }
            return false;
        }, E_DEPRECATED | E_USER_DEPRECATED);
    })->create();
