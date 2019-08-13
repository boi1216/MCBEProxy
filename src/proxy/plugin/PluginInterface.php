<?php

declare(strict_types=1);

namespace proxy\plugin;

/**
 * Interface PluginInterface
 * @package proxy\plugin
 */
interface PluginInterface {

    public function onEnable();

    public function onDisable();
}