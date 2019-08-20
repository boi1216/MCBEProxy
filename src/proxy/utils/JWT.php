<?php


namespace proxy\utils;

use Mdanter\Ecc\Crypto\{Key\PrivateKeyInterface, Signature\Signature};
use Mdanter\Ecc\Serializer\PrivateKey\{DerPrivateKeySerializer, PemPrivateKeySerializer};
use Mdanter\Ecc\Serializer\PublicKey\{DerPublicKeySerializer, PemPublicKeySerializer};
use Mdanter\Ecc\Serializer\Signature\DerSignatureSerializer;


class JWT
{

    /**
     * @param string $data
     * @return string
     */
    public static function b64UrlEncode(string $data) : string{
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    /**
     * @param $data
     * @return string
     */
    private static function b64UrlDecode($data) : string{
        return base64_decode(str_pad(strtr($data, '-_', '+/'), strlen($data) % 4, '=', STR_PAD_RIGHT));
    }

    /**
     * @param array $data
     * @return string
     */
    public static function encodePart(array $data) : string{
        return self::b64UrlEncode(json_encode($data));
    }

    /**
     * @param string $part
     * @return array
     */
    private static function decodePart(string $part) : array{
        return json_decode(self::b64UrlDecode($part), true) ?? [];
    }

    /**
     * @param string $jwt
     * @param $headers
     * @param $payload
     * @return bool
     */
    public static function decode(string $jwt, &$headers, &$payload) : bool{
        [$headB64, $payloadB64, $sigB64] = explode(".", $jwt);
        $headers = self::decodePart($headB64);
        $payload = self::decodePart($payloadB64);
        if(strlen($plainSignature = self::b64UrlDecode($sigB64)) !== 96){
            return false;
        }
        [$rString, $sString] = str_split($plainSignature, 48);
        $sig = new Signature(gmp_init(bin2hex($rString), 16), gmp_init(bin2hex($sString), 16));
        $der = new DerPublicKeySerializer();
        $signature = (new DerSignatureSerializer())->serialize($sig);
        $pubKey = (new PemPublicKeySerializer($der))->serialize($der->parse(base64_decode($headers["x5u"] ?? null)));
        return openssl_verify("$headB64.$payloadB64", $signature, $pubKey, OPENSSL_ALGO_SHA384) === 1;
    }

    /**
     * @param PrivateKeyInterface $privateKey
     * @param array $payload
     * @return string
     */
    public static function encode(PrivateKeyInterface $privateKey, array $payload) : string{
        $publicKey = $privateKey->getPublicKey();
        $jwtBody =
            self::encodePart(["x5u" => $pubKey = base64_encode((new DerPublicKeySerializer())->serialize($publicKey)), "alg" => "ES384"]) . "." .
            self::encodePart(["exp" => 0] + $payload + ["identityPublicKey" => $pubKey, "nbf" => 0]); //TODO: exp, nbf
        openssl_sign($jwtBody, $sig, (new PemPrivateKeySerializer(new DerPrivateKeySerializer()))->serialize($privateKey), OPENSSL_ALGO_SHA384);
        $decodedSig = (new DerSignatureSerializer())->parse($sig);
        $jwtSig = self::b64UrlEncode(
            hex2bin(str_pad(gmp_strval($decodedSig->getR(), 16), 96, "0", STR_PAD_LEFT)) .
            hex2bin(str_pad(gmp_strval($decodedSig->getS(), 16), 96, "0", STR_PAD_LEFT))
        );
        return "$jwtBody.$jwtSig";
    }


}