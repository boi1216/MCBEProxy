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
        echo $this->getPrefix("Info") . $message . "\n";
    }

    /**
     * @param string $message
     */
    public function error(string $message) {
        echo $this->getPrefix("Error") . $message . "\n";
    }

    /**
     * @param string $notice
     * @return string
     */
    protected function getPrefix(string $notice): string {
        return "[" . gmdate("H:i:s") . "] [$this->threadName] [$notice] ";
    }
}