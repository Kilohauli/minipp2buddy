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
    
    /**
     * Iterable connectPlaylog in reversed row order
     * @var connectPlaylog
     */
    protected $_playlogReverse = null;
    
    // New numbers where to start just to keep numbering clear from parent
    /**
     * New round was found
     */
    const NEW_ROUND_MATCH = 101;
    
    /**
     * Biggest fish was found
     */
    const BIGGEST_FISH_MATCH = 102;
    
    public function __construct(miniPPBuddy &$buddy) {
        parent::__construct($buddy);
        $this->loadIterable();
    }
    
    public function loadIterable() {
        if (!class_exists('connectPlaylog')) {
            require_once BUDDY_ROOT_PATH."buddyconnect/connectPlaylog.class.php";
        }
    }
    
    public function setFile($fileContents) {
        if ($this->file === null) {
            $this->file = $fileContents;
            $this->currentLine = 0;
            $this->rows = $this->inputToRows($fileContents);
            $this->_playlog = new connectPlaylog($this->rows, $this->buddy);
        }
        return true;
    }
    
    
    public function getReverseRows() {
        $this->_playlogReverse = $this->_playlog->copyToReverse();
    }

    public function iterate() {
        
    }
    /**
     * Identifies what pattern row is
     * Constants do not match exactly what is wanted but 
     * @param string $row
     * @return integer|boolean
     */
    public function rowType($row) {
        switch (true) {
            case $this->isNewRound($row):
                $rowType = self::NEW_ROUND_MATCH;
                break;
            case $this->isBiggestFish($row):
                $rowType = self::BIGGEST_FISH_MATCH;
                break;
            default : // All the other rows just skip
                $rowType = false;
                break;
        }
        return $rowType;
    }
}