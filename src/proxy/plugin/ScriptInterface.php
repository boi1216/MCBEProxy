<?php

declare(strict_types=1);

namespace proxy\plugin;

/**
 * Interface ScriptInterface
 * @package proxy\plugin
 */
interface ScriptInterface {

    /**
     * @return void
     */
    public function load();

    /**
     * @return void
     */
    public function unload();

    /**
     * @return string
     */
    public function getName(): string;

    /**
     * @return string
     */
    public function getVersion(): string;

    /**
     * @return string
     */
    public function getDescription(): string;
}