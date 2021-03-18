<?php

namespace App\Libraries;

use App\Support\Monolog\Formatter\LineFormatter;

use Monolog\Logger as Monolog;
use Monolog\Handler\RotatingFileHandler;

use Psr\Log\AbstractLogger;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;

class BLogger extends AbstractLogger implements LoggerInterface
{
    /**
     * The Log levels.
     *
     * @var array
     */
    private static $levels = [
        'debug' => Monolog::DEBUG,
        'info' => Monolog::INFO,
        'notice' => Monolog::NOTICE,
        'warning' => Monolog::WARNING,
        'error' => Monolog::ERROR,
        'critical' => Monolog::CRITICAL,
        'alert' => Monolog::ALERT,
        'emergency' => Monolog::EMERGENCY,
    ];

    /**
     * 日志存放位置
     *
     * @var string
     */
    private $segs;

    /**
     * @var array
     */
    private $config = [];

    /**
     * @var \Monolog\Logger
     */
    private $logger;

    /**
     * @return \Psr\Log\LoggerInterface
     */
    public static function scope(array $segs)
    {
        $log = new static();
        $log->segs = storage_path('logs' . DIRECTORY_SEPARATOR . implode(DIRECTORY_SEPARATOR, $segs));
        $log->logger = new Monolog(config('app.env'));

        return $log;
    }

    /**
     * Logs with an arbitrary level.
     *
     * @param mixed  $level
     * @param string $message
     * @param array  $context
     *
     * @return void
     *
     * @throws \Psr\Log\InvalidArgumentException
     */
    public function log($level, $message, array $context = array())
    {
        $config = [
            'path' => implode('_', [$this->segs, $level]) . '.log',
            'days' => $this->config['days'] ?? 7,
            'level' => $this->config['level'] ?? LogLevel::DEBUG,
            'bubble' => $this->config['bubble'] ?? true,
            'permission' => $this->config['permission'] ?? null,
            'locking' => $this->config['locking'] ?? false
        ];
        $this->logger->setHandlers([static::handler($config)]);
        $this->logger->log($level, $message, $context);
    }

    /**
     * Get a Monolog formatter instance.
     *
     * @return \Monolog\Handler\RotatingFileHandler
     */
    private static function handler($config)
    {
        $hander = new RotatingFileHandler(
            $config['path'],
            $config['days'] ?? 7,
            static::level($config),
            $config['bubble'] ?? true,
            $config['permission'] ?? null,
            $config['locking'] ?? false
        );

        $hander->setFormatter(static::formatter());
        return $hander;
    }

    /**
     * Get a Monolog formatter instance.
     *
     * @return \Monolog\Formatter\FormatterInterface
     */
    private static function formatter()
    {
        return tap(new LineFormatter(null, null, true, true), function (LineFormatter $formatter) {
            $formatter->includeStacktraces();
            // $formatter->setMaxNormalizeItemCount(20);
        });
    }

    /**
     * Parse the string level into a Monolog constant.
     *
     * @param  array  $config
     * @return int
     *
     * @throws \InvalidArgumentException
     */
    private static function level(array $config)
    {
        $level = $config['level'] ?? 'debug';

        if (isset(static::$levels[$level])) {
            return static::$levels[$level];
        }

        throw new \InvalidArgumentException('Invalid log level.');
    }
}
