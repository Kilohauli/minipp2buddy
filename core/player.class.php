<?php

class miniPlayer {
    private $buddy = false;
    
    protected $name = '';
    protected $team = '';
    protected $country = '';
    protected $positions = array();
    protected $scores = array();
    
    public function __construct(miniPPBuddy &$buddy) {
        $this->buddy = $buddy;
    }
    
    public function setName($name) {
        $this->name = $name;
    }
    
    public function setTeam($team) {
        $this->team = $team;
    }
    
    public function setCountry($country) {
        $this->country = $country;
    }
    
    public function setPosition($lake, $position) {
        $this->positions[] = $position;
    }
    
    public function setScore($lake, $score) {
        $this->scores[] = $score;
    }
    
}