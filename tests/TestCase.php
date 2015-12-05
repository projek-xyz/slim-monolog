<?php
namespace Projek\Slim\Tests;

use Projek\Slim\Monolog;
use PHPUnit_Framework_TestCase;
use DateTimeZone;

abstract class TestCase extends PHPUnit_Framework_TestCase
{
    /**
     * @var Projek\Slim\Monolog
     */
    protected $logger;

    /**
     * Slim Application settings
     *
     * @var array
     */
    protected $settings = [
        'basename' => 'slim-monolog-app',
        'logger' => [
            'directory' => '',
            'filename' => 'app',
            'level' => '',
            'handlers' => [],
        ],
    ];

    public function setUp()
    {
        $this->settings['logger']['directory'] = __DIR__.'/logs';
        $this->logger = new Monolog($this->settings['basename'], $this->settings['logger']);
    }
}
