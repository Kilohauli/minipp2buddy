<?php

abstract class miniProcessor {
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
    protected $_lakes = null;
    
    /**
     * Players
     * @var array
     */
    protected $_players = null;
    
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
     * Used to calculate the scores before output
     * @return array
     */
    abstract protected function calculate();
    
    /**
     * Returns final output
     * Formats array (default) and json
     * @param string $format 
     * @return array|string
     */
    abstract protected function output($format);
    
    /**
     * Compares current biggest fish (if set)
     * Used in iteration through all players and their fishes
     * @param array $details
     */
    protected function biggestFish($details) {
        if (empty($this->_rounds[$this->_currentRound]['biggest'])) {
            $this->_rounds[$this->_currentRound]['biggest'] = $details;
            return true;
        }
        
        // Need to check the naming for biggest biggest :)
        $weight = $this->_rounds[$this->_currentRound]['biggest']['weight'];
        if ($details['weight'] > $weight) {
            $this->_rounds[$this->_currentRound]['biggest'] = $details;
        }
    }
    
    protected function iterateFishes($player, $team, $fishes) {
        foreach($fishes as $i => $fish) {
            $this->biggestFish(array(
                'name' => $player,
                'team' => $team,
                'weight' => $fish['biggest'],
                'fish' => $fish['fish']
            ));
        }
    }
    
    /**
     * Return list of biggest fishes
     * @return array
     */
    protected function getBiggestFishes() {
        foreach($this->_rounds as $key => $round) {
            $biggest[] = array_merge($round['biggest'], array('team_strip' => $this->_buddy->strip($round['biggest']['team'])));
        }
        
        return $biggest;
    }
    
    protected function sortPlayers($sortA, $sortB) {
        if ($sortA['total'] == $sortB['total']) {
            return 0;
        }
        
        return ($sortA['total'] < $sortB['total']) ? +1 : -1;
    }
}
