<?php

namespace Taopix\Core\Logging;

use Psr\Log\LoggerInterface;
use Psr\Log\LoggerTrait;
use \UtilsDebugLogObj as DebugLogger;

class FileLogger implements LoggerInterface
{
    use LoggerTrait;

    public function log($level, $message, array $context = [])
    {
        DebugLogger::log("[" . $level. "] - " . $message);
    }
}