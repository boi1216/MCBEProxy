<?php


namespace proxy\network;

use pocketmine\utils\InternetAddress;

class DownstreamSocket
{

    /** @var InternetAddress $address */
    private $address;

    /** @var resource $socket */
    private $socket;

    /**
     * DownstreamSocket constructor.
     * @param string $ip
     * @param int $port
     */
    public function __construct(string $ip, int $port)
    {
         $this->address = new InternetAddress($ip, $port, 4);
         $this->socket = socket_create(AF_INET, SOL_UDP, SOCK_DGRAM);
    }

    /**
     * @param InternetAddress $address
     * @throws \Exception
     */
    public function bind(InternetAddress $address) {
        if(socket_bind($this->socket, $address->ip, $address->port)) {
            echo 'Downstream bound to ' . $address->ip . ':' . $address->port;
        }
        else {
            throw new \Exception("Could not bind to {$address->ip}:{$address->port}");
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

    public function close() {
        socket_close($this->socket);
    }

}