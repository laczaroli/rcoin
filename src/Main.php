<?php
namespace App;
use drupol\phpmerkle\Merkle;

class Main {
    private $tree;
    private $blockchain;
    private $balance;

    function __construct() {
        $this->tree = new Merkle();
        $this->blockchain = new Blockchain($_POST["weight"]);
        $this->balance = 0;
    }

    function run() {
        $this->handleRequests();
        $this->show();
    }

    private function handleRequests() {
        if($_POST["submit_calculate"]) {
            $keys = new KeyStore();
            $from = $keys->getPublicKeyString();
            for ($i = 0; $i < $_POST["blocks"]; $i++) {
                $transaction = new Transaction($from, $this->getRandomName(), rand(0,40));
                $transaction->sign($keys);

                $this->blockchain->addTransaction($transaction);
                $this->blockchain->addBlock($from);
                $this->blockchain->getMyBalance($from, $this->balance);
            }

            foreach($this->blockchain as $block) {
                $this->tree[] = $block->getHash();
            }

        }
    }

    private function getRandomName() {
        $names = ["Bob", "William", "Sharley", "Tim", "Janny", "Yone"];
        return $names[rand(0, count($names)-1)];
    }

    function getTreeHash() {
        return $this->tree->count() > 0 ? $this->tree->hash() : -1;
    }

    function show() {
        ?>
        <style>
            .block-table {
                width: 100%;
                border-collapse: collapse;
            }
            .block-table td,
            .block-table th {
                padding: 0.5em;
            }

            .block-table td {
                text-align: center;
            }

            .block-table th {
                background-color: #696969;
                color: white;
            }

            .block-table tr td {
                background-color: #c0c0c0;
            }
        </style>
        <form method="post">
            <div class="">
                <label for="weight">Weight:</label>
                <input type="number" size="6" name="weight" id="weight" value="<?=$_POST["weight"]?>">
            </div><div class="">
                <label for="blocks">Number of blocks (excluding genesis):</label>
                <input type="number" size="6" name="blocks" id="blocks" value="<?=$_POST["blocks"]?>">
            </div>
            <div class="">
                <input type="submit" name="submit_calculate" value="Calculate">
            </div>
        </form>


        <table class="block-table">
            <tr>
                <th>#</th>
                <th>Block hash</th>
                <th>Transactions</th>
            </tr>
            <? foreach($this->blockchain as $index => $block) { ?>
                <tr>
                    <td><?=$index+1?> <?=$index == 0 ? "(genesis)" : ""?></td>
                    <td><?=$block->getHash()?></td>
                    <? if($index != 0) { ?>
                    <td>
                        <table style="border-collapse: collapse;">
                            <tr>
                                <th>Sender</th>
                                <th>Receiver</th>
                                <th>Transaction hash</th>
                            </tr>
                            <? foreach($block->getTransactions() as $transaction) {?>
                                <tr>
                                    <td><?=$transaction->getSender()?></td>
                                    <td><?=$transaction->getReceiver()?></td>
                                    <td><?=$transaction->getData()?></td>
                                </tr>
                            <? } ?>
                        </table>
                    </td>
                    <? } else { ?>
                    <td></td>
                    <? } ?>
                </tr>
            <? }  ?>
        </table>
        <strong>Merkle root hash:</strong> <?=$this->getTreeHash()?>
        <br>
        <strong>Your total balance: </strong> <?=$this->balance?> RCoins
        <?
    }
}