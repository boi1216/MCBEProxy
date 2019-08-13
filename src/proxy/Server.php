<?php


namespace proxy;


use proxy\network\DownstreamListener;
use proxy\network\DownstreamSocket;

class Server
{

    /** @var Server $instance */
    public static $instance;

    /** @var int $startTime */
    public static $startTime;

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
     */
    public function __construct(array $arguments)
    {
        self::$instance = $this;
        echo 'Starting MCPE Proxy' . PHP_EOL;

        self::$startTime = time();

        $this->downstreamListener = new DownstreamListener(new DownstreamSocket("0.0.0.0", 19132));

        while($this->running){
            try{
               $this->downstreamListener->tick();

            }catch (\Exception $ingore){}
        }
    }




}