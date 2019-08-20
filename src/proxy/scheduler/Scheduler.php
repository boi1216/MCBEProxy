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

    /** @var float $lastTick */
    private $lastTick = null;

    /** @var int $currentTick */
    private $currentTick = 1;

    /** @var int $scheduledTaskCount */
    private $scheduledTaskCount = 0;

    /** @var array $repeatingTasks */
    private $repeatingTasks = [];

    /** @var Task[] $tasks */
    private $tasks = [];

    /**
     * Scheduler constructor.
     * @param Server $server
     */
    public function __construct(Server $server) {
        $this->server = $server;
        $this->lastTick = microtime(true);
    }

    /**
     * @param Task $task
     */
    public function scheduleTask(Task $task) {
        $this->tasks[++$this->scheduledTaskCount] = $task->schedule($this->getServer(), $this->scheduledTaskCount);
    }

    /**
     * @param Task $task
     * @param int $period
     */
    public function scheduleRepeatingTask(Task $task, int $period = 20) {
        $this->repeatingTasks[++$this->scheduledTaskCount] = $task->schedule($this->getServer(), $this->scheduledTaskCount, $period);
    }

    public function tick() {
        if(microtime(true)-$this->lastTick >= 0.05) {
            foreach ($this->tasks as $index => $task) {
                $task->onRun();
                unset($this->tasks[$index]);
            }
            foreach ($this->repeatingTasks as $index => $task) {
                if($this->currentTick % $task->getPeriod() == 0) {
                    $task->onRun();
                }
            }

            $this->lastTick = microtime(true);
            $this->currentTick++;

            if($this->currentTick == 20) {
                $this->currentTick = 1;
            }
        }
    }

    /**
     * @return Server
     */
    public function getServer(): Server {
        return $this->server;
    }
}