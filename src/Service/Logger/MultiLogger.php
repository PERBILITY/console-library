<?php
namespace Perbility\Console\Service\Logger;

use Psr\Log\AbstractLogger;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

/**
 * @author Marc Wörlein <marc.woerlein@perbility.de>
 * @package Perbility\Console\Service\Logger
 */
class MultiLogger extends AbstractLogger
{
    /** @var LoggerInterface[] */
    private $loggers = [];
    
    /**
     * @param LoggerInterface $logger
     */
    public function addLogger(LoggerInterface $logger)
    {
        if ($logger instanceof NullLogger) {
            return;
        }
        $this->loggers[] = $logger;
    }
    
    /**
     * Logs with an arbitrary level.
     *
     * @param mixed $level
     * @param string $message
     * @param array $context
     *
     * @return void
     */
    public function log($level, $message, array $context = array())
    {
        foreach ($this->loggers as $logger) {
            $logger->log($level, $message, $context);
        }
    }
}
