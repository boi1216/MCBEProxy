<?php

declare(strict_types=1);

namespace proxy\exampleplugin;

use proxy\plugin\PluginBase;

/**
 * Class ExamplePlugin
 * @package proxy\exampleplugin
 */
class ExamplePlugin extends PluginBase {

    public function onEnable() {
        $this->getLogger()->info("Example plugin loaded!");
    }

    public function onDisable() {

    }
}