<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Allowed Environments for SQL Logging
    |--------------------------------------------------------------------------
    |
    | This option controls which application environments should have SQL
    | query logging enabled. SQL logging will ONLY work when APP_DEBUG=true
    | regardless of this setting. You can add custom environments of your own.
    |
    | (Production mode is not added intentionally! Add with caution).
    |
    */

    'allowed_envs' => [
        'local',
        'testing',
        'staging',
    ],

    /*
    |--------------------------------------------------------------------------
    | SQL Log File Path
    |--------------------------------------------------------------------------
    |
    | This option specifies the absolute path where SQL queries will be logged.
    | The directory must exist and be writable by the PHP process.
    |
    | Examples:
    | storage_path('logs/sql.log')
    | storage_path('logs/sql-' . date('Y-m-d') . '.log')
    |
    */

    'file_path' => storage_path('logs/sql.log'),

    /*
    |--------------------------------------------------------------------------
    | File Locking for Concurrent Writes
    |--------------------------------------------------------------------------
    |
    | This option controls whether file locking is used during write operations.
    | Enabling file locking prevents log corruption when multiple PHP processes
    | write to the same log file simultaneously, but adds minimal overhead.
    |
    | Recommended settings:
    | - true: For production, staging, or any multi-process environment
    | - false: For local development or single-process setups for maximum speed
    |
    | Performance impact per query:
    | - With locking: ~0.001ms
    | - Without locking: ~0.0005ms
    |
    */

    'file_lock' => true,
];
