<?php
namespace Blockchain\BlockHash;

class BlockHash {
    private $content;
    private $hash;

    function __construct($content) {
        $this->content = $content;
    }

    /**
     * lazy function
     * encrypts the content of this object.
     * @return string the encrypted content of this object.
     */
    function getHash() {
        return $this->hash ?? $this->hash = hash("sha256",$this->content);
    }
}