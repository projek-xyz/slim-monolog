# [Unofficial] Slim Framework 3.x Monolog Logger

[![Build Status](https://img.shields.io/travis/projek-xyz/slim-monolog/master.svg?style=flat-square)](https://travis-ci.org/projek-xyz/slim-monolog)
[![LICENSE](https://img.shields.io/packagist/l/projek-xyz/slim-monolog.svg?style=flat-square)](https://packagist.org/packages/projek-xyz/slim-monolog)
[![VERSION](https://img.shields.io/packagist/v/projek-xyz/slim-monolog.svg?style=flat-square)](https://packagist.org/packages/projek-xyz/slim-monolog)
[![Coveralls](https://img.shields.io/coveralls/projek-xyz/slim-monolog/master.svg?style=flat-square)](https://coveralls.io/github/projek-xyz/slim-monolog)
[![SensioLabs Insight](https://img.shields.io/sensiolabs/i/26316c51-2637-473f-81bb-17af361f4b65.svg?style=flat-square)](https://insight.sensiolabs.com/projects/26316c51-2637-473f-81bb-17af361f4b65)

This is a Slim Framework 3.x component helper built on top of the Monolog Logger.

## Install

Via [Composer](https://getcomposer.org/)

```bash
$ composer require projek-xyz/slim-monolog --prefer-dist
```

Requires Slim Framework 3 and PHP 5.5.0 or newer.

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
        'directory' => 'path/to/logs',  // Path to log directory
        'filename' => 'my-app.log',     // Log file name
        'timezone' => 'Asia/Jakarta',   // Your timezone
        'level' => 'DEBUG',             // Log level
        'handlers' => [],               // List of Monolog Handler you wanna use
    ];

    return new \Projek\Slim\Monolog($settings);
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
- if you are using _option 1_ please make sure you already have `$container['settings']['logger']` in your configuration file.
- `$settings['filename']` only required if you have `$settings['directory']`

## Testing

```bash
phpunit
```

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Credits

- [Fery Wardiyanto](http://feryardiant.me)
- [Slim Framework](http://www.slimframework.com)
- [Monolog](https://github.com/Seldaek/monolog)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.