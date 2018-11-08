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
    
    /**
     *
     * @var connectRegex
     */
    private $_regexp = false;
    
    private $_roundStart = 0;
    
    private $_isIncomplete = false;
    
    public function __construct(Array $playlog, miniPPBuddy &$buddy) {
        $this->_buddy = $buddy;
        $this->_rows = $playlog;
        $this->_regexp = $this->_buddy->getRegexp();
    }
    
    /**
     * Rewind array to first position
     */
    public function rewind() {
        $this->_pos = 0;
    }
    
    /**
     * Return current value
     * @return mixed
     */
    public function current() {
        return $this->_rows[$this->_pos];
    }
    
    /**
     * Return current array key
     * @return integer
     */
    public function key() {
        return $this->_pos;
    }
    
    /**
     * Move forward to next array element
     */
    public function next() {
        ++$this->_pos;
    }
    
    /**
     * Check if value is valid
     * @return boolean
     */
    public function valid() {
        return isset($this->array[$this->_pos]);
    }
    
    /**
     * Set position for row number where new rounds starts
     */
    public function setRoundStart() {
        $this->_roundStart = $this->key();
    }
    
    /**
     * Set true if round is not finished yet
     * @param type $value
     */
    public function isIncomplete($value = false) {
        $this->_isIncomplete = $value;
    }
    
    /**
     * Return values for curl to receive information what to do next
     * @return array
     */
    public function getDetails() {
        return array(
            'current' => $this->key(),
            'rnd' => $this->_roundStart,
            'complete' => ($this->_isIncomplete ? 1 : 0)
        );
    }
    
    /**
     * New connectPlaylog in reverse row order
     * @return \connectPlaylog
     */
    public function copyToReverse() {
        return new connectPlaylog(array_flip($this->_rows), $this->_buddy);
    }
}