<?php

declare(strict_types=1);

namespace proxy;

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

        $this->downstreamListener = new DownstreamListener(new DownstreamSocket("0.0.0.0", 19132));
        $this->pluginManager = new PluginManager($this);

        $this->getPluginManager()->loadPlugins("plugins");
        $this->getPluginManager()->enablePlugins();

        while ($this->running) {
            try {
                $this->downstreamListener->tick();
            }
            catch (\Exception $ingore) {}
        }
    }

    public static function getInstance(): Server {
        return self::$instance;
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
}