<?php

namespace proxy\network;

use proxy\network\encryption\Encryption;
use proxy\Server;
use raklib\protocol\IncompatibleProtocolVersion;
use raklib\protocol\OpenConnectionReply1;
use raklib\protocol\OpenConnectionReply2;
use raklib\protocol\OpenConnectionRequest1;
use raklib\protocol\OpenConnectionRequest2;
use raklib\protocol\UnconnectedPing;
use raklib\protocol\UnconnectedPong;
use raklib\utils\InternetAddress;

use pocketmine\network\mcpe\encryption\NetworkCipher;

class DownstreamListener
{

    /** @var DownstreamSocket $downstream */
    private $downstream;

    /** @var Server $server */
    private $server;

    /** @var InternetAddress $address */
    private $address;

    /** @var NetworkCipher $networkCipher */
    private $networkCipher;

    /** @var Encryption $encryption */
    private $encryption;

    /** @var bool $raknetDone */
    private $raknetDone = false;

    /** @var array $ackQueue */
    private $ackQueue = array();

    /** @var array $nakQueue */
    private $nakQueue = array();

    /** @var int $sendSeqNumber */
    private $sendSeqNumber = 0;

    /** @var array $splitPackets */
    private $splitPackets = array();

    /**
     * DownstreamListener constructor.
     * @param DownstreamSocket $socket
     */
    public function __construct(DownstreamSocket $socket, Server $server)
    {
        $this->downstream = $socket;
        $this->server = $server;

        if(Server::getInstance()->encryptionEnabled()){
            $this->networkCipher = new NetworkCipher(Server::CODENAME);
        }
    }

    public function tick() : void{
        $this->downstream->receive($buffer, $address, $port);

        if($this->raknetDone)$this->handleMCPE($buffer, $address, $port);
        $this->handleRaknet($buffer, $address, $port);
    }

    /**
     * @param string $buffer
     */
    public function handleRaknet(string $buffer, $address, $port) : void{
         $pid = ord($buffer{0});
         switch($pid){
             case UnconnectedPing::$ID;
             $this->address = new InternetAddress($address, $port, 4);

             $ping = new UnconnectedPing($buffer);
             $ping->decode();

             $pong = new UnconnectedPong();
             //$pong->sendPingTime = $ping->sendPingTime; Bylo to asi blbě :D
             $pong->sendTime = $ping->sendTime; // <----
             $pong->serverName = $this->getPongInfo();
             $pong->encode();

             $this->downstream->send($pong->getBuffer(), $this->address->ip, $this->address->port);
             break;
             case OpenConnectionRequest1::$ID;
             $request = new OpenConnectionRequest1($buffer);
             $request->decode();

             if($request->protocol !== 10){
                 $packet = new IncompatibleProtocolVersion();
                 $packet->protocolVersion = 10;
                 $packet->serverId = 1;
                 $packet->encode();

                 $this->downstream->send($packet->getBuffer(), $this->address->ip, $this->address->port);
                 break;
             }

             $reply = new OpenConnectionReply1();
             $reply->mtuSize = $request->mtuSize + 28;
             $reply->serverId = 1;

             $this->downstream->send($reply->getBuffer(), $this->address->ip, $this->address->port);
             break;
             case OpenConnectionRequest2::$ID;
             $request = new OpenConnectionRequest2($buffer);
             $request->decode();

             $mtuSize = min($request->mtuSize, 5000);
             $reply = new OpenConnectionReply2();
             $reply->mtuSize = $mtuSize;
             $reply->serverID = 1;
             $reply->clientAddress = $this->address;
             $reply->encode();

             $this->downstream->send($reply->getBuffer(), $this->address->ip, $this->address->port);
             $this->raknetDone = true;
             break;
         }
    }

    /**
     * @param string $buffer
     * @param string $address
     * @param int $port
     */
    public function handleMCPE(string $buffer, string $address, int $port) : void{

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
                    Server::getInstance()->downstreamConnected ? 1 : 0,
                    1,
                    1,
                    'MCBE Proxy',
                    'survival'
                ]) . ";";
    }

}
