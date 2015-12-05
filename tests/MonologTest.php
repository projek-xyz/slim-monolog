<?php
namespace Projek\Slim\Tests;

use Monolog\Logger;
use Monolog\Handler;

class MonologTest extends TestCase
{
    public function testShoudReturnsMonologLogger()
    {
        $this->assertInstanceOf('Monolog\Logger', $this->logger->getMonolog());
    }

    public function testShoudHasDefaultHandlers()
    {
        $monolog = $this->logger->getMonolog();
        $this->assertCount(1, $monolog->getHandlers());
    }

    public function testShoudPushHandler()
    {
        $path = $this->settings['logger']['directory'];
        $monolog = $this->logger->pushHandler(
            new Handler\StreamHandler($path, Logger::toMonologLevel('debug'))
        );
        $this->assertCount(2, $monolog->getHandlers());
    }

    public function testShoudPopHandler()
    {
        $path = $this->settings['logger']['directory'];
        $monolog = $this->logger->pushHandler(
            new Handler\StreamHandler($path, Logger::toMonologLevel('debug'))
        );
        $this->logger->popHandler();

        $this->assertCount(1, $monolog->getHandlers());
    }

    public function testShoudPushProcessor()
    {
        $monolog = $this->logger->pushProcessor(function (array $record) {
            return $record;
        });
        $this->assertCount(1, $monolog->getProcessors());
    }

    public function testShoudPopProcessor()
    {
        $monolog = $this->logger->pushProcessor(function (array $record) {
            return $record;
        });
        $this->logger->popProcessor();

        $this->assertCount(0, $monolog->getProcessors());
    }

    public function testShouldUseSyslogBySettings()
    {
        $settings = ['directory' => 'syslog'];
        $logger = $this->getMockBuilder('Projek\Slim\Monolog')
            ->disableOriginalConstructor()
            ->getMock();

        $monolog = $this->getMockBuilder('Monolog\Logger')
            ->setConstructorArgs([$this->settings['basename']])
            ->getMock();

        $logger->expects($this->once())
            ->method('useSyslog')
            ->willReturn($monolog);

        $mock = new \ReflectionClass('Projek\Slim\Monolog');
        $cons = $mock->getConstructor();
        $cons->invokeArgs($logger, [$this->settings['basename'], $settings]);
    }

    public function testShouldUseRollingFileWhenDirExists()
    {
        $settings = ['directory' => __DIR__.'/logs'];
        $logger = $this->getMockBuilder('Projek\Slim\Monolog')
            ->disableOriginalConstructor()
            ->getMock();

        $monolog = $this->getMockBuilder('Monolog\Logger')
            ->setConstructorArgs([$this->settings['basename']])
            ->getMock();

        $logger->expects($this->once())
            ->method('useRotatingFiles')
            ->willReturn($monolog);

        $mock = new \ReflectionClass('Projek\Slim\Monolog');
        $cons = $mock->getConstructor();
        $cons->invokeArgs($logger, [$this->settings['basename'], $settings]);
    }
}
