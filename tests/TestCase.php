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
            'directory' => __DIR__.'/logs',
            'filename' => 'app',
            'level' => '',
            'handlers' => [],
        ],
    ];

    public function setUp()
    {
        $this->logger = new Monolog($this->settings['basename'], $this->settings['logger']);
    }
}
