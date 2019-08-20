<?php

declare(strict_types=1);

namespace vixikhd\tasktest;

use proxy\plugin\PluginBase;

/**
 * Class TaskTest
 * @package vixikhd\tasktest
 */
class TaskTest extends PluginBase {

    public function onEnable() {
        require_once "plugins/TaskTest/src/vixikhd/tasktest/BroadcastTask.php";
        $this->getServer()->getScheduler()->scheduleRepeatingTask(new BroadcastTask($this));
    }

    public function onDisable() {
    }
}