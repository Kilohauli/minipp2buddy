<?php

abstract class miniProcessor {
    /**
     * Holds players with points, easier for differentiating zero score 
     * players at the end
     * @var array
     */
    protected $_countedPlayers = array();
    
    /*
     * Team scores, players with scores and other details
     * $var array
     */
    protected $_teams = array();
    
    /**
     *
     * @var miniPPBuddy
     */
    protected $_buddy = false;
    
    /**
     * Presuming that there is always default scoring for positions
     * @var integer
     */
    protected $_scoring = null;
    
    /**
     * Lakes
     * @var array|miniLake
     */
    protected $_lakes = array();
    
    /**
     * Players
     * @var array
     */
    protected $_players = array();
    
    /**
     * Rounds
     * @var array
     */
    protected $_rounds = null;
    
    /**
     * Currently parsed round, starts from 1
     * @var integer
     */
    protected $_currentRound = null;
    
    public function __construct(miniPPBuddy &$buddy) {
        $this->_buddy = $buddy;
        
        $this->_scoring = $this->_buddy->getPoints();
        $this->_lakes = $this->_buddy->getLakes();
        
    }
    
    /**
     * Used to calculate the scores before output # Could remove completely
     * @return array
     */
    abstract protected function calculate();
    
    /**
     * Returns final output
     * Formats array (default) and json
     * @param string $format 
     * @return array|string
     */
    abstract public function output($format);
    
    /**
     * Compares current biggest fish (if set)
     * Used in iteration through all players and their fishes
     * @param array $details
     */
    protected function biggestFish($details, $round) {
        if (empty($this->_rounds[$round]['biggest'])) {
            $this->_rounds[$round]['biggest'] = $details;
            return true;
        }
        
        // Need to check the naming for biggest biggest :)
        $weight = $this->_rounds[$round]['biggest']['weight'];
        if ($details['weight'] > $weight) {
            $this->_rounds[$round]['biggest'] = $details;
        }
    }
    
    protected function iterateFishes($player, $team, $fishes, $rnd = false) {
        if (!$rnd) {
            $rnd = $this->_currentRound;
        }
        if (!is_array($fishes)) {
            return;
        }
        if (!$this->_buddy->getConfigKey('use_biggest_fish')) {
            foreach($fishes as $i => $fish) {
                $this->biggestFish(array(
                    'name' => $player,
                    'team' => $team,
                    'weight' => $fish['biggest'],
                    'fish' => $fish['fish'],
                    'points' => $this->_buddy->getBiggestPoints($rnd)[0]
                ), $rnd);
            }
        } else {
            if (empty($this->_rounds[$rnd]['biggest'])) {
                $biggest = $this->_lakes[$rnd]->getBiggestFish();
                if (!empty($biggest)) {
                    $plrObj = $this->_buddy->getPlayer($biggest['name']);
                    $this->biggestFish(array(
                        'name' => $plrObj->getName(),
                        'team' => $plrObj->getTeam(),
                        'weight' => $biggest['weight'],
                        'fish' => $biggest['fish'],
                        'points' => $this->_buddy->getBiggestPoints($rnd)[0]
                    ), $rnd);
                } else {
                    $this->_rounds[$rnd]['biggest']['name'] = '';
                }
            }
            if (empty($this->_rounds[$rnd]['biggest']['name'])) {
                /* If empty for biggest fish, iterate player fishes 
                 * for this round
                 * Usually caused by "Most species" competition type which 
                 * has nothing in it
                */
                foreach($fishes as $i => $fish) {
                    $this->biggestFish(array(
                        'name' => $player,
                        'team' => $team,
                        'weight' => $fish['biggest'],
                        'fish' => $fish['fish'],
                        'points' => $this->_buddy->getBiggestPoints($rnd)[0]
                    ), $rnd);
                }
            }
        }
    }
    
    /**
     * Return list of biggest fishes
     * @return array
     */
    protected function getBiggestFishes() {
        foreach($this->_rounds as $key => $round) {
            $biggest[$key] = array_merge($round['biggest'], array(
                'team_strip' => $this->_buddy->strip($round['biggest']['team']),
                'name_strip' => $this->_buddy->strip($round['biggest']['name'])
                )
            );
        }
        
        return $biggest;
    }
    
    /**
     * 
     * @param miniPlayer $players
     * @param array $biggest
     */
    protected function addPlayerBiggestPoints($biggest) {
        foreach ($biggest as $rnd => $fish) {
            $this->_players[$fish['name_strip']]['biggest_points'][$rnd] = $fish['points'];
            //$this->_players[$fish['name_strip']]['total'] = $fish['points'];
        }
    }
    
    protected function sortPlayers($sortA, $sortB) {
        if ($sortA['total'] == $sortB['total']) {
            return 0;
        }
        
        return ($sortA['total'] < $sortB['total']) ? +1 : -1;
    }
}
