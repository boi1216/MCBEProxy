<?php

declare(strict_types=1);

namespace proxy\plugin;

use proxy\Server;
use proxy\utils\Logger;

/**
 * Class PluginBase
 * @package proxy\plugin
 */
abstract class PluginBase implements PluginInterface {

    /** @var Server $server */
    private $server;

    /** @var string $name */
    private $name;

    /** @var string $version */
    private $version;

    /** @var string $author */
    private $author;

    /** @var string $description */
    private $description;

    /** @var Logger $logger */
    private $logger;

    /**
     * PluginBase constructor.
     * @param Server $server
     * @param string $name
     * @param string $version
     * @param string $author
     * @param string $description
     */
    public final function __construct(Server $server, string $name, string $version, string $author, string $description) {
        $this->name = $name;
        $this->version = $version;
        $this->author = $author;
        $this->description = $description;
        $this->server = $server;
        $this->logger = new PluginLogger($this->getName());


    }

    /**
     * @return string
     */
    public function getName(): string {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getVersion(): string {
        return $this->version;
    }

    /**
     * @param string $author
     */
    public function setAuthor(string $author): void {
        $this->author = $author;
    }

    /**
     * @return string
     */
    public function getDescription(): string {
        return $this->description;
    }

    /**
     * @return Server
     */
    public function getServer(): Server {
        return $this->server;
    }

    /**
     * @return Logger
     */
    public function getLogger(): Logger {
        return $this->logger;
    }
}