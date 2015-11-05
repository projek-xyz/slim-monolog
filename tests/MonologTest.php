<?php
namespace Projek\Slim\Tests;

use Projek\Slim\Monolog;
use PHPUnit_Framework_TestCase;

class MonologTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Projek\Slim\Monolog
     */
    protected $logger;

    public function setUp()
    {
        $this->logger = new Monolog('slim-app-test', [
            'timezone' => new \DateTimeZone('UTC'),
            'handlers' => [],
        ]);
    }

    public function testShoudReturnsPlatesEngine()
    {
        $this->assertInstanceOf('Monolog\Logger', $this->logger->getMonolog());
    }
}
