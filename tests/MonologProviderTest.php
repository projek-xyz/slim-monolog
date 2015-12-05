<?php
namespace Projek\Slim\Tests;

use Slim\App;
use Projek\Slim\MonologProvider;

class MonologProviderTest extends TestCase
{
    public function testAddContainer()
    {
        $app = new App(['settings' => $this->settings]);
        $container = $app->getContainer();
        $container->register(new MonologProvider);

        $this->assertTrue($container->has('logger'));
        $this->assertInstanceOf('Monolog\Logger', $container->get('logger')->getMonolog());
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testInvalidLoggerSettings()
    {
        unset($this->settings['logger']);
        $app = new App(['settings' => $this->settings]);
        $container = $app->getContainer();
        $container->register(new MonologProvider);
    }
}
