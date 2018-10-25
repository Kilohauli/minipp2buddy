<?php

if (!class_exists('miniProcessor')) {
    require_once dirname(__FILE__) . '/miniProcessor.class.php';
}

class miniTeamProcessor extends miniProcessor {
    protected $_teams = array();
    
    public function __construct(\miniPPBuddy &$buddy) {
        parent::__construct($buddy);
    }
    
    protected function calculate() {
        foreach($this->_lakes as $index => $lake) {
            $processed = $lake->process();
            $this->_currentRound = $index;

            foreach($processed as $key => $player) {
                $plrObj = $this->_buddy->getPlayer($player['name']);
                
                $plrName = $player['name'];
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
                
                $this->addTeamPoints($plrTeamStripped, $player['points']);
                $this->addPlayerPoints($plrObj, $player['points']);
            }
        }
        return $this->_teams;
    }

    public function output($format = 'array') {
        $this->calculate();
        $out = array();
        
        
        if ($format === 'json') {
            $out = json_encode($out);
        }
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
        $teamStripped = $this->_buddy->strip($plrObj->getTeam());
        $playerStripped = $this->_buddy->strip($plrObj->getName());
        $this->_teams[$teamStripped]['players'][$playerStripped]['total'] += (int) $score;
        $this->_teams[$teamStripped]['players'][$playerStripped]['lake_points'][$this->_currentRound] = $score;
        
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
     * 
     * @param string $plrName
     * @param string $teamName
     * @return boolean
     */
    protected function playerExists($plrName, $teamName) {
        $name = $this->_buddy->strip($plrName);
        $team = $this->_buddy->strip($teamName);
        
        return array_key_exists($name, $this->_teams[$team]['players']);
    }
    
    /**
     * Initialises player structure to _teams['players'] array
     * @param miniPlayer $plrObj
     */
    protected function playerSkeleton($plrObj) {
        $teamStripped = $this->_buddy->strip($plrObj->getTeam());
        $plrName = $plrObj->getName();
        $playerStripped = $this->_buddy->strip($plrName);
        $this->_teams[$teamStripped]['players'][$playerStripped] = array(
            'name' => $plrName,
            'total' => 0,
            'lake_points' => array()
        );
    }
}