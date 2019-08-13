<?php

declare(strict_types=1);

namespace proxy\utils;

/**
 * Class Logger
 * @package proxy\utils
 */
class Logger {

    /** @var string $threadName */
    private $threadName;

    /**
     * Logger constructor.
     * @param string $threadName
     */
    public function __construct(string $threadName) {
        $this->threadName = $threadName;
    }

    /**
     * @param string $message
     */
    public function info(string $message) {
        echo $this->getPrefix() . $message . "\n";
    }

    /**
     * @return string
     */
    private function getPrefix() {
        return "[" . gmdate("H:i:s") . "] [$this->threadName] ";
    }
}