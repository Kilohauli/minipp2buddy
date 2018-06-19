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
    
    private $players = array();
    
    private $biggestFishes = array();
    
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
    
    public function debug() {
        print_r(array(
            array_keys($this->lakes),
            array_keys($this->players),
            $this->biggestFishes
        ));
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
    
    /**
     * 
     * @param string $name
     * @return miniPlayer
     */
    public function newPlayer($name) {
        $stripped = $this->strip($name);
        if (!class_exists('miniPlayer')) {
            require_once dirname(__FILE__) . '/player.class.php';
        }
        $this->players[$stripped] = new miniPlayer($this);
        return $this->players[$stripped];
    }
    
    public function getPlayer($name) {
        $stripped = $this->strip($name);
        if (!array_key_exists($stripped, $this->players)) {
            return false;
        }
        return $this->players[$stripped];;
    }
    
    public function setBiggestFish($lake, $name, $fish) {
        $this->biggestFishes[$lake] = array(
            'player' => $this->strip($name),
            'fish' => $fish[2],
            'weight' => $fish[3]
        );
    }
}