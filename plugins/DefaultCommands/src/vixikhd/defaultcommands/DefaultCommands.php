<?php

declare(strict_types=1);

namespace vixikhd\defaultcommands;

use proxy\plugin\PluginBase;
use vixikhd\defaultcommands\command\HelpCommand;
use vixikhd\defaultcommands\command\StopCommand;

/**
 * Class DefaultCommands
 * @package vixikhd\defaultcommands
 */
class DefaultCommands extends PluginBase {

    public function onEnable() {
        require "plugins/DefaultCommands/src/vixikhd/defaultcommands/command/HelpCommand.php";
        require "plugins/DefaultCommands/src/vixikhd/defaultcommands/command/StopCommand.php";
        $this->getServer()->getCommandMap()->registerCommand(new HelpCommand($this));
        $this->getServer()->getCommandMap()->registerCommand(new StopCommand($this));
    }

    public function onDisable() {
    }
}