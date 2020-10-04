<?php
namespace App;


class Blockchain extends \ArrayObject {
    private $weight;
    private $reward;
    private $transactions;

    public function __construct($weight = 2, $transactions = []) {
        parent::__construct();
        $this[] = $this->createGenesisBlock();
        $this->transactions = $transactions;
        $this->weight = $weight;
        $this->reward =  rand(40,100);
    }

    function addBlock($from) {
        $block = new Block($this->transactions, time(), $this->getLastBlock()->getHash());
        $block->pow($this->weight);
        if($block->validateTransactions())
            return false;

        $this[] = $block;
        $this->transactions = [
            new Transaction(null, $from, $this->reward)
        ];

        return $this;
    }

    function addTransaction(Transaction $transaction) {
        if(empty($transaction->getSender()) || empty($transaction->getReceiver()))
            return false;

        if(!$transaction->validate())
            return false;


        $this->transactions[] = $transaction;
        return true;
    }

    function getMyBalance($address, &$old_balance = 0) {
        foreach($this as $index => $block) {
            if($index == 0) continue;
            foreach ($block->getTransactions() as $transaction) {
                if ($transaction->getSender() == $address) {
                    $old_balance -= $transaction->getAmount();
                } else {
                    $old_balance += $transaction->getAmount();
                }
            }
        }
    }

    function createGenesisBlock() {
        return new Block(["genesis"], time());
    }

    function getGenesis() {
        return $this[0];
    }

    function getLastBlock() : Block {
        return $this[count($this)-1];
    }

    function validate() {
        for($i = 1; $i<count($this); $i++) {
            if($this[$i]->getPrevHash() != $this[$i-1]->getHash())
                return false;

            if(!$this[$i]->validateTransactions())
                return false;
        }
        return true;
    }
}