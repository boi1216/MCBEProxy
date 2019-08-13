<?php

declare(strict_types=1);

namespace proxy;


/**
 * Class ThreadManager
 * @package proxy
 */
class ThreadManager {

    /** @var ThreadManager $instance */
    private static $instance;

    /** @var \Thread[] $threads */
    private $threads = [];

    public static function init() {
        if(!self::$instance instanceof ThreadManager) {
            self::$instance = new ThreadManager();
        }
    }

    /**
     * @param \Thread $thread
     * @return string
     */
    public function runThread(\Thread $thread): string {
        $this->threads[$hash = spl_object_hash($thread)] = $thread;
        $thread->start();
        return $hash;
    }

    /**
     * @param string $thread
     * @return \Thread|null
     */
    public function getThread(string $thread): ?\Thread {
        return isset($this->threads[$thread]) ? $this->threads[$thread] : null;
    }

    /**
     * Stejně to nebude fungovat
     */
    public function closeThreads() {
        foreach ($this->threads as $thread) {
            $thread->shutdown = true;
            $thread->join();
        }
    }

    /**
     * @return ThreadManager
     */
    public static function getInstance(): ThreadManager {
        return self::$instance;
    }
}