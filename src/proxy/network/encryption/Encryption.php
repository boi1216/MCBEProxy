<?php


namespace proxy\network\encryption;

use Mdanter\Ecc\Crypto\Key\PrivateKeyInterface;
use Mdanter\Ecc\Crypto\Key\PublicKeyInterface;
use Mdanter\Ecc\EccFactory;
use Mdanter\Ecc\Serializer\PrivateKey\DerPrivateKeySerializer;
use Mdanter\Ecc\Serializer\PrivateKey\PemPrivateKeySerializer;
use Mdanter\Ecc\Serializer\PublicKey\DerPublicKeySerializer;
use Mdanter\Ecc\Serializer\Signature\DerSignatureSerializer;


class Encryption
{

    /** @var PublicKeyInterface $clientPublicKey */
    public $clientPublicKey;

    /** @var PrivateKeyInterface $proxyPrivateKey */
    public $proxyPrivateKey;

    /** @var string $handshakeJWT */
    public $handshakeJWT;
    public $aesKey;

    /**
     * Encryption constructor.
     * @param PublicKeyInterface $clientPublicKey
     * @throws \Exception
     */
    public function __construct(PublicKeyInterface $clientPublicKey)
    {
         $this->proxyPrivateKey = EccFactory::getNistCurves()->generator384()->createPrivateKey();
         $this->clientPublicKey = $clientPublicKey;

         $sharedSecret = $this->proxyPrivateKey->createExchange($this->clientPublicKey)->calculateSharedKey(); //final combination of public + private key

         $i = random_bytes(16);
         $this->aesKey = openssl_digest($i . hex2bin(str_pad(gmp_strval($sharedSecret, 16), 96, "0", STR_PAD_LEFT)), 'sha256', true);
         $this->generateServerHandshakeJwt($this->proxyPrivateKey, $i);
    }

    /**
     * @param PrivateKeyInterface $serverPriv
     * @param string $salt
     * @return string
     */
    private function generateServerHandshakeJwt(PrivateKeyInterface $serverPriv, string $salt) : string{
        $jwtBody = self::b64UrlEncode(json_encode([
                    "x5u" => base64_encode((new DerPublicKeySerializer())->serialize($serverPriv->getPublicKey())),
                    "alg" => "ES384"
                ])
            ) . "." . self::b64UrlEncode(json_encode([
                    "salt" => base64_encode($salt)
                ])
            );
        openssl_sign($jwtBody, $sig, (new PemPrivateKeySerializer(new DerPrivateKeySerializer()))->serialize($serverPriv), OPENSSL_ALGO_SHA384);
        $decodedSig = (new DerSignatureSerializer())->parse($sig);
        $jwtSig = self::b64UrlEncode(
            hex2bin(str_pad(gmp_strval($decodedSig->getR(), 16), 96, "0", STR_PAD_LEFT)) .
            hex2bin(str_pad(gmp_strval($decodedSig->getS(), 16), 96, "0", STR_PAD_LEFT))
        );
        return "$jwtBody.$jwtSig";
    }

    /**
     * @param string $str
     * @return string
     */
    private static function b64UrlEncode(string $str) : string{
        return rtrim(strtr(base64_encode($str), '+/', '-_'), '=');
    }

}