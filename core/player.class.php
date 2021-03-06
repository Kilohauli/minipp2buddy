<?php

class miniPlayer {
    private $buddy = false;
    
    protected $name = '';
    protected $team = null;
    protected $country = '';
    protected $positions = array();
    protected $scores = array();
    protected $fishes = array();
    protected $_wasDisconnected = false;
    
    private $_parsingPlayer = false;
    
    public function __construct(miniPPBuddy &$buddy) {
        $this->buddy = $buddy;
    }
    
    public function setName($name) {
        $this->name = $name;
    }
    
    public function getName() {
        return $this->name;
    }
    
    /**
     * Set team and remove [] characters
     * @param string $team
     */
    public function setTeam($team) {
        $this->team = trim($team, "[] ");
    }
    
    /**
     * 
     * @return boolean|string
     */
    public function getTeam() {
        if ($this->team === null) {
            return false;
        }
        return $this->team;
    }
    
    public function setCountry($country) {
        $this->country = $country;
    }
    
    public function getCountry() {
        return $this->country;
    }
    
    
    /*** NEXT ONES MIGHT CHANGE ***/
    
    /**
     * Set position for lake which is same as current rounds integer for simplicity
     * 
     * @param integer $lake
     * @param integer $position
     */
    public function setPosition($lake, $position) {
        if (strpos($position, '*') === 0) {
            $position = str_replace('*', '', $position);
            $this->_parsingPlayer = true;
        }
        $this->positions[$lake] = $position;
    }
    
    /**
     * Set score for lake which is same as current rounds integer for simplicity
     * 
     * @param integer $lake
     * @param integer $score
     */
    public function setScore($lake, $score) {
        $this->scores[$lake] = $score;
    }
    
    public function getScore($lake) {
        return $this->scores[$lake];
    }
    /**
     * Set individual fish species with their score per lake
     * 
     * @param integer $lake
     * @param array $fish
     */
    public function setFishes($lake, $fish) {
        if (isset($this->fishes[$lake]) && is_array($this->fishes[$lake])) {
            foreach($this->fishes[$lake] as $key => $val) {
                if ($val['fish'] == $fish[1]) {
                    return;
                }
            }
        }
        $this->fishes[$lake][] = array(
            'fish' => $fish[1],
            'amount' => $fish[2],
            'weight' => $fish[3],
            'biggest' => $fish[4]
        );
    }
    
    public function getFishes($lake) {
        if (!isset($this->fishes[$lake])) {
            return false;
        }
        return $this->fishes[$lake];
    }
    
    public function isParser() {
        return $this->_parsingPlayer;
    }
    
    public function setDisconnected() {
        $this->_wasDisconnected = true;
    }
    
    public function debug() {
        print_r(array(
            'name' => $this->name,
            'team' => $this->team,
            'country' => $this->country,
            'scores' => $this->scores,
            'positions' => $this->positions,
            'fishies' => $this->fishes
        ));
    }
    
}