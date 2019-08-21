<?php

declare(strict_types=1);

namespace proxy;

use proxy\command\CommandMap;
use proxy\network\DownstreamListener;
use proxy\network\DownstreamSocket;
use proxy\plugin\PluginManager;
use proxy\scheduler\Scheduler;
use proxy\utils\Logger;

/**
 * Class Server
 * @package proxy
 */
class Server {

    const VERSION = "1.0";
    const CODENAME = "Nyancat";

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

    /** @var Scheduler $scheduler */
    private $scheduler;

    /** @var bool $downstreamConnected */
    public $downstreamConnected = false;

    /** @var bool $upstreamConnected */
    public $upstreamConnected = false;

    /** @var DownstreamListener $downstreamListener */
    private $downstreamListener;

    /** @var bool $running */
    public $running = true;

    /** @var bool $jwtMode */
    private $useEncryption = false;

    /**
     * Server constructor.
     * @param array $arguments
     * @throws \Exception
     */
    public function __construct(array $arguments){
        self::$startTime = microtime(true);
        self::$instance = $this;

        foreach($arguments as $position => $argument){
            if($position == 0)continue;
            if($argument == "encryption")$this->useEncryption = true;
        }

        $this->logger = new Logger("Main Thread");
        $this->getLogger()->info("Starting proxy server...");

        ThreadManager::init();
        $this->downstreamListener = new DownstreamListener(new DownstreamSocket("0.0.0.0", 19132), $this);
        $this->pluginManager = new PluginManager($this);
        $this->commandMap = new CommandMap($this);
        $this->scheduler = new Scheduler($this);

        $this->getPluginManager()->loadScripts("scripts");
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
                $this->downstreamListener->tick();
                $this->getCommandMap()->tick();
                $this->getScheduler()->tick();
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
     * @return Scheduler
     */
    public function getScheduler(): Scheduler {
        return $this->scheduler;
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

    /**
     * @return bool
     */
    public function encryptionEnabled() : bool{
        return $this->useEncryption;
    }
}