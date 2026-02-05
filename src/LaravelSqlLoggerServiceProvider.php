<?php

namespace HesamRad\LaravelSqlLogger;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Foundation\Http\Events\RequestHandled;
use Illuminate\Support\Facades\Event;
use Psr\Log\LoggerInterface;

class LaravelSqlLoggerServiceProvider extends ServiceProvider
{
    /**
     * Track if listeners are already attached.
     *
     * This is used to prevent multiple attachments
     * to prevent memory leak.
     */
    private static $attached = false;

    public function boot(): void
    {
        if (!$this->isAllowedToLogSqlQueries()) {
            return;
        }

        $this->logSqlQueries();
    }

    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/sql-logger.php', 'sql_logger');

        $this->publishes([
            __DIR__ . '/../config/' => config_path(),
        ], 'sql_logger');
    }

    private function isAllowedToLogSqlQueries(): bool
    {
        if ($this->app->runningInConsole()) {
            return false;
        }

        if (config('app.debug') === false) {
            return false;
        }

        if (in_array(config('app.env'), config('sql_logger.allowed_envs', []), true) === false) {
            return false;
        }

        return true;
    }

    private function logSqlQueries(): void
    {
        if (self::$attached) {
            return;
        }

        self::$attached = true;
        $sqlLogger = $this->sqlLogger();
        $separatorLogger = $this->separatorLogger();

        DB::listen(function ($query) use ($sqlLogger) {
            $sqlLogger->info("[{$query->time}ms] {$query->sql}", $query->bindings);
        });

        Event::listen(RequestHandled::class, function () use ($separatorLogger) {
            $separatorLogger->info(str_repeat('-', 60));
        });
    }

    private function sqlLogger(): LoggerInterface
    {
        return Log::build([
            'driver' => 'single',
            'path' => config('sql_logger.file_path'),
            'locking' => config('sql-logger.file_lock'),
            'formatter' => \Monolog\Formatter\LineFormatter::class,
            'formatter_with' => [
                'format' => "[%datetime%] %message% %context%\n",
                'date_format' => 'Y-m-d H:i:s',
                'allowInlineLineBreaks' => true,
                'ignoreEmptyContextAndExtra' => true,
            ],
        ]);
    }

    private function separatorLogger(): LoggerInterface
    {
        return Log::build([
            'driver' => 'single',
            'path' => config('sql_logger.file_path'),
            'locking' => config('sql-logger.file_lock'),
            'formatter' => \Monolog\Formatter\LineFormatter::class,
            'formatter_with' => [
                'format' => "%message%\n",
                'ignoreEmptyContextAndExtra' => true,
            ],
        ]);
    }
}
