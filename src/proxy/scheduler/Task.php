<?php

declare(strict_types=1);

namespace proxy\scheduler;

use proxy\Server;

/**
 * Class Task
 * @package proxy\scheduler
 */
abstract class Task {

    /** @var Server $server */
    private $server;

    /** @var int $id */
    private $id;

    /** @var int $period */
    private $period = null;

    /**
     * @return void
     */
    abstract public function onRun(): void;

    /**
     * @param Server $server
     * @param int $id
     * @param int|null $period
     *
     * @return Task
     */
    public function schedule(Server $server, int $id, int $period = null): Task {
        $this->server = $server;
        $this->id = $id;
        $this->period = $period;
        return $this;
    }

    /**
     * @return int
     */
    public function getId(): int {
        return $this->id;
    }

    /**
     * @return Server
     */
    public function getServer(): Server {
        return $this->server;
    }

    /**
     * @return int
     */
    public function getPeriod(): int {
        return $this->period;
    }
}