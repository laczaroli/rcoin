<?php
namespace App;

class Block {
    public $previousBlockHash;
    private $transactions;
    private $timestamp;
    private $hash;
    private $nonce;

    function __construct(array $transactions, $timestamp, $previousBlockHash = "") {
        $this->previousBlockHash = $previousBlockHash;
        $this->transactions = $transactions;
        $this->timestamp = $timestamp;
        $this->nonce = 0;
    }

    function getHash() {
        return $this->hash = hash("sha256",json_encode($this->transactions).$this->timestamp.$this->previousBlockHash.$this->nonce);
    }

    function getPrevHash() {
        return $this->previousBlockHash;
    }


    function validateTransactions() {
        foreach($this->transactions as $transaction) {
            if($transaction->validate()) {
                return false;
            }
        }
        return true;
    }

    function getTransactions() {
        return $this->transactions;
    }

    function pow($weight = 2) {
        $startingZeros = "";
        for($i = 0; $i<$weight; $i++) {
            $startingZeros .= "0";
        }
        while((substr($this->getHash(), 0, $weight)) !== $startingZeros) {
            $this->nonce++;
            $this->getHash();
        }
    }
}