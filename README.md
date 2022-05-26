# Lightning Runtime

A runtime for [Swoole](https://www.swoole.co.uk/) for [Symfony](https://symfony.com/) applications based on the [Php Swoole Runtime](https://github.com/php-runtime/swoole).

If you are new to the Symfony Runtime component, read more in the [main readme](https://github.com/php-runtime/runtime).

**Still in development**

## Installation

```
 composer require lightning/runtime
```

## Usage

Define the environment variable `APP_RUNTIME` for your application.

```
APP_RUNTIME=Runtime\Lightning\Runtime
```

### Symfony

```php
// public/index.php

use App\Kernel;

require_once dirname(__DIR__).'/vendor/autoload_runtime.php';

return function (array $context) {
    return new Kernel($context['APP_ENV'], (bool) $context['APP_DEBUG']);
};
```

## Using Options

You can define some configurations using Symfony's Runtime `APP_RUNTIME_OPTIONS` API.

| Option | Description | Default |
| --- | --- | --- |
| `host` | The host where the server should binds to (precedes `SWOOLE_HOST` environment variable) | `127.0.0.1` |
| `port` | The port where the server should be listing (precedes `SWOOLE_PORT` environment variable) | `8000` |
| `mode` | Swoole's server mode (precedes `SWOOLE_MODE` environment variable) | `SWOOLE_PROCESS` |
| `settings` | All Swoole's server settings ([swoole.co.uk/docs/modules/swoole-server/configuration](https://www.swoole.co.uk/docs/modules/swoole-server/configuration)) | `[]` |

```php
// public/index.php

use App\Kernel;

$_SERVER['APP_RUNTIME_OPTIONS'] = [
    'host' => '0.0.0.0',
    'port' => 9501,
    'mode' => SWOOLE_BASE,
    'settings' => [
        \Swoole\Constant::OPTION_WORKER_NUM => swoole_cpu_num() * 2,
        \Swoole\Constant::OPTION_ENABLE_STATIC_HANDLER => true,
        \Swoole\Constant::OPTION_DOCUMENT_ROOT => dirname(__DIR__).'/public'
    ],
];

require_once dirname(__DIR__).'/vendor/autoload_runtime.php';

return function (array $context) {
    return new Kernel($context['APP_ENV'], (bool) $context['APP_DEBUG']);
};
```