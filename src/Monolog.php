<?php
namespace Projek\Slim;

use Monolog\Logger;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\SyslogHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\ErrorLogHandler;
use Monolog\Handler\HandlerInterface;
use Psr\Log\LoggerTrait;

class Monolog
{
    use LoggerTrait;

    private $name;

    /**
     * @var array
     */
    private $settings = [
        'directory' => null,
        'filename' => null,
        'timezone' => null,
        'level' => 'DEBUG',
        'handlers' => [],
    ];

    /**
     * @var \Monolog\Logger
     */
    private $monolog;

    /**
     * Register this plates view provider with a Pimple container
     *
     * @param string $name
     * @param array  $settings
     */
    public function __construct($name = 'slim-app', $settings = [])
    {
        $this->name = $name;
        $this->settings += $settings;
        $this->monolog = new Logger($this->name);

        if (null !== $this->settings['timezone']) {
            if (is_string($this->settings['timezone'])) {
                $this->settings['timezone'] = new \DateTimeZone($this->settings['timezone']);
            }

            Logger::setTimezone($this->settings['timezone']);
        }

        $this->monolog->setHandlers($this->settings['handlers']);

        $levels = array_keys(Logger::getLevels());
        $level = strtoupper($this->settings['level']);
        if (!in_array($level, $levels)) {
            $level = 'debug';
        }

        if ($path = $this->settings['directory']) {
            if ($path === 'syslog') {
                $this->useSyslog($this->name, $level);
            } elseif (is_dir($path)) {
                $path .= '/'.$this->settings['filename'];
                $this->useFiles($path, $level);
            }
        }
    }

    /**
     * Returns Monolog Instance
     *
     * @return \Monolog\Logger
     */
    public function getMonolog()
    {
        return $this->monolog;
    }

    /**
     * Pushes a handler on to the stack.
     *
     * @param  HandlerInterface $handler
     * @return \Monolog\Logger
     */
    public function pushHandler(HandlerInterface $handler)
    {
        return $this->monolog->pushHandler($handler);
    }

    /**
     * Pops a handler from the stack
     *
     * @return Monolog\Handler\HandlerInterface
     */
    public function popHandler()
    {
        return $this->monolog->popHandler();
    }

    /**
     * Adds a processor on to the stack.
     *
     * @param  callable $callback
     * @return \Monolog\Logger
     */
    public function pushProcessor($callback)
    {
        return $this->monolog->pushProcessor($callback);
    }

    /**
     * Removes the processor on top of the stack and returns it.
     *
     * @return callable
     */
    public function popProcessor()
    {
        return $this->monolog->popProcessor();
    }

    /**
     * Adds a log record.
     *
     * @param  integer $level   The logging level
     * @param  string  $message The log message
     * @param  array   $context The log context
     * @return Boolean Whether the record has been processed
     */
    public function log($level, $message, array $context = [])
    {
        return $this->monolog->log($level, $message, $context);
    }

    /**
     * Register a Syslog handler.
     *
     * @param  string $name
     * @param  string $level
     * @return void
     */
    public function useSyslog($name = 'slim-app', $level = 'debug')
    {
        $name or $name = $this->name;
        $this->monolog->pushHandler(new SyslogHandler($name, LOG_USER, $level));
        return $this;
    }

    /**
     * Register an error_log handler.
     *
     * @param  string $level
     * @param  int    $messageType
     * @return void
     */
    public function useErrorLog($level = 'debug', $messageType = ErrorLogHandler::OPERATING_SYSTEM)
    {
        $handler = new ErrorLogHandler($messageType, Logger::toMonologLevel($level));
        $this->monolog->pushHandler($handler);
        $handler->setFormatter($this->getDefaultFormatter());
        return $this;
    }

    /**
     * Register a file log handler.
     *
     * @param  string  $path
     * @param  string  $level
     * @return void
     */
    public function useFiles($path = '', $level = 'debug')
    {
        $path or $path = $this->settings['directory'];
        $handler = new StreamHandler($path, Logger::toMonologLevel($level));
        $this->monolog->pushHandler($handler);
        $handler->setFormatter($this->getDefaultFormatter());

        return $this;
    }

    /**
     * Get a defaut Monolog formatter instance.
     *
     * @return \Monolog\Formatter\LineFormatter
     */
    protected function getDefaultFormatter()
    {
        return new LineFormatter(null, null, true, true);
    }
}