<?php

namespace proxy\network;

use proxy\Server;
use raklib\utils\InternetAddress;

/**
 * Class DownstreamSocket
 * @package proxy\network
 */
class DownstreamSocket {

    /** @var InternetAddress $address */
    private $address;

    /** @var resource $socket */
    private $socket;

    /**
     * DownstreamSocket constructor.
     * @param string $ip
     * @param int $port
     * @throws \Exception
     */
    public function __construct(string $ip, int $port) {
         $this->address = new InternetAddress($ip, $port, 4);
         $this->socket = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);
         $this->bind($this->getAddress());
    }

    /**
     * @param InternetAddress $address
     * @throws \Exception
     */
    public function bind(InternetAddress $address) {
        if(socket_bind($this->socket, $address->ip, $address->port)) {
            Server::getInstance()->getLogger()->info('Downstream bound to ' . $address->ip . ':' . $address->port);
        }
        else {
            Server::getInstance()->getLogger()->error("Could not bind to {$address->ip}:{$address->port}, maybe some server is already running on that port?");
            Server::getInstance()->getLogger()->error("Shutting down the server in 10 sec.");
            sleep(10);
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

}