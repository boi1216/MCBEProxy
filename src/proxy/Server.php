<?php

declare(strict_types=1);

namespace proxy;

use proxy\command\CommandMap;
use proxy\network\DownstreamListener;
use proxy\network\DownstreamSocket;
use proxy\plugin\PluginManager;
use proxy\utils\Logger;

/**
 * Class Server
 * @package proxy
 */
class Server {

    /** @var Server $instance */
    private static $instance;

    /** @var int $startTime */
    public static $startTime;

    /** @var Logger $logger */
    private $logger;

    /** @var PluginManager $pluginManager */
    private $pluginManager;

    /** @var CommandMap $commandMap */
    private $commandMap;

    public $downstreamConnected = false;
    public $upstreamConnected = false;

    /** @var DownstreamListener $downstreamListener */
    private $downstreamListener;

    /** @var bool $running */
    public $running = true;

    /** @var int $lastTickTime */
    private $lastTickTime;

    /**
     * Server constructor.
     * @param array $arguments
     * @throws \Exception
     */
    public function __construct(array $arguments){
        self::$startTime = microtime(true);
        self::$instance = $this;
        $this->logger = new Logger("Main Thread");
        $this->getLogger()->info("Starting proxy server...");

        ThreadManager::init();
        $this->downstreamListener = new DownstreamListener(new DownstreamSocket("0.0.0.0", 19132));
        $this->pluginManager = new PluginManager($this);
        $this->commandMap = new CommandMap($this);

        $this->getPluginManager()->loadPlugins("plugins");
        $this->getPluginManager()->enablePlugins();

        $this->tickProcessor();
    }

    public function shutdown() {
        $this->getLogger()->info("Stopping proxy server...");
        $this->getPluginManager()->disablePlugins();
        $this->running = false;
        $this->getLogger()->info("Closing other threads...");
        ThreadManager::getInstance()->closeThreads();
        $this->getLogger()->info("Server closed.");
        exit();
    }


    public function tickProcessor() {
        while ($this->running) {
            try {
                //$this->downstreamListener->tick();
                $this->getCommandMap()->tick();
            }
            catch (\Exception $exception) {
                $this->getLogger()->error($exception->getMessage());
            }
        }
    }


    /**
     * @return CommandMap
     */
    public function getCommandMap(): CommandMap {
        return $this->commandMap;
    }

    /**
     * @return PluginManager
     */
    public function getPluginManager(): PluginManager {
        return $this->pluginManager;
    }

    /**
     * @return Logger
     */
    public function getLogger(): Logger {
        return $this->logger;
    }

    /**
     * @return Server
     */
    public static function getInstance(): Server {
        return self::$instance;
    }
}