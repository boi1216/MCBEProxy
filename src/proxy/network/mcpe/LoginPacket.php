<?php


namespace proxy\network\mcpe;



class LoginPacket extends pocketmine\network\mcpe\protocol\LoginPacket
{

    public function encodePayload() {
        $this->putInt($this->protocol);

        $chainData = json_encode($this->chainData);
        $this->putString(strlen($chainData) . $chainData . strlen($this->clienDataJwt) . $this->clientDataJwt);
    }

}