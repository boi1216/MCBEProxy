<?php

namespace proxy;

/**
 * Class ConsoleReader
 * @package proxy
 */
class ConsoleReader extends \Thread {

    /** @var bool $shutdown */
    public $shutdown;

    /** @var \Threaded $buffer */
    public $buffer;

    /**
     * @param int $options
     * @return bool|void
     */
    public function start(int $options = PTHREADS_INHERIT_ALL) : void {
        $this->buffer = new \Threaded();
        parent::start($options);
    }

    public function kill() : void {
        parent::kill();
    }

    public function run() : void {
        $resource = fopen("php://stdin", "r");
        while ($this->shutdown !== true) {
            $commandLine = trim(fgets($resource));
            if ($commandLine != "") {
                $this->buffer[] = $commandLine;
            }
        }
    }

    public function setGarbage() : void {
        // TODO: Implement setGarbage() method.
    }
}