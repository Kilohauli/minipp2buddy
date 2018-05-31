<?php
/**
 * Basically a round for a event/competition
 */
class miniLake {
    private $buddy = false;
    
    public $lakeName = '';
    public $ingameTime = '';
    public $roundLength = '';
    public $gameType = '';
    public $realTime = '';
    public $players = array();
    public $scores = array();
    
    public function __construct(miniPPBuddy &$buddy) {
        $this->buddy = $buddy;
    }
    
    public function setName($name) {
        
    }
    
    public function setIngameTime($time) {
        
    }
    
    public function setGameType($gameType) {
        
    }
    
    public function setRealTime($realTime) {
        
    }
    
    public function setRoundLength($length) {
        
    }
}