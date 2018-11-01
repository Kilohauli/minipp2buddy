<?php

if (!class_exists('miniProcessor')) {
    require_once dirname(__FILE__) . '/miniProcessor.class.php';
}

class miniFinnishLeague extends miniProcessor {
    
    /*
     * Team scores, players with scores and other details
     * $var array
     */
    protected $_teams = array();
    
    /**
     * Holds players with points, easier for differentiating zero score 
     * players at the end
     * @var array
     */
    protected $_countedPlayers = array();
    
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
                
                if (!$this->teamExists($plrTeamStripped)) {
                    $this->teamSkeleton($plrObj);
                }
                
                if (!$this->playerExists($plrName, $plrTeam)) {
                    $this->playerSkeleton($plrObj);
                }

                $this->addPlayerLakeResult($plrObj);
                $this->addPlayerFishes($plrObj);
                $this->addTeamPoints($plrTeamStripped, $player['points']);
                $this->addPlayerPoints($plrObj, $player['points']);
                $this->playedLake($plrObj);
            }
        }
        //$zeroScoringPlayers = $this->getZeroScoringPlayers();
        
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
        return $this->_teams;
    }

    protected function calculateLeagueScore($teamKeys, $round) { 
        ksort($teamKeys);
        
        $scores = array(
            $teamKeys[0] => $this->_teams[$teamKeys[0]]['lake_points'][$round],
            $teamKeys[1] => $this->_teams[$teamKeys[1]]['lake_points'][$round]
        );
        arsort($scores);

        // reset the teamKeys so 0 matches higher score
        $teamKeys = array_keys($scores);
        
        if ($scores[$teamKeys[0]] == $scores[$teamKeys[1]]) {
            $final = array(
                $teamKeys[0] => 1,
                $teamKeys[1] => 1
            );
        } else {
            $final = array(
                $teamKeys[0] => 2,
                $teamKeys[1] => 0
            );
        }
        
        // Maintain original key order
        ksort($final);
        return $final;
    }

    public function output($format = 'array') {
        $this->calculate();

        $teamKeys = array_keys($this->_teams);

        // Output array which collects all the data
        $temp = array();
        $finalScore = array(
            $teamKeys[0] => 0,
            $teamKeys[1] => 0
        );

        
        foreach($this->_lakes as $index => $lake) {
            $temp['lakes'][$index] = array(
                'league_score' => $this->calculateLeagueScore($teamKeys, $index),
                'normal_score' => array(
                    $teamKeys[0] => $this->_teams[$teamKeys[0]]['lake_points'][$index],
                    $teamKeys[1] => $this->_teams[$teamKeys[1]]['lake_points'][$index]
                )
            );
            $finalScore[$teamKeys[0]] += (int) $temp['lakes'][$index]['league_score'][$teamKeys[0]];
            $finalScore[$teamKeys[1]] += (int) $temp['lakes'][$index]['league_score'][$teamKeys[1]];
            
            $temp['lakes'][$index] = array_merge($temp['lakes'][$index], $lake->getLakeDetail());
        }
        $temp['final_score'] = $finalScore;
        $temp['team_names'] = array(
            $teamKeys[0] => $this->_teams[$teamKeys[0]]['name'],
            $teamKeys[1] => $this->_teams[$teamKeys[1]]['name']
        );
        $temp['biggest'] = $this->getBiggestFishes();
        // Sort players by their total score
        uasort($this->_teams[$teamKeys[0]]['players'], array($this, 'sortPlayers'));
        uasort($this->_teams[$teamKeys[1]]['players'], array($this, 'sortPlayers'));
                
        $temp['players'][$teamKeys[0]] = $this->_teams[$teamKeys[0]]['players'];
        $temp['players'][$teamKeys[1]] = $this->_teams[$teamKeys[1]]['players'];
        
        if ($format === 'json') {
            $temp = json_encode($temp);
        }
        
        return $temp;
    }
    
    /**
     * Add points to teams total score
     * Skeleton has been formed and has 0 as base so easy to add
     * @param string $teamStripped
     * @param integer $score
     */
    protected function addTeamPoints($teamStripped, $score) {
        $this->_teams[$teamStripped]['total'] += $score;
        if (!array_key_exists($this->_currentRound, $this->_teams[$teamStripped]['lake_points'])) {
            $this->_teams[$teamStripped]['lake_points'][$this->_currentRound] = $score;
        } else {
            $this->_teams[$teamStripped]['lake_points'][$this->_currentRound] += $score;
        }
    }
    
    /**
     * 
     * @param miniPlayer $plrObj
     * @param integer $score
     */
    protected function addPlayerPoints($plrObj, $score) {
        // Some players might have just 0 for biggest fish as score
        if ($score == 0) {
            return;
        }
        $teamStripped = $this->_buddy->strip($plrObj->getTeam());
        $playerStripped = $this->_buddy->strip($plrObj->getName());

        $this->_teams[$teamStripped]['players'][$playerStripped]['total'] += (int) $score;
        $this->_teams[$teamStripped]['players'][$playerStripped]['lake_points'][$this->_currentRound] = $score;
        
    }
    
    /**
     * Add fishes to players array
     * @param type $plrObj
     */
    protected function addPlayerFishes($plrObj, $rnd = '') {
        $teamStripped = $this->_buddy->strip($plrObj->getTeam());
        $playerStripped = $this->_buddy->strip($plrObj->getName());
        if ($rnd == '') {
            $rnd = $this->_currentRound;
        }

        $this->_teams[$teamStripped]['players'][$playerStripped]['fishes'][$rnd] = $plrObj->getFishes($rnd);
    }
    
    protected function addPlayerLakeResult($plrObj, $rnd = '') {
        $teamStripped = $this->_buddy->strip($plrObj->getTeam());
        $playerStripped = $this->_buddy->strip($plrObj->getName());

        if ($rnd == '') {
            $rnd = $this->_currentRound;
        }

        $this->_teams[$teamStripped]['players'][$playerStripped]['lake_score'][$rnd] = $plrObj->getScore($rnd);
    }
    /**
     * Check if team is in the _team array
     * @param string $teamStripped
     * @return boolean
     */
    protected function teamExists($teamStripped) {
        return array_key_exists($teamStripped, $this->_teams);
    }
    
    /**
     * Initialises team structure to _teams array
     * @param miniPlayer $plrObj
     */
    protected function teamSkeleton($plrObj) {
        $teamName = $plrObj->getTeam();
        $teamStripped = $this->_buddy->strip($teamName);
        
        $this->_teams[$teamStripped] = array(
            'name' => $teamName,
            'total' => 0,
            'lake_points' => array(),
            'players' => array()
        );
    }
    
    /**
     * Check if player exists in teams, also add to _players if not found
     * @param string $plrName
     * @param string $teamName
     * @return boolean
     */
    protected function playerExists($plrName, $teamName) {
        $name = $this->_buddy->strip($plrName);
        $team = $this->_buddy->strip($teamName);
        
        if (!array_key_exists($name, $this->_countedPlayers)) {
            $this->_countedPlayers[$name] = $team;
        }
        
        return array_key_exists($name, $this->_teams[$team]['players']) ? true : false;
    }
    
    /**
     * Initialises player structure to _teams['players'] array
     * @param miniPlayer $plrObj
     */
    protected function playerSkeleton($plrObj) {
        $plrName = $plrObj->getName();
        $playerStripped = $this->_buddy->strip($plrName);
        $teamStripped = $this->_buddy->strip($plrObj->getTeam());
        if ($this->playerExists($plrName, $plrObj->getTeam())) {
            return true;
        }

        $this->_teams[$teamStripped]['players'][$playerStripped] = array(
            'name' => $plrName,
            'total' => 0,
            'played' => array_fill(1, 3, 0),
            'lake_points' => array_fill(1, 3, 0),
            'lake_score' => array_fill(1, 3, 0),
            'fishes' => array()
        );
        return $this->_teams[$teamStripped]['players'][$playerStripped];
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
        $teamStripped = $this->_buddy->strip($plrObj->getTeam());
        $this->_teams[$teamStripped]['players'][$playerStripped]['played'][$rnd] = 1;
    }
}