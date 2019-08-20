<?php

declare(strict_types=1);

namespace proxy\scheduler;

use proxy\Server;

/**
 * Class Task
 * @package proxy\scheduler
 */
abstract class Task {

    /** @var int $period */
    private $period = null;

    public function __construct(int $period) {
        $this->period = $period;
    }

    /**
     * @return void
     */
    abstract public function onRun(): void;



    /**
     * @return int
     */
    public function getPeriod(): int {
        return $this->period;
    }
}