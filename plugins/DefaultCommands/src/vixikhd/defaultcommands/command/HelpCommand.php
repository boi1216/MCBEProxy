<?php

declare(strict_types=1);

namespace vixikhd\defaultcommands\command;

use proxy\command\Command;
use proxy\command\CommandSender;
use vixikhd\defaultcommands\DefaultCommands;

/**
 * Class HelpCommand
 * @package vixikhd\defaultcommands\command
 */
class HelpCommand extends Command {

    /** @var DefaultCommands $plugin */
    private $plugin;

    /**
     * HelpCommand constructor.
     * @param DefaultCommands $plugin
     */
    public function __construct(DefaultCommands $plugin) {
        $this->plugin = $plugin;
        parent::__construct("help", "Displays help page");
    }

    /**
     * @param CommandSender $sender
     * @param array $args
     * @return bool
     */
    public function onExecute(CommandSender $sender, array $args): bool {
        $sender->sendMessage("--- MCBE Proxy Help Page ---");
        foreach ($this->plugin->getServer()->getCommandMap()->getCommands() as $command) {
            $sender->sendMessage("{$command->getName()} :: {$command->getDescription()}");
        }
        return false;
    }
}