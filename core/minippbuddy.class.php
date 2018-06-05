<?php

class miniPPBuddy {
    protected $config = array();
    
    protected $regex = null;
    
    /**
     * Enables console printing when set to true
     * @var $debug boolean
     */
    private $debug = false;
    
    /**
     * Lake objects which in simplicity are rounds
     * @var array
     */
    private $lakes = array();
    
    private $currentRound = 0;
    
    public function __construct($config) {
        
    }
    
    public function getRegexp() {
        if ($this->regex !== null) {
            return $this->regex;
        }
        
        require_once dirname(__FILE__) . '/regexp.class.php';
        $this->regex = new miniRegexp($this);
        return $this->regex;
    }
    
    public function getRenderer() {
        
    }

    /**
     * Remove odd characters for array keys
     * Characters include scandic ä, ö, å and so on
     * list can be adjusted when new issue arises, which will arise sooner than later
     */
    public function strip($string) {
        return (string) strtolower(
            str_replace(
                    array("[", "]", 'ä', 'ö', 'å', 'Ä', 'Ö', 'Å', ' ', '(', ')', '-'), 
                    array('', '', 'a', 'o', 'a', 'A', 'O', 'A', '', '', '', '_'), 
                        $string));
    }
    
    public function setDebug(boolean $debug) {
        $this->debug = $debug;
    }
    
    public function isDebug() {
        return $this->debug();
    }
    
    /**
     * 
     * @return miniLake
     */
    public function newLake() {
        if (!class_exists('miniLake')) {
            require_once dirname(__FILE__) . '/lake.class.php';
        }
        $this->currentRound++;
        $this->lakes[$this->currentRound] = new miniLake($this);
        return $this->lakes[$this->currentRound];
    }
    
    public function getRound() {
        return $this->currentRound;
    }
    
    public function newPlayer($name) {
        if (!class_exists('miniPlayer')) {
            require_once dirname(__FILE__) . '/player.class.php';
        }
        
    }
    
    public function getPlayer($id) {
        
    }
    
    public function playerId($name) {
        
    }
}