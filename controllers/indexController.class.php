<?php

class ppIndexController {
    /**
     *
     * @var miniPPBuddy
     */
    protected $_buddy = '';
    
    protected $_processed = '';
    protected $_request = array();
    protected $_headers = array();
    protected $_variables = array();
    
    protected $_lakeOutput = array();
    
    protected $_includeFishes = true;
    
    public function __construct(miniPPBuddy &$buddy, $request) {
        $this->_buddy = $buddy;
        $this->_request = $request;
    }
    
    public function out() {
        
    }
    
    public function processLakesOutput() {
        $lakes = $this->_buddy->getLakes();
        
        foreach($lakes as $key => $lake) {
            $this->_lakeOutput[$key] = $lake->getOutput();
            if ($this->_includeFishes === true) {
                $players = $lake->getPlayers();
                foreach($players as $plrKey => $player) {
                    if ($lake->dnf($this->_buddy->strip($player->getName()))) {
                        continue;
                    }
                    $this->_lakeOutput[$key]['player_fishes'][$this->_buddy->strip($player->getName())] = $player->getFishes($key);
                }

            }
        }

        return $this->_lakeOutput;
    }
}