<?php
namespace App;


class Transaction {
    private $sender;
    private $receiver;
    private $amount;
    private $signature;
    private $keys;

    function __construct($sender, $receiver, $amount) {
        $this->receiver = $receiver;
        $this->sender = $sender;
        $this->amount = $amount;
    }

    function getData() {
        return hash("sha256",$this->sender.$this->receiver.$this->amount);
    }

    function sign(KeyStore $keys) {
        $this->keys = $keys;
        $public_key = $keys->getPublicKeyString();
        if($this->sender != $public_key)
            return false;

        if(empty($this->signature)) {
            openssl_sign($this->getData(),$this->signature, $keys->getPrivateKeyResource());
        }
        return $this->signature;
    }

    function getSender() {
        return $this->sender;
    }

    function getReceiver() {
        return $this->receiver;
    }

    function getAmount() {
        return $this->amount;
    }

    function validate() {
        if($this->sender === null) return true;

        if(!$this->signature or strlen($this->signature) <= 0)
            return false;

        return openssl_verify($this->getData(), $this->signature, $this->keys->getPublicKeyResource());
    }
}