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

    /** @var PluginBase[] $plugins */
    protected $plugins = [];

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

    /**
     * @param string $pluginFolder
     * @return bool
     */
    private function loadPlugin(string $pluginFolder): bool {
        try {
            $yaml = yaml_parse_file($pluginFolder . DIRECTORY_SEPARATOR . "plugin.yml");
        }
        catch (\Exception $exception) {
            $this->getServer()->getLogger()->error("Could not load plugin " . basename($pluginFolder) . ", invalid or corrupted YAML file");
            $this->getServer()->getLogger()->error($exception->getMessage());
            return false;
        }

        $values = ["name", "version", "author", "description", "api-version", "main"];
        foreach ($values as $value) {
            if(!isset($yaml[$value])) {
                $this->getServer()->getLogger()->error("Could not load plugin " . basename($pluginFolder) . ", missing '$value' in plugin description.");
                return false;
            }
        }

        $name = (string)$yaml["name"];

        if(isset($this->plugins[$name])) {
            $this->getServer()->getLogger()->error("Could not load plugin $name, Plugin already exists.");
            return false;
        }

        if($yaml["api-version"] !== "1.0.0") {
            $this->getServer()->getLogger()->error("Could not load plugin $name, Invalid api version.");
            return false;
        }

        $class = DIRECTORY_SEPARATOR . $yaml["main"];
        $classPath = $pluginFolder . DIRECTORY_SEPARATOR . "src" . DIRECTORY_SEPARATOR . $yaml["main"] . ".php";

        if(!is_file($classPath)) {
            $this->getServer()->getLogger()->error("Could not load plugin $name, main class wasn't found.");
            return false;
        }

        try {
            require $classPath;

            /** @var PluginBase $plugin */
            $plugin = new $class($this->getServer(), $name, $yaml["version"], $yaml["author"], $yaml["description"]);
        }
        catch (\Exception $exception) {
            $this->getServer()->getLogger()->error("Could not load plugin $name, {$exception->getMessage()}");
            return false;
        }

        if(!$plugin instanceof PluginBase) {
            $this->getServer()->getLogger()->error("Could not load plugin $name, plugin must extend PluginBase class.");
            return false;
        }

        $this->plugins[$name] = $plugin;
        $this->getServer()->getLogger()->info("Loading plugin {$name}");
        return true;
    }

    public function enablePlugins() {
        foreach ($this->plugins as $name => $plugin) {
            $this->getServer()->getLogger()->info("Enabling plugin {$name}!");
            $plugin->onEnable();
        }
    }

    public function disablePlugins() {
        foreach ($this->plugins as $name => $plugin) {
            $this->getServer()->getLogger()->info("Disabling plugin {$name}!");
            $plugin->onDisable();
            unset($plugin);
        }
    }

    /**
     * @return Server
     */
    public function getServer(): Server {
        return $this->server;
    }
}