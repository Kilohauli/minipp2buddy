<?php

class miniPPBuddy {
    protected $config = array();
    
    /**
     * Enables console printing when set to true
     * @var $debug boolean
     */
    private $debug = true;
    
    
    public function __construct($config) {
        
    }
    
    public function getRegexp() {
        
    }
    
    public function getRenderer() {
        
    }

    /**
     * Remove odd characters for array keys
     * Characters include scandic ä, ö, å and so on
     * list can be adjusted when new issue arises, which will arise sooner than later
     */
    public function strip($string) {
        return (string) strtolower(
            str_replace(
                    array("[", "]", 'ä', 'ö', 'å', 'Ä', 'Ö', 'Å'), 
                    array('', '', 'a', 'o', 'a', 'A', 'O', 'A'), 
                        $string));
    }
    
    
}