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

    public function testShouldConfiguredWithTimezoneString()
    {
        extract($this->settings);
        $settings['timezone'] = 'Asia/Jakarta';
        $monolog = $this->getMockBuilder(Monolog::class)
            ->disableOriginalConstructor()
            ->getMock();

        $mock = new ReflectionClass(Monolog::class);
        $mock->getConstructor()->invokeArgs($monolog, [$basename, $settings]);
        $setting = $mock->getProperty('settings');
        $setting->setAccessible(true);

        $this->assertInstanceOf(\DateTimeZone::class, $setting->getValue($monolog)['timezone']);
    }

    public function testConstructorUsingSyslogHandler()
    {
        $this->settings['logger']['directory'] = 'syslog';

        // Mocking Projek\Slim\Monolog
        $monolog = $this->getMockBuilder(Monolog::class)
            ->disableOriginalConstructor()
            ->getMock();

        // Mocking Monolog\Logger
        $logger = $this->getMockBuilder(Logger::class)
            ->setConstructorArgs([$this->settings['basename']])
            ->getMock();

        // Expect method Projek\Slim\Monolog::useSyslog() will called once
        $monolog->expects($this->once())
            ->method('useSyslog')
            ->willReturn($logger);

        // Invoke Projek\Slim\Monolog::__construct()
        $mock = new ReflectionClass(Monolog::class);
        $mock->getConstructor()->invokeArgs($monolog, [
            $this->settings['basename'],
            $this->settings['logger']
        ]);
    }

    public function testConstructorUsingRotatingFilesHandler()
    {
        // Mocking Projek\Slim\Monolog
        $monolog = $this->getMockBuilder(Monolog::class)
            ->disableOriginalConstructor()
            ->getMock();

        // Mocking Monolog\Logger
        $logger = $this->getMockBuilder(Logger::class)
            ->setConstructorArgs([$this->settings['basename']])
            ->getMock();

        // Expect method Projek\Slim\Monolog::useRotatingFiles() will called once
        $monolog->expects($this->once())
            ->method('useRotatingFiles')
            ->willReturn($logger);

        // Invoke Projek\Slim\Monolog::__construct()
        $mock = new ReflectionClass(Monolog::class);
        $mock->getConstructor()->invokeArgs($monolog, [
            $this->settings['basename'],
            $this->settings['logger']
        ]);
    }

    public function testUsingSyslogHandler()
    {
        $this->settings['logger']['directory'] = 'syslog';

        // Create new Projek\Slim\Monolog::__construct() instance
        $mock = (new ReflectionClass(Monolog::class))->newInstanceArgs([
            $this->settings['basename'],
            $this->settings['logger']
        ]);

        // Expect instance of registered handler
        $handler = $mock->getMonolog()->getHandlers();
        $this->assertCount(1, $handler);
        $this->assertInstanceOf(Handler\SyslogHandler::class, array_shift($handler));
    }

    public function testUsingRotatingFilesHandler()
    {
        // Create new Projek\Slim\Monolog::__construct() instance
        $mock = (new ReflectionClass(Monolog::class))->newInstanceArgs([
            $this->settings['basename'],
            $this->settings['logger']
        ]);

        // Expect instance of registered handler
        $handler = $mock->getMonolog()->getHandlers();
        $this->assertCount(1, $handler);
        $this->assertInstanceOf(Handler\RotatingFileHandler::class, array_shift($handler));

        // Expect create new file in $settings['directory']
        $mock->log('DEBUG', 'coba');
        $logfile = $this->settings['logger']['directory'].'/'.$this->settings['basename'].'-'.date('Y-m-d').'.log';
        $logged = file_exists($logfile);

        $this->assertTrue($logged);

        if ($logged) {
            unlink($logfile);
        }
    }

    public function testUsingFilesHandler()
    {
        $logger = new Monolog(
            $this->settings['basename'],
            $this->settings['logger']
        );

        $logger->popHandler();
        $logger->useFiles();
        $handler = $logger->getMonolog()->getHandlers();

        // Expect instance of registered handler
        $this->assertInstanceOf(Handler\StreamHandler::class, array_shift($handler));

        // Expect create new file in $settings['directory']
        $logger->log('DEBUG', 'coba');
        $logfile = $this->settings['logger']['directory'].'/'.$this->settings['basename'].'.log';
        $logged = file_exists($logfile);

        $this->assertTrue($logged);

        if ($logged) {
            unlink($logfile);
        }
    }

    public function testUsingErrorlogHandler()
    {
        $logger = new Monolog(
            $this->settings['basename'],
            $this->settings['logger']
        );

        $logger->popHandler();
        $logger->useErrorLog();
        $handler = $logger->getMonolog()->getHandlers();

        // Expect instance of registered handler
        $this->assertInstanceOf(Handler\ErrorLogHandler::class, array_shift($handler));
    }

    public function testFormaterInstance()
    {
        $mock = new ReflectionMethod($this->logger, 'getDefaultFormatter');
        $mock->setAccessible(true);

        $this->assertInstanceOf(LineFormatter::class, $mock->invoke($this->logger));
    }
}
