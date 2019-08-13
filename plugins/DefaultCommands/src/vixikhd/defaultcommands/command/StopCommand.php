<?php

declare(strict_types=1);

namespace vixikhd\defaultcommands\command;

use proxy\command\Command;
use proxy\command\CommandSender;
use vixikhd\defaultcommands\DefaultCommands;

/**
 * Class StopCommand
 * @package vixikhd\defaultcommands\command
 */
class StopCommand extends Command {

    /** @var DefaultCommands $plugin */
    public $plugin;

    /**
     * StopCommand constructor.
     * @param DefaultCommands $plugin
     */
    public function __construct(DefaultCommands $plugin) {
        $this->plugin = $plugin;
        parent::__construct("stop", "Stops proxy server");
    }

    /**
     * @param CommandSender $sender
     * @param array $args
     * @return bool
     */
    public function onExecute(CommandSender $sender, array $args): bool {
        $this->plugin->getServer()->shutdown();
        return false;
    }


}