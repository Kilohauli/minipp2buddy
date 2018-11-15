<?php

class miniPiece {
    private $_buddy = '';
    
    public function __construct(miniPPBuddy &$buddy, $pieceVariables = array()) {
        $this->_buddy = $buddy;
    }
    
    public function debug() {
        
    }
}