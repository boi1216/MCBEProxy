<?php

declare(strict_types=1);

namespace proxy\plugin;

use proxy\utils\Logger;

/**
 * Class PluginLogger
 * @package proxy\plugin
 */
class PluginLogger extends Logger {

    /** @var string $pluginName */
    private $pluginName;

    /**
     * PluginLogger constructor.
     * @param string $pluginName
     */
    public function __construct(string $pluginName) {
        $this->pluginName = $pluginName;
        parent::__construct("Main thread"); // Plugins should use logger just on main thread
    }

    /**
     * @param string $notice
     * @return string
     */
    protected function getPrefix(string $notice): string {
        return parent::getPrefix($notice) . "[$this->pluginName] ";
    }
}