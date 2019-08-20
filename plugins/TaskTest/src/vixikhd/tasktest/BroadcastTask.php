<?php

declare(strict_types=1);

namespace vixikhd\tasktest;

use proxy\scheduler\Task;

/**
 * Class BroadcastTask
 * @package vixikhd\tasktest
 */
class BroadcastTask extends Task {

    public function onRun(): void {
        $this->getServer()->getLogger()->info("You are using broxy by @boi1216 & @viix");
    }
}