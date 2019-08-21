<?php

namespace proxy\network;

use pocketmine\network\mcpe\compression\Zlib;
use pocketmine\network\mcpe\PacketBatch;
use pocketmine\utils\Binary;
use proxy\network\encryption\Encryption;
use proxy\network\mcpe\LoginPacket;
use proxy\Server;
use raklib\protocol\ConnectionRequest;
use raklib\protocol\ConnectionRequestAccepted;
use raklib\protocol\EncapsulatedPacket;
use raklib\protocol\IncompatibleProtocolVersion;
use raklib\protocol\NewIncomingConnection;
use raklib\protocol\OpenConnectionReply1;
use raklib\protocol\OpenConnectionReply2;
use raklib\protocol\OpenConnectionRequest1;
use raklib\protocol\OpenConnectionRequest2;
use raklib\protocol\PacketReliability;
use raklib\protocol\UnconnectedPing;
use raklib\protocol\UnconnectedPong;
use raklib\utils\InternetAddress;
use raklib\protocol\Datagram;

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

    /** @var MessageSender $messageSender */
    private $messageSender;

    /** @var Encryption $encryption */
    private $encryption;

    /** @var bool $raknetDone */
    private $raknetDone = false;

    /** @var bool $decryptPackets */
    private $decryptPackets = false;

    /** @var LoginPacket $cachedLogin */
    private $cachedLogin;

    /** @var int $sendSeqNumber */
    private $sendSeqNumber = 0;


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
        if($buffer==null)return;

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
             $pong->sendPingTime = $ping->sendPingTime;
             $pong->serverName = "MCPE;§bsvile sucks;361;1.12.0;0;20;9214870599196236078;MCBE Proxy;Survival;";
             $pong->serverId = 1;
             $pong->encode();

             $this->downstream->send($pong->getBuffer(), $this->address->ip, $this->address->port);
             break;
             case OpenConnectionRequest1::$ID;
             $request = new OpenConnectionRequest1($buffer);
             $request->decode();

             if($request->protocol !== 9){
                 $packet = new IncompatibleProtocolVersion();
                 $packet->protocolVersion = 10;
                 $packet->serverId = 1;
                 $packet->encode();

                 $this->downstream->send($packet->getBuffer(), $this->address->ip, $this->address->port);

                 break;
             }


             $reply = new OpenConnectionReply1();
             $reply->mtuSize = $request->mtuSize + 28;
             $reply->serverID = 1;
             $reply->encode();

             $this->downstream->send($reply->getBuffer(), $address, $port);
             break;
             case OpenConnectionRequest2::$ID;
             $request = new OpenConnectionRequest2($buffer);
             $request->decode();

             $mtuSize = min($request->mtuSize, 5000);
             $reply = new OpenConnectionReply2();
             $reply->mtuSize = $mtuSize;
             $reply->serverID = 1;
             $reply->clientAddress = new InternetAddress($address, $port, 4);
             $reply->encode();

             $this->downstream->send($reply->getBuffer(), $address, $port);

             break;
             default:
                 if (($pid & Datagram::BITFLAG_VALID) !== 0) {
                     $datagram = new Datagram($buffer);
                     $datagram->decode();
                     foreach($datagram->packets as $encapsulated){
                         $pid = ord($encapsulated->buffer{0});

                         switch($pid){
                             case ConnectionRequest::$ID;
                             $request = new ConnectionRequest($buffer);
                             $request->decode();

                             $pk = new ConnectionRequestAccepted();
                             $pk->address = new InternetAddress($address, $port,4);
                             $pk->sendPingTime = $request->sendPingTime;
                             $pk->sendPongTime = $request->sendPingTime + 1; //hack
                             $pk->encode();

                             $encapsulated = new EncapsulatedPacket();
                             $encapsulated->reliability = PacketReliability::UNRELIABLE;
                             $encapsulated->orderChannel = 0;
                             $encapsulated->buffer = $pk->getBuffer();

                             $datagram = new Datagram();
                             $datagram->packets[] = $encapsulated;
                             $datagram->seqNumber = $this->sendSeqNumber++;
                             $datagram->sendTime = time();
                             $datagram->encode();
                             $this->downstream->send($datagram->getBuffer(), $address, $port);
                             break;
                             case NewIncomingConnection::$ID;
                             $this->raknetDone = true;
                             break;
                         }
                     }
                 }
             break;

         }
    }

    /**
     * @param string $buffer
     * @param string $address
     * @param int $port
     */
    public function handleMCPE(string $buffer, string $address, int $port) : void{
        $pid = ord($buffer{0});
        if (($pid & Datagram::BITFLAG_VALID) !== 0) {
            if ($pid & Datagram::BITFLAG_ACK) {
                //TODO: implement this
            } elseif ($pid & Datagram::BITFLAG_NAK) {
               //TODO: implement this
            } else {
                if (($datagram = new Datagram($buffer)) instanceof Datagram) {
                    $datagram->decode();
                    foreach ($datagram->packets as $packet) {
                     /*   if ($packet->hasSplit) {
                            $split = //TODO: decode of split

                            if ($split !== null) {
                                $packet = $split;
                            }
                        }*/

                        $payload = substr($packet->buffer, 1);

                        if($this->networkCipher !== null && $this->decryptPackets){
                            $payload = $this->networkCipher->decrypt($payload);
                        }

                        $stream = new PacketBatch(Zlib::decompress($payload));
                        $mcpe = $stream->getPacket();

                        switch($mcpe::$NETWORK_ID){
                            case LoginPacket::$NETWORK_ID;
                            $mcpe->decode();

                            $this->cachedLogin = $mcpe;

                            $this->server->getLogger()->info($mcpe->username . " has joined [XUID: " . $mcpe->xuid . "]");

                            //TODO: upstream connection


                            break;
                        }

                    }

                }
            }
        }
    }


    /**
     * @return string
     */
    public function getPongInfo() : string{
        return "MCBE PROXY"  . ";" . "survival" . ";" . "void" . ";" . "0" . ";" . "1"  . ";" . Binary::writeLShort(19132) . "127.0.0.1" . ";";
    }

}
