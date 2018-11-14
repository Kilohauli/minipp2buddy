<?php

if (!class_exists('miniProcessor')) {
    require_once dirname(__FILE__) . '/miniProcessor.class.php';
}

class miniPlayerRanking extends miniProcessor {
    
    public function __construct(\miniPPBuddy &$buddy) {
        parent::__construct($buddy);
    }
    
    protected function calculate() {}
    
    public function output($format = 'array') {
        $out = array();
        if ($format === 'json') {
            $temp = json_encode($out);
        }
        
        return $out;
    }
    
    /**
     * Store values from miniPlayerRanking::output() to database
     * Will be implemented later on when database design and orm/orb is known
     */
    public function store() {
        
    }
    
    /**
     * 
     * @param miniPlayer $plrObj
     * @param integer $score
     */
    protected function addPlayerPoints($plrObj, $points, $rnd = false) {
        if (!$rnd) {
            $rnd = $this->_currentRound;
        }
        $playerStripped = $this->_buddy->strip($plrObj->getName());
        $this->_players[$playerStripped]['total'] += (int) $points;
        $this->_players[$playerStripped]['lake_points'][$rnd] = $points;
        $this->_players[$playerStripped]['lake_score'][$rnd] = $plrObj->getScore($rnd);
        
    }
    
    /**
     * 
     * @param string $plrName
     * @return boolean
     */
    protected function playerExists($plrName) {
        $name = $this->_buddy->strip($plrName);
        return array_key_exists($name, $this->_players);
    }
    
    /**
     * Initialises player structure to _teams['players'] array
     * @param miniPlayer $plrObj
     */
    protected function playerSkeleton($plrObj) {
        $plrName = $plrObj->getName();
        $playerStripped = $this->_buddy->strip($plrName);
        if ($this->playerExists($plrName)) {
            return true;
        }

        $this->_players[$playerStripped] = array(
            'name' => $plrName,
            'total' => 0,
            'played' => array(),
            'lake_points' => array(),
            'lake_score' => array(),
            'fishes' => array()
        );
    }
    
        
    protected function getZeroScoringPlayers() {
        $allPlayers = $this->_buddy->getAllPlayers();

        foreach($allPlayers as $key => $plrObj) {
            if (!array_key_exists($key, $this->_countedPlayers)) {
                $players[] = $plrObj;
            }
        }
        return $players;
    }
    
    protected function playedLake($plrObj, $rnd = false) {
        if (!$rnd) {
            $rnd = $this->_currentRound;
        }

        $playerStripped = $this->_buddy->strip($plrObj->getName());
        $this->_players[$playerStripped]['played'][$rnd] = 1;
    }
}