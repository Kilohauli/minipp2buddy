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
    private $biggestFish = array();
    private $endScores = array();
    private $disqualifiedPlayers = array(); // MOST LIKELY TOBE REMOVED
    
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
        $this->pointsFish = $points[0];
    }
    
    public function setPlayer(miniPlayer &$player) {
        $this->players[] = $player;
    }
    
    public function getPlayers() {
        return $this->players;
    }
    
/*    public function setDisqualifiedPlayer($name) {  MOST LIKELY TO BE REMOVED
        if (!array_key_exists($this->buddy->strip($name), $this->disqualifiedPlayers)) {
            $this->disqualifiedPlayers[$this->buddy->strip($name)] = true;
        }
    }
    */
    
    /**
     * Check if player did not finish. Currently used in end processing to check
     * if players individual fishes should be reported
     * @param string $name
     * @return boolean
     */
    public function dnf($name) {
        if (!array_key_exists($name, $this->endScores)) {
            return true;
        }
        return false;
    }
 
    /**
     * Set biggest for round/lake
     * TODO: Rewrite whole method or replace with "setRealBiggestFish"
     * @param array $biggest
     */
    public function setBiggestFish($biggest) {
        $this->biggestFish = array(
            'key' => $this->buddy->strip($biggest[1]),
            'name' => $biggest[1],
            'fish' => $biggest[2],
            'weight' => $biggest[3]
        );
    }
    
    /**
     * Processes the final results for lake
     * @return array
     */
    public function process() {
        $out = array();
        foreach($this->points as $key => $point) {
            if (!isset($this->players[$key])) {
                /* Quick kill switch if there is no more players in the 
                 * $this->players array */
                break;
            }
            if ($this->players[$key]->getScore($this->round) > 0) {
                $this->endScores[$this->buddy->strip($this->players[$key]->getName())] = array(
                    'name' => $this->players[$key]->getName(),
                    'points' => (int) $point
                );
            }

        }
        // bubblegum fix to biggest fish missing in 'Most species' for now
        if (!empty($this->biggestFish)) {
            if (array_key_exists($this->biggestFish['key'], $this->endScores)) {
            $this->endScores[$this->biggestFish['key']]['points'] += (int) $this->pointsFish;
            } else {
                $this->endScores[$this->biggestFish['key']] = array(
                    'name' => $this->biggestFish['name'],
                    'points' => $this->pointsFish
                );
            }
        }

        return $this->endScores;
    }
    
    public function getOutput() {
        return array(
            'scores' => $this->endScores,
            'currentRound' => $this->round,
            'name' => $this->lakeName,
            'ingametime' => $this->ingameTime,
            'length' => $this->roundLength,
            'type' => $this->gameType,
            'real' => $this->realTime,
            'biggestFishPoints' => $this->pointsFish,
            'BiggestFish' => $this->biggestFish,
        );
    }
    
    /**
     * Get lake details only
     * @return array
     */
    public function getLakeDetail() {
        return array(
            'name' => $this->lakeName,
            'ingametime' => $this->ingameTime,
            'ingametime_season' => $this->buddy->dateToString($this->ingameTime),
            'ingametime_day_time' => $this->buddy->timeToString($this->ingameTime),
            'length' => $this->roundLength,
            'type' => $this->gameType,
            'type_trans' => $this->buddy->translate(trim($this->gameType)),
            'real' => $this->realTime,
        );
    }
    
    public function debug() {
        print_r(array(
            'currentRound' => $this->round,
            'name' => $this->lakeName,
            'ingametime' => $this->ingameTime,
            'length' => $this->roundLength,
            'type' => $this->gameType,
            'real' => $this->realTime,
            'points' => $this->points,
            'biggestFishPoints' => $this->pointsFish,
            'BiggestFish' => $this->biggestFish,
        ));
    }
}