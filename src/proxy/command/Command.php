<?php

declare(strict_types=1);

namespace proxy\command;

/**
 * Class Command
 * @package proxy\command
 */
abstract class Command {

    /** @var string $name */
    private $name;

    /** @var string $description */
    private $description;

    /**
     * Command constructor.
     * @param string $name
     * @param string $description
     */
    public function __construct(string $name, string $description) {
        $this->name = $name;
        $this->description = $description;
    }

    /**
     * @param CommandSender $sender
     * @param array $args
     *
     * @return bool $sendPermissionMessage
     */
    public abstract function onExecute(CommandSender $sender, array $args): bool;

    /**
     * @return string
     */
    public function getName(): string {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getDescription(): string {
        return $this->description;
    }
}