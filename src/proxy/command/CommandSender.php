<?php

declare(strict_types=1);

namespace proxy\command;

/**
 * Interface CommandSender
 * @package proxy\command
 */
interface CommandSender {

    /**
     * @param string $message
     * @return mixed
     */
    public function sendMessage(string $message);
}