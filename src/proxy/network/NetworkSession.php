<?php


namespace proxy\network;


class NetworkSession
{

    public $sendQueue = array();

    public $nakQueue = array();
    public $ackQueue = array();

    public $needACK = array();

    /** @var DownstreamListener $downstream */
    private $downstream;

    /** @var UpstreamListener $upstream */
    private $upstream;

    /**
     * NetworkSession constructor.
     * @param DownstreamListener $downstream
     * @param UpstreamListener $upstream
     */
    public function __construct(DownstreamListener $downstream, UpstreamListener $upstream){
          $this->downstream = $downstream;
          $this->upstream = $upstream;
    }

}