# Monolog Logger Integration for Slim micro framework 3

[![LICENSE](https://img.shields.io/packagist/l/projek-xyz/slim-monolog.svg?style=flat-square)](LICENSE.md)
[![VERSION](https://img.shields.io/packagist/v/projek-xyz/slim-monolog.svg?style=flat-square)](https://github.com/projek-xyz/slim-monolog/releases)
[![Build Status](https://img.shields.io/travis/projek-xyz/slim-monolog/master.svg?branch=master&style=flat-square)](https://travis-ci.org/projek-xyz/slim-monolog)
[![Coveralls](https://img.shields.io/coveralls/projek-xyz/slim-monolog/master.svg?style=flat-square)](https://coveralls.io/github/projek-xyz/slim-monolog)
[![Code Climate](https://img.shields.io/codeclimate/coverage/projek-xyz/slim-monolog.svg?style=flat-square)](https://codeclimate.com/coverage/projek-xyz/slim-monolog)
[![Code Climate](https://img.shields.io/codeclimate/github/projek-xyz/slim-monolog.svg?style=flat-square)](https://codeclimate.com/github/projek-xyz/slim-monolog)
[![SensioLabs Insight](https://img.shields.io/sensiolabs/i/26316c51-2637-473f-81bb-17af361f4b65.svg?style=flat-square)](https://insight.sensiolabs.com/projects/26316c51-2637-473f-81bb-17af361f4b65)

Write log file on your Slim 3 application with Monolog logger.

## Install

Via [Composer](https://getcomposer.org/)

```bash
$ composer require projek-xyz/slim-monolog --prefer-dist
```

Requires Slim micro framework 3 and PHP 5.5.0 or newer.

## Usage

```php
// Create Slim app
$app = new \Slim\App();

// Fetch DI Container
$container = $app->getContainer();

// Register Monolog helper:
// Option 1, using MonologProvider
$container->register(new \Projek\Slim\MonologProvider);

// Option 2, using Closure
$container['logger'] = function ($c) {
    $settings = [
        // Path to log directory
        'directory' => 'path/to/logs',
        // Log file name
        'filename' => 'my-app.log',
        // Your timezone
        'timezone' => 'Asia/Jakarta',
        // Log level
        'level' => 'debug',
        // List of Monolog Handlers you wanna use
        'handlers' => [],
    ];

    return new \Projek\Slim\Monolog('slim-app', $settings);
};

// Define a log middleware
$app->add(function ($req, $res, $next) {
    $return = $next($req, $res);

    $this->logger->info('Something happen');

    return $return;
});

// Run app
$app->run();
```

**NOTE**:
- If you are using _option 1_ please make sure you already have `$container['settings']['logger']` in your configuration file.
- `$settings['filename']` only required if you have `$settings['directory']`
- Set `$settings['directory']` to `syslog` to use System Log.

## Contributing

Please see [CONTRIBUTING](.github/CONTRIBUTING.md) and [CONDUCT](.github/CONDUCT.md) for details.

## License

This library is open-sourced software licensed under [MIT license](LICENSE.md).
