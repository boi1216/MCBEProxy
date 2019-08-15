<?php


namespace proxy\network;


use proxy\Server;

class UpstreamListener
{

    /** @var UpstreamSocket */
    private $upstream;



    public function __construct(UpstreamSocket $socket)
    {
        $this->upstream = $socket;
    }

    public function startLogin() : void{
        $packet = new UnconnectedPing();
        $packet->sendPingTime = time();
        $packet->clientId = -1;
    }

    public function tick() : void{
        $this->upstream->receive($buffer, $address, $port);


    }

    /**
     * @param string $buffer
     * @param $address
     * @param $port
     */
    public function handleRaknet(string $buffer, $address, $port) : void{
        $pid = ord($buffer{0});
        switch($pid){
            //TODO: Full raknet login
        }
    }



}