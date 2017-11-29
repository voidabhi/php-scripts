<?php
class Trie {
    private $_root;
    public function __construct() {
        $this->_root = new TrieNode();
    }
    public function add($word) {
        $current = $this->_root;
        for ($i = 0; $i < strlen($word); $i++) {
            $chr = $word[$i];
            $node = $current->get($chr);
            if (is_null($node)) {
                $node = new TrieNode();
                $current->addNode($chr, $node);
            }
            $current = $node;
        }
        $current->setEndOfWord();
    }
    public function hasWord($word) {
        $current = $this->_root;
        for ($i = 0; $i < strlen($word); $i++) {
            $chr = $word[$i];
            $node = $current->get($chr);
            if ($node == null) {
                return false;
            }
            $current = $node;
        }
        return $current->isEndOfWord();
    }
    public function countPrefixWords($prefix) {
        $current = $this->_root;
        $count = 0;
        foreach (str_split($prefix) as $i => $chr) {
            $node = $current->get($chr);
            if ($node == null) {
                return 0;
            }
            $current = $node;
        }
        $children = $current->getChildren();
        return $this->walkAndCountWordEnds($children);
    }
    public function walkAndCountWordEnds($array) {
        $count = 0;
        foreach( $array as $item ) {
            if ($item->isEndOfWord()) {
                $count++;
            } else {
                $count += $this->walkAndCountWordEnds($item->getChildren());
            }
        }
        return $count;
    }
class Trie {
    private $_root;
    public function __construct() {
        $this->_root = new TrieNode();
    }
    public function add($word) {
        $current = $this->_root;
        for ($i = 0; $i < strlen($word); $i++) {
            $chr = $word[$i];
            $node = $current->get($chr);
            if (is_null($node)) {
                $node = new TrieNode();
                $current->addNode($chr, $node);
            }
            $current = $node;
        }
        $current->setEndOfWord();
    }
    public function hasWord($word) {
        $current = $this->_root;
        for ($i = 0; $i < strlen($word); $i++) {
            $chr = $word[$i];
            $node = $current->get($chr);
            if ($node == null) {
                return false;
            }
            $current = $node;
        }
        return $current->isEndOfWord();
    }
    public function countPrefixWords($prefix) {
        $current = $this->_root;
        $count = 0;
        foreach (str_split($prefix) as $i => $chr) {
            $node = $current->get($chr);
            if ($node == null) {
                return 0;
            }
            $current = $node;
        }
        $count += $current->isEndOfWord() ? 1 : 0;
        $children = $current->getChildren();
        return $count +$this->walkAndCountWordEnds($children);
    }
    public function walkAndCountWordEnds($array) {
        $count = 0;
        foreach( $array as $item ) {
            if ($item->isEndOfWord()) {
                $count++;
            }
            $count += $this->walkAndCountWordEnds($item->getChildren());
        }
        return $count;
    }
    public function printOut() {
        print_r($this->_root);
    }
}
class TrieNode {
    protected $_children = [];
    protected $_endOfWord = false;
    public function has($chr) {
        return isset($this->_children[$chr]);
    }
    public function get($chr) {
        if ($this->has($chr)) {
            return $this->_children[$chr];
        }
        return null;
    }
    public function addNode($chr, TrieNode $node) {
        $this->_children[$chr] = $node;
    }
    public function setEndOfWord() {
        $this->_endOfWord = true;
    }
    public function isEndOfWord() {
        return (bool)$this->_endOfWord;
    }
    public function getChildren() {
        return $this->_children;
    }
    public function hasChildren() {
        return count($this->_children) > 0;
    }
}
$trie = new Trie();
$trie->add("happy");
$trie->add("ham");
$trie->add("hart");
var_export($trie->countPrefixWords("ha")); // 3
var_export($trie->countPrefixWords("hap")); // 1
var_export($trie->countPrefixWords("hat")); // 0
