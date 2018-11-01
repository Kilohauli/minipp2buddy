<?php

/**
 * connectRegexp extends miniRegexp with added methods and iterable playlog
 * for easier detection of incomplete rounds and what to return back to 
 * bash script
 */
class connectRegexp extends miniRegexp {
    /**
     * Iterable connectPlaylog
     * @var connectPlaylog
     */
    protected $_playlog = null;
    
    public function __construct(\miniPPBuddy &$buddy) {
        parent::__construct($buddy);
        $this->loadIterable();
    }
    
    public function loadIterable() {
        if (!class_exists('connectPlaylog')) {
            require_once BUDDY_ROOT_PATH."buddyconnect/connectPlaylog.class.php";
        }
    }
    
    public function setFile($fileContents) {
        $this->rows = $this->inputToRows($fileContents);
        $this->_playlog = new connectPlaylog($this->_rows, $this->buddy);
    }
    
    /**
     * Identifies what pattern row is
     * @param string $row
     * @return string
     */
    public function rowType($row) {
        
        return $rowType;
    }
}