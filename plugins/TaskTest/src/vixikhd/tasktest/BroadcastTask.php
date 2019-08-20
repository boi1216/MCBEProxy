<?php

declare(strict_types=1);

namespace vixikhd\tasktest;

use proxy\scheduler\Task;

/**
 * Class BroadcastTask
 * @package vixikhd\tasktest
 */
class BroadcastTask extends Task {

    /** @var TaskTest $plugin */
    public $plugin;

    /**
     * BroadcastTask constructor.
     * @param TaskTest $plugin
     */
    public function __construct(TaskTest $plugin) {
        $this->plugin = $plugin;
        parent::__construct(20 * 60 * 5); // every 5 minutes
    }

    public function onRun(): void {
        $this->plugin->getLogger()->info("You are using broxy by @boi1216 & @viix");
    }
}