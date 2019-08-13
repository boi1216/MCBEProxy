<?php

declare(strict_types=1);

namespace proxy\command;

use proxy\ConsoleReader;
use proxy\Server;
use proxy\ThreadManager;

/**
 * Class CommandMap
 * @package proxy\command
 */
class CommandMap {

    /** @var Server $server */
    private $server;

    /** @var string $consoleReaderHash */
    private $consoleReaderHash;

    /** @var string $consoleCommandSender */
    private $consoleCommandSender;

    /** @var Command[] $commands */
    protected $commands = [];

    /**
     * CommandMap constructor.
     * @param Server $server
     */
    public function __construct(Server $server) {
        $this->server = $server;
        $this->consoleReaderHash = ThreadManager::getInstance()->runThread(new ConsoleReader());
        $this->consoleCommandSender = new ConsoleCommandSender($this->getServer()->getLogger());
    }

    /**
     * @param Command $command
     * @param bool $override
     *
     * @return bool
     */
    public function registerCommand(Command $command, bool $override = false): bool {
        if(isset($this->commands[$command->getName()]) && !$override) {
            return false;
        }
        $this->commands[$command->getName()] = $command;
        return true;
    }

    /**
     * @param CommandSender $sender
     * @param string $commandLine
     */
    public function dispatchCommand(CommandSender $sender, string $commandLine) {
        $args = explode(" ", $commandLine);
        $commandName = array_shift($args);

        if($commandLine == "" || !isset($this->commands[$commandName])) {
            $sender->sendMessage("Unknown command.");
            return;
        }

        $this->commands[$commandName]->onExecute($sender, $args);
    }

    public function tick() {
        /** @var ConsoleReader $reader */
        $reader = ThreadManager::getInstance()->getThread($this->consoleReaderHash);
        if(empty($reader->buffer)) {
            return;
        }
        foreach ($reader->buffer as $index => $line) {
            $this->dispatchCommand($this->consoleCommandSender, $line);
            unset($reader->buffer[$index]);
        }
    }

    /**
     * @return Command[]
     */
    public function getCommands(): array {
        return $this->commands;
    }

    /**
     * @return Server
     */
    public function getServer(): Server {
        return $this->server;
    }
}