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
    
    /**
     *
     * @var miniProcessor
     */
    protected $_processor = false;
    
    public function __construct(miniPPBuddy &$buddy, $request) {
        $this->_buddy = $buddy;
        $this->_request = $request;
        
        
    }

    /**
     * Currently quick and dirty processor loading which uses force loading
     * from configuration file property
     * @return array
     */
    public function out() {
        if ($this->_processor === false) {
            $this->getProcessor();
        }
        return $this->_processor->output();
        
    }
    
    /**
     * Will be deprecated more than likely
     * @return array
     */
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
    
    /**
     * Will contain switch->case for valid processors just in case of security
     * But that is just for now
     * @param string $processorName
     * @return miniProcessor
     */
    public function getProcessor($processorName = '') {
        if (!class_exists('miniProcessor')) {
            require_once $this->_buddy->getConfigKey('processor_path') . "miniProcessor.class.php";
        }
        
        if (!empty($this->_buddy->getConfigKey('force_processor'))) {
            $processorName = $this->_buddy->getConfigKey('force_processor');
        }
        
        $this->loadProcessor($processorName);

        $this->_processor = new $processorName($this->_buddy);
        return $this->_processor;
    }
    
    /**
     * 
     * @param string $processorName
     */
    protected function loadProcessor($processorName) {
        if (!class_exists($processorName)) {
            require_once $this->_buddy->getConfigKey('processor_path') . "{$processorName}.class.php";
        }
    }
}