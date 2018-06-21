<?php
/**
 * Basically a round for a event/competition
 */
class miniLake {
    private $buddy = false;
    
    private $lakeName = '';
    private $ingameTime = '';
    private $roundLength = '';
    private $gameType = '';
    private $realTime = '';
    private $players = array();
    private $round = 0;
    
    /**
     * There is no actual round objects as lake does the same job, the scores
     * from web configuration are passed directly to lake
     * @var array Points for positions
     */
    private $points = array();
    
    /**
     *
     * @var int Points for biggest fish
     */
    private $pointsFish = 0;
    /**
     * @var array Actual scores for round per player
     */
    private $scores = array();
    
    
    public function __construct(miniPPBuddy &$buddy) {
        $this->buddy = $buddy;
    }
    
    public function setName($name) {
        $this->lakeName = $name;
    }
    
    public function setIngameTime($time) {
        $this->ingameTime = $time;
    }
    
    public function setGameType($gameType) {
        $this->gameType = $gameType;
    }
    
    public function setRealTime($realTime) {
        $this->realTime = $realTime;
    }
    
    public function setRoundLength($length) {
        $this->roundLength = $length;
    }
    
    public function setRoundNumber($round) {
        $this->round = $round;
    }
    
    public function setPoints($points) {
        $this->points = $points;
    }
    
    public function setBiggestPoints($points) {
        $this->pointsFish = $points;
    }
    
    public function setPlayer(miniPlayer &$player) {
        $this->players[] = $player;
    }
    
    public function getPlayers() {
        return $this->players;
    }
    
    public function debug() {
        print_r(array(
            'currentRound' => $this->round,
            'name' => $this->lakeName,
            'ingametime' => $this->ingameTime,
            'length' => $this->roundLength,
            'type' => $this->gameType,
            'real' => $this->realTime
        ));
    }
}