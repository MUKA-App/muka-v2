<?php

use Illuminate\Support\Str;


// locally: use standard settings
$servers = [[
    'host' => env('MEMCACHED_HOST', '127.0.0.1'),
    'port' => env('MEMCACHED_PORT', 11211),
    'weight' => 100,
]];

// on fortrabbit: construct credentials from App secrets
if (getenv('APP_SECRETS')) {
    $secrets = json_decode(file_get_contents(getenv('APP_SECRETS')), true);
    $servers = [[
        'host' => $secrets['MEMCACHE']['HOST1'],
        'port' => $secrets['MEMCACHE']['PORT1'],
        'weight' => 100,
    ]];
    if ($secrets['MEMCACHE']['COUNT'] > 1) {
        $servers []= [
            'host' => $secrets['MEMCACHE']['HOST2'],
            'port' => $secrets['MEMCACHE']['PORT2'],
            'weight' => 100,
        ];
    }
}

if (extension_loaded('memcached')) {
    $timeout_ms = 50;
    $options = [
        // Assure that dead servers are properly removed and ...
        \Memcached::OPT_REMOVE_FAILED_SERVERS => true,

        // ... retried after a short while (here: 2 seconds)
        \Memcached::OPT_RETRY_TIMEOUT         => 2,

        // KETAMA must be enabled so that replication can be used
        \Memcached::OPT_LIBKETAMA_COMPATIBLE  => true,

        // Replicate the data, write it to both memcached servers
        \Memcached::OPT_NUMBER_OF_REPLICAS    => 1,

        // Those values assure that a dead (due to increased latency or
        // really unresponsive) memcached server is dropped fast
        \Memcached::OPT_POLL_TIMEOUT          => $timeout_ms,        // milliseconds
        \Memcached::OPT_SEND_TIMEOUT          => $timeout_ms * 1000, // microseconds
        \Memcached::OPT_RECV_TIMEOUT          => $timeout_ms * 1000, // microseconds
        \Memcached::OPT_CONNECT_TIMEOUT       => $timeout_ms,        // milliseconds

        // Further performance tuning
        \Memcached::OPT_NO_BLOCK              => true,
    ];
}

return [

    /*
    |--------------------------------------------------------------------------
    | Default Cache Store
    |--------------------------------------------------------------------------
    |
    | This option controls the default cache connection that gets used while
    | using this caching library. This connection is used when another is
    | not explicitly specified when executing a given caching function.
    |
    */

    'default' => env('CACHE_DRIVER', 'file'),

    /*
    |--------------------------------------------------------------------------
    | Cache Stores
    |--------------------------------------------------------------------------
    |
    | Here you may define all of the cache "stores" for your application as
    | well as their drivers. You may even define multiple stores for the
    | same cache driver to group types of items stored in your caches.
    |
    | Supported drivers: "apc", "array", "database", "file",
    |            "memcached", "redis", "dynamodb", "null"
    |
    */

    'stores' => [

        'apc' => [
            'driver' => 'apc',
        ],

        'array' => [
            'driver' => 'array',
            'serialize' => false,
        ],

        'database' => [
            'driver' => 'database',
            'table' => 'cache',
            'connection' => null,
            'lock_connection' => null,
        ],

        'file' => [
            'driver' => 'file',
            'path' => storage_path('framework/cache/data'),
        ],

        // other code …
        'memcached' => [
            'driver'        => 'memcached',
            'persistent_id' => env('MEMCACHED_PERSISTENT_ID'),
            'options'       => $options ?? [],
            'servers'       => $servers,
        ],

        'redis' => [
            'driver' => 'redis',
            'connection' => 'cache',
            'lock_connection' => 'default',
        ],

        'dynamodb' => [
            'driver' => 'dynamodb',
            'key' => env('AWS_ACCESS_KEY_ID'),
            'secret' => env('AWS_SECRET_ACCESS_KEY'),
            'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
            'table' => env('DYNAMODB_CACHE_TABLE', 'cache'),
            'endpoint' => env('DYNAMODB_ENDPOINT'),
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Cache Key Prefix
    |--------------------------------------------------------------------------
    |
    | When utilizing a RAM based store such as APC or Memcached, there might
    | be other applications utilizing the same cache. So, we'll specify a
    | value to get prefixed to all our keys so we can avoid collisions.
    |
    */

    'prefix' => env('CACHE_PREFIX', Str::slug(env('APP_NAME', 'laravel'), '_').'_cache'),

];
