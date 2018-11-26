<?php

if (!class_exists('miniProcessor')) {
    require_once dirname(__FILE__) . '/miniProcessor.class.php';
}

class miniPlayerRanking extends miniProcessor {
    
    public function __construct(\miniPPBuddy &$buddy) {
        parent::__construct($buddy);
    }
    
    protected function calculate() {
        $plrTemp = array();
        foreach($this->_lakes as $index => $lake) {
            $processed = $lake->process();
            $this->_currentRound = $index;
            foreach($processed as $key => $player) {
                if(!$player['name']) {
                    continue;
                }
                $plrName = $player['name'];
                $plrTemp[$this->_currentRound][] = $this->_buddy->strip($plrName);
                $plrObj = $this->_buddy->getPlayer($player['name']);
                $plrTeam = $plrObj->getTeam();
                $plrTeamStripped = $this->_buddy->strip($plrTeam);

                // To get biggest fish for the round
                $this->iterateFishes($plrName, $plrTeam, $plrObj->getFishes($index));

                if (!$this->playerExists($plrName, $plrTeam)) {
                    $this->playerSkeleton($plrObj);
                }
                $this->addPlayerTeam($plrObj);
                $this->addPlayerCountry($plrObj);
                $this->addPlayerLakeResult($plrObj);
                $this->addPlayerFishes($plrObj);
                $this->addPlayerPoints($plrObj, $player['points']);
                $this->addPlayerTags($plrObj);
                $this->playedLake($plrObj);
            }
        }

        foreach($this->_lakes as $index => $lake) {
            $players = $lake->getPlayers();
            foreach($players as $key => $plrObj) {

                $playerStripped = $this->_buddy->strip($plrObj->getName());
                if (in_array($playerStripped, $plrTemp[$index]) && $this->playerSkeleton($plrObj)) {
                    continue;
                }
                $this->playerSkeleton($plrObj);
                $this->addPlayerLakeResult($plrObj, $index);
                $this->addPlayerFishes($plrObj, $index);
                $this->playedLake($plrObj, $index);
                $this->iterateFishes($plrObj->getName(), $plrObj->getTeam(), $plrObj->getFishes($index), $index);
            } 
        }
        return $this->_players;
    }
    
    public function output($format = 'array') {
        $this->calculate();
        uasort($this->_players, array($this, 'sortPlayers'));
        foreach ($this->_lakes as $rnd => $lake) {
            $out['lakes'][$rnd] = $lake->getLakeDetail();
        }
        $out['biggest'] = $this->getBiggestFishes();
        $out['players'] = &$this->_players;
       $this->addPlayerBiggestPoints($out['biggest']);
        
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
    
    protected function addPlayerLakeResult($plrObj, $rnd = '') {
        $playerStripped = $this->_buddy->strip($plrObj->getName());

        if ($rnd == '') {
            $rnd = $this->_currentRound;
        }

        $this->_players[$playerStripped]['lake_score'][$rnd] = $plrObj->getScore($rnd);
    }
    
    /**
     * Add fishes to players array
     * @param type $plrObj
     */
    protected function addPlayerFishes($plrObj, $rnd = false) {
        $playerStripped = $this->_buddy->strip($plrObj->getName());
        
        if (!$rnd) {
            $rnd = $this->_currentRound;
        }

        $this->_players[$playerStripped]['fishes'][$rnd] = $plrObj->getFishes($rnd);
    }
    
    /**
     * Add team to player
     * @param type $plrObj
     */
    protected function addPlayerTeam($plrObj) {
        $nameStripped = $this->_buddy->strip($plrObj->getName());
        if (empty($this->_players[$nameStripped]['team'])) {
            $this->_players[$nameStripped]['team'] = $plrObj->getTeam();
        }
    }
    
    /**
     * Add country to player
     * @param type $plrObj
     */
    protected function addPlayerCountry($plrObj) {
        $nameStripped = $this->_buddy->strip($plrObj->getName());
        if (empty($this->_players[$nameStripped]['country'])) {
            $this->_players[$nameStripped]['country'] = $plrObj->getCountry();
        }
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
    
    protected function addPlayerTags($plrObj) {
        if ($plrObj->getTags()) {
            $nameStripped = $this->_buddy->strip($plrObj->getName());
            $this->_players[$nameStripped]['tags'] = $plrObj->getTags();
        }
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
            'team' => '',
            'country' => '',
            'total' => 0,
            'played' => array(),
            'lake_points' => array(),
            'lake_score' => array(),
            'biggest_points' => array(),
            'fishes' => array(),
            'tags' => ''
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