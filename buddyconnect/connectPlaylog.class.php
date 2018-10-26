<?php

class connectPlaylog implements Iterator {
    private $_pos = 0;
    
    /**
     * Playlog rows
     * @var array
     */
    private $_rows = array();
    
    /**
     *
     * @var miniPPBuddy
     */
    private $_buddy = false;
    
    private $_config = array();
    
    private $_roundStart = 0;
    
    private $_isIncomplete = false;
    
    public function __construct(Array $playlog, miniPPBuddy &$buddy) {
        $this->_buddy = $buddy;
        $this->_rows = $playlog;
    }
    
    public function rewind() {
        $this->_pos = 0;
    }
    
    public function current() {
        return $this->_pos;
    }
    
    public function key() {
        return $this->_pos;
    }
    
    public function next() {
        ++$this->_pos;
    }
    
    public function valid() {
        return isset($this->array[$this->_pos]);
    }
    
    public function setRoundStart() {
        $this->_roundStart = $this->key();
    }
    
    public function isIncomplete($value = false) {
        $this->_isIncomplete = $value;
    }
    
    public function toArray() {
        return array(
            'current' => $this->key(),
            'rnd' => $this->_roundStart,
            'complete' => ($this->_isIncomplete ? 1 : 0)
        );
    }
}