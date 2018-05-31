<?php
/**
 * Basically a round for a event/competition
 */
class miniLake {
    private $buddy = false;
    
    public $lakeName = '';
    public $ingameTime = '';
    public $gameType = '';
    public $realTime = '';
    public $players = array();
    public $scores = array();
    
    public function __construct(miniPPBuddy &$buddy) {
        $this->buddy = $buddy;
    }
}