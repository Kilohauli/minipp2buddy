<?php

class miniTemplate {
    private $_buddy = '';
    private $_templateVariables = array();
    private $_pieces = array();
    private $_template = '';
    private $_templateName = '';
    
    const PIECE = '/\[\[[a-zA-Z0-9]\]\]/';
    
    public function __construct(miniPPBuddy &$buddy, $templateName = '', $templateVariables = array()) {
        $this->_buddy = $buddy;
        $this->_templateVariables= $templateVariables;
        $this->_templateName = $templateName;
    }
    
    public function process() {
        
    }

    public function loadTemplate() {
        
    }
    
    public function getPieces() {
        $matches = array();
        preg_match(self::PIECE, $this->_template);
    }
    
    public function parsePieces() {
        
    }
    
    public function debug() {
        
    }
}