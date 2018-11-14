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
        if (!mb_detect_encoding($fileContents, "UTF-8", true)) {
            $fileContents = utf8_encode($fileContents);
        }
        if ($this->file === null) {
            $this->file = $fileContents;
            $this->currentLine = 0;
            $this->rows = $this->inputToRows($fileContents);
            $this->_playlog = new connectPlaylog($this->rows, $this->buddy);
        }
        return true;
    }
    
    
    public function getReverseRows() {
        return $this->_playlog->copyToReverse();
    }

    public function process() {
        $reverse = $this->getReverseRows();
        $rows = $reverse->getRows();
        $rndStarts = null;
        $rndEnds = null;
        foreach ($rows as $i => $row) {
            $rowType = $this->rowType($row);
            if ($rowType === self::BIGGEST_FISH_MATCH) {
                $rndEnds = $i;
                // Jump over next row that would contain "Isoin kala:"
                continue 2;
                //parent::process();
                break;
            } else if ($rowType === self::NEW_ROUND_MATCH) {
                if ($rndEnds !== null) {
                    $rndStarts = $i;
                }
                break;
            }
        }
        if ($rndStarts !== null && $rndEnds !== null) {
            $this->rows = array_reverse(array_slice($rows, $rndEnds, $rndStarts));
        } else if ($rndStart === null && $rndEnds !== null) {
            // Longer partition from playlog required. Round start not found
        } else if ($rndStart !== null && $rndEnds === null) {
            // Incomplete round
        }
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
                // 
                return self::NEW_ROUND_MATCH;
                break;
            case $this->isBiggestFishPlayer($row) :
            case $this->isBiggestFish($row):
                return self::BIGGEST_FISH_MATCH;
                break;
            default : // All the other rows just skip
                $rowType = false;
                break;
        }
        return $rowType;
    }
    
    protected function isBiggestFishPlayer($row) {
        if (preg_match(self::BIGGEST_FISH_FOR_LAKE, $row)) {
            return true;
        }
        return false;
    }
    
    /**
     * Reset rows to match start and end of round
     * @param integer $start
     * @param integer $end
     */
    protected function resetRows($start, $end) {
        
    }
    /**
     * Slightly modified version of http://www.geekality.net/2011/05/28/php-tail-tackling-large-files/
     * @author Torleif Berger, Lorenzo Stanco
     * @link http://stackoverflow.com/a/15025877/995958
     * @license http://creativecommons.org/licenses/by/3.0/
     * @link https://gist.github.com/lorenzos/1711e81a9162320fde20 original 
     */
    public function tail($filepath, $lines = 1, $adaptive = true) {
           $f = @fopen($filepath, "rb");
           if ($f === false) return false;
           if (!$adaptive) $buffer = 4096;
           else $buffer = ($lines < 2 ? 64 : ($lines < 10 ? 512 : 4096));
           fseek($f, -1, SEEK_END);
           if (fread($f, 1) != "\n") $lines -= 1;
           
           $output = '';
           $chunk = '';

           while (ftell($f) > 0 && $lines >= 0) {
                   $seek = min(ftell($f), $buffer);
                   fseek($f, -$seek, SEEK_CUR);
                   $output = ($chunk = fread($f, $seek)) . $output;
                   fseek($f, -mb_strlen($chunk, '8bit'), SEEK_CUR);
                   $lines -= substr_count($chunk, "\n");
           }

           while ($lines++ < 0) {
                   $output = substr($output, strpos($output, "\n") + 1);
           }
           fclose($f);
           return trim($output);
    }
}