<?php

namespace proxy\network;

use proxy\Server;
use raklib\utils\InternetAddress;

/**
 * Class UpstreamSocket
 * @package proxy\network
 */
class UpstreamSocket {

    /** @var InternetAddress $address */
    private $address;

    /** @var InternetAddress $target */
    private $target;

    /** @var resource $socket */
    private $socket;

    public function __construct(string $tIP, int $tPORT) {
        $this->address = new InternetAddress("0.0.0.0", rand(10000, 50000), 4);
        $this->target = new InternetAddress($tIP, $tPORT, 4);
        $this->socket = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);

        $this->connect($this->target);
    }

    /**
     * @param InternetAddress $address
     * @throws \Exception
     */
    public function connect(InternetAddress $address) {
        if(!socket_connect($this->socket, $this->target->ip, $this->target->port)){
            throw new \Exception("Failed to create connection with " . $this->target->ip . ":" . $this->target->port);
        }
    }

    /**
     * @param string|null $buffer
     * @param string|null $ip
     * @param int|null $port
     */
    public function receive(?string &$buffer, ?string &$ip, ?int &$port) {
        socket_recvfrom($this->socket, $buffer, 65535, 0, $ip, $port);
    }

    /**
     * @param string $buffer
     * @param string $ip
     * @param int $port
     */
    public function send(string $buffer, string $ip, int $port) {
        socket_sendto($this->socket, $buffer, strlen($buffer), 0, $ip, $port);
    }

    /**
     *
     */
    public function close() {
        socket_close($this->socket);
    }

    /**
     * @return InternetAddress
     */
    public function getAddress(): InternetAddress {
        return $this->address;
    }

    /**
     * @return InternetAddress
     */
    public function getTarget() : InternetAddress{
        return $this->target;
    }

}