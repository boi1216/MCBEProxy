<?php

declare(strict_types=1);

namespace proxy\plugin;

use proxy\Server;

/**
 * Class PluginManager
 * @package proxy\plugin
 */
class PluginManager {

    /** @var Server $server */
    private $server;

    /**
     * PluginManager constructor.
     * @param Server $server
     */
    public function __construct(Server $server) {
        $this->server = $server;
    }

    public function loadPlugins(string $directory) {
        $path = getcwd() . DIRECTORY_SEPARATOR . $directory;
        foreach (glob($path . DIRECTORY_SEPARATOR . "*") as $pluginFolder) {
            if(file_exists($fileName = $pluginFolder . DIRECTORY_SEPARATOR . "plugin.yml") && is_file($fileName)) {
                $this->loadPlugin($pluginFolder);
            }
        }
    }

    private function loadPlugin(string $pluginFolder) {

    }

    /**
     * @return Server
     */
    public function getServer(): Server {
        return $this->server;
    }
}