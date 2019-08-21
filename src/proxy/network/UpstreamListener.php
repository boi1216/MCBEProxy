<?php


namespace proxy\network;


use proxy\network\mcpe\LoginPacket;
use proxy\Server;
use raklib\protocol\ConnectionRequest;
use raklib\protocol\Datagram;
use raklib\protocol\EncapsulatedPacket;
use raklib\protocol\OpenConnectionReply1;
use raklib\protocol\OpenConnectionReply2;
use raklib\protocol\OpenConnectionRequest1;
use raklib\protocol\OpenConnectionRequest2;
use raklib\protocol\UnconnectedPing;
use raklib\protocol\UnconnectedPong;
use raklib\utils\InternetAddress;

class UpstreamListener
{

    /** @var UpstreamSocket */
    private $upstream;

    /** @var Server $server */
    private $server;

    /** @var MessageSender $messageSender */
    private $messageSender;


    /**
     * UpstreamListener constructor.
     * @param UpstreamSocket $socket
     * @param Server $server
     */
    public function __construct(UpstreamSocket $socket, Server $server)
    {
        $this->upstream = $socket;
        $this->server = $server;

        $this->messageSender = new MessageSender($this->upstream->getTarget(), $this->upstream->socket);

    }

    public function startLogin() : void{
        $packet = new UnconnectedPing();
        $packet->sendPingTime = time();
        $packet->clientId = -1;
        $packet->encode();

        $this->upstream->send($packet->getBuffer(), $this->upstream->getTarget()->ip, $this->upstream->getTarget()->port);
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
            case UnconnectedPong::$ID;
            $packet = new OpenConnectionRequest1();
            $packet->mtuSize = 1236;
            $packet->protocol = 8;
            $packet->encode();

            $this->upstream->send($packet->getBuffer(), $address, $port);
            break;
            case OpenConnectionReply1::$ID;
            $opc = new OpenConnectionRequest2();
            $opc->mtuSize = 576;
            $opc->serverAddress = new InternetAddress("0.0.0.0", 19132, 4);
            $opc->clientID = -1;
            $opc->encode();

            $this->upstream->send($opc->getBuffer(), $address, $port);
            break;
            case OpenConnectionReply2::$ID;
            $pk = new ConnectionRequest();
            $pk->clientID = $this->clientID;
            $pk->sendPingTime = $this->sendPingTime;
            $pk->encode();
            $encapsulated = new EncapsulatedPacket();
            $encapsulated->hasSplit = false;
            $encapsulated->buffer = $pk->buffer;
            $encapsulated->reliability = 0;
            $datagram = new Datagram();
            $datagram->seqNumber = 0;
            $datagram->packets[] = $encapsulated;
            $datagram->encode(); //TODO: move this

            $this->upstream->send($datagram->getBuffer(), $address, $port);

            break;
        }
    }

}