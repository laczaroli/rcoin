<?php
namespace App;


class KeyStore {

    private $res;
    private function createKeys() {
        $config = ["digest_alg" => "sha256",
                    "private_key_bits" => 4096,
                    "private_key_type" => OPENSSL_KEYTYPE_RSA];
        return $this->res ?? $this->res = openssl_pkey_new($config);
    }

    function getPublicKeyResource() {
        return openssl_get_publickey($this->getPublicKeyString());
    }
    function getPublicKeyString() {
        if(!$this->res) $this->createKeys();
        $pubKey = openssl_pkey_get_details($this->res);
        return $pubKey["key"];
    }


    function getPrivateKeyResource() {
        return openssl_get_privatekey($this->getPrivateKeyString());
    }

    function getPrivateKeyString() {
        if(!$this->res) $this->createKeys();
        $privKey = "";
        openssl_pkey_export($this->res, $privKey);
        return $privKey;
    }
}