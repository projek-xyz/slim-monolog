<?php
namespace Projek\Slim\Tests;

use Monolog\Logger;
use Monolog\Handler;
use Monolog\Formatter\LineFormatter;
use ReflectionClass;
use ReflectionMethod;
use Projek\Slim\Monolog;

class MonologTest extends TestCase
{
    public function testShoudReturnsMonologLogger()
    {
        $this->assertInstanceOf(Logger::class, $this->logger->getMonolog());
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
        $monolog = $this->getMockBuilder(Monolog::class)
            ->disableOriginalConstructor()
            ->getMock();

        $logger = $this->getMockBuilder(Logger::class)
            ->setConstructorArgs([$this->settings['basename']])
            ->getMock();

        $monolog->expects($this->once())
            ->method('useSyslog')
            ->willReturn($logger);

        $mock = new ReflectionClass(Monolog::class);
        $cons = $mock->getConstructor();
        $cons->invokeArgs($monolog, [$this->settings['basename'], $settings]);
    }

    public function testShouldUseRollingFileWhenDirExists()
    {
        $settings = ['directory' => __DIR__.'/logs'];
        $monolog = $this->getMockBuilder(Monolog::class)
            ->disableOriginalConstructor()
            ->getMock();

        $logger = $this->getMockBuilder(Logger::class)
            ->setConstructorArgs([$this->settings['basename']])
            ->getMock();

        $monolog->expects($this->once())
            ->method('useRotatingFiles')
            ->willReturn($logger);

        $mock = new ReflectionClass(Monolog::class);
        $cons = $mock->getConstructor();
        $cons->invokeArgs($monolog, [$this->settings['basename'], $settings]);
    }

    public function testShouldUseSyslogWhenNeeded()
    {
        extract($this->settings);
        $logger = new Monolog($basename, $logger);
        $logger->useFiles();
        $handler = $logger->getMonolog()->getHandlers();

        $this->assertInstanceOf(Handler\StreamHandler::class, array_shift($handler));
    }

    public function testShouldUseFilesWhenNeeded()
    {
        extract($this->settings);
        $logger = new Monolog($basename, $logger);
        $logger->useSyslog();
        $handler = $logger->getMonolog()->getHandlers();

        $this->assertInstanceOf(Handler\SyslogHandler::class, array_shift($handler));
    }

    public function testShouldUseErrorlogWhenNeeded()
    {
        extract($this->settings);
        $logger = new Monolog($basename, $logger);
        $logger->useErrorLog();
        $handler = $logger->getMonolog()->getHandlers();

        $this->assertInstanceOf(Handler\ErrorLogHandler::class, array_shift($handler));
    }

    public function testShouldWriteLog()
    {
        extract($this->settings);
        $handle = fopen('php://memory', 'a+');
        $logger = new Monolog($basename, $logger);
        $logger->pushHandler(new Handler\StreamHandler($handle));
        $logger->debug('test');
        fseek($handle, 0);
        $out = explode('.', fread($handle, 100));

        $this->assertEquals('DEBUG: test [] []'.PHP_EOL, $out[1]);

        if (file_exists($file = 'debug-'.date('Y-m-d'))) {
            unlink($file);
        }
    }

    public function testFormaterInstance()
    {
        $mock = new ReflectionMethod($this->logger, 'getDefaultFormatter');
        $mock->setAccessible(true);

        $this->assertInstanceOf(LineFormatter::class, $mock->invoke($this->logger));
    }
}
