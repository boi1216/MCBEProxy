<?php


namespace proxy\network;


use proxy\Server;

class DownstreamListener
{

    /** @var DownstreamSocket $downstream */
    private $downstream;

    /**
     * DownstreamListener constructor.
     * @param DownstreamSocket $socket
     */
    public function __construct(DownstreamSocket $socket)
    {
        $this->downstream = $socket;
    }

    public function tick() : void{
        $this->downstream->receive($buffer, $address, $port);

    }

    /**
     * @param string $buffer
     */
    public function handleRaknet(string $buffer) : void{
         $pid = ord($buffer{0});
         switch($pid){
             case UnconnectedPing::$ID;

             break;
         }
    }

    public function handleMCPE(string $buffer) : void{

    }

    /**
     * @return string
     */
    public function getPongInfo() : string{
        return implode(";",
                [
                    "MCPE",
                    rtrim(addcslashes("MCPE Proxy", ";"), '\\'),
                    354,
                    "1.11",
                    Server::$instance->downstreamConnected ? 1 : 0,
                    1,
                    1,
                    'MCBE Proxy',
                    'survival'
                ]) . ";";
    }

}