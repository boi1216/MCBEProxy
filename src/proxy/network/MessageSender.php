<?php


namespace proxy\network;

use raklib\utils\InternetAddress;


class MessageSender extends \Thread
{

    /** @var InternetAddress $address */
    private $address;

    /** @var array $buffers */
    private $buffers = array();

    /** @var resource $sendingSocket */
    private $sendingSocket;

    /**
     * MessageSender constructor.
     * @param InternetAddress $address
     * @param $socket
     */
    public function __construct(InternetAddress $address, $socket)
    {
        $this->address = $address;
        $this->sendingSocket = $socket;
    }

    public function run()
    {
        socket_sendto($this->sendingSocket, $buf = array_shift($this->buffers), strlen($buf), 0, $this->address->ip, $this->address->port);
        $this->run();
    }

}