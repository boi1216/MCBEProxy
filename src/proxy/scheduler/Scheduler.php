<?php

declare(strict_types=1);

namespace proxy\scheduler;

use proxy\Server;

/**
 * Class Scheduler
 * @package proxy\scheduler
 */
class Scheduler {

    /** @var Server $server */
    private $server;

    /** @var int $lastTick */
    private $lastTick = 0;

    /** @var int $scheduledTaskCount */
    private $scheduledTaskCount = 0;

    /** @var Task[] $repeatingTasks */
    private $repeatingTasks = [];

    /**
     * Scheduler constructor.
     * @param Server $server
     */
    public function __construct(Server $server) {
        $this->server = $server;
    }

    /**
     * @param Task $task
     */
    public function scheduleRepeatingTask(Task $task) {
        $this->repeatingTasks[++$this->scheduledTaskCount] = $task;
    }

    public function tick() {
        if(microtime(true) - Server::$startTime >= $this->lastTick / 20) {
            foreach ($this->repeatingTasks as $id => $task) {
                if($this->lastTick % $task->getPeriod() === 0){
                    $task->onRun();
                }
            }

            $this->lastTick++;
        }
    }

    /**
     * @return Server
     */
    public function getServer(): Server {
        return $this->server;
    }
}