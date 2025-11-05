<?php

namespace phpCollab;

use Exception;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Monolog\Processor\IntrospectionProcessor;

/**
 * Factory for creating Logger instances
 */
class LoggerFactory
{
    /**
     * Create a Logger instance
     *
     * @param string $logPath Path to the log file
     * @param int $level Log level (default: 400 = ERROR)
     * @return Logger
     */
    public function create(string $logPath, int $level = 400): Logger
    {
        try {
            $stream = new StreamHandler($logPath, $level);
            $logger = new Logger('phpCollab');
            $logger->pushHandler($stream);
            $logger->pushProcessor(new IntrospectionProcessor());

            return $logger;
        } catch (Exception $e) {
            error_log('Logger creation error: ' . $e->getMessage());
            // Return a basic logger as fallback
            $logger = new Logger('phpCollab');
            return $logger;
        }
    }
}
