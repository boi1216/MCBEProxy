<?php

declare(strict_types=1);

namespace proxy\command;

use proxy\utils\Logger;

/**
 * Class ConsoleCommandSender
 * @package proxy\command
 */
class ConsoleCommandSender implements CommandSender {

    /** @var Logger $logger */
    private $logger;

    /**
     * ConsoleCommandSender constructor.
     * @param Logger $logger
     */
    public function __construct(Logger $logger) {
        $this->logger = $logger;
    }

    /**
     * @param string $message
     * @return mixed|void
     */
    public function sendMessage(string $message) {
        $this->logger->info($message);
    }
}