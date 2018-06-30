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
    private $disqualifiedPlayers = array(); // MOST LIKELY TO BE REMOVED
    
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
    
    public function isDisqualified($name) {
        if (array_key_exists($this->buddy->strip($name), $this->disqualifiedPlayers)) {
            return true;
        }
        return false;
    }
 */
    /**
     * Set biggest for round/lake
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
        if (array_key_exists($this->biggestFish['key'], $this->endScores)) {
            $this->endScores[$this->biggestFish['key']]['points'] += (int) $this->pointsFish;
        } else {
            $this->endScores[$this->biggestFish['key']] = array(
                'name' => $this->biggestFish['name'],
                'points' => $this->pointsFish
            );
        }
        return $this->endScores;
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