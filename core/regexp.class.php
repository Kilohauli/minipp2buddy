<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class miniRegexp {
    private $buddy = false;
    
    private $currentLine = 0;
    
    /**
     * Next constants use strpos() to match beginning of line
     */
    // Catches biggest fish heading
    const BIGGEST_FISH = 'Isoin kala:';
    // Catches lake, time and so on from server side playlog
    const NEW_ROUND_HOST = 'New LAN SERVER';
    // Catches lake, time and so on from client side playlog
    const NEW_ROUND_SELF = 'New LAN CLIENT';
    // Begin row processing after this row
    const FINISHED = 'Competition finished';
    // Skip plenty of unnecessary rows through the file
    const SKIP_OWN = 'Omat kalat:';
    // Joining players
    const JOIN = '-->';
    // Leaving players
    const LEAVE = '<--';
    // Skip new game line
    const NEW_GAME = '---------------';
    // Break out from whole loop when game abandoned
    const ABANDON = 'Competition aborted';
    
    /** 
     * Next constants are for regular expressions 
     */
    // Disqualified players (Failed to return to finish)
    const DISQUALIFIED = '/\(disq\)$/';
    
    const RESULT = '/^(\*\d+|\d+)\.\s/';
    
    /** Lake information with resulting array keys
     * 0 = whole line
     * 1 = lake name
     * 2 = in-game date and time
     * 3 = length of round
     * 4 = game type
     * 5 = real time
    */
    const LAKE_INFORMATION = '/^[\s\w]*:\s+([\s\w-äöÄÖÅ]*)\.\s+\((\d+\.\d+\.\s\d+:\d+)\/\s+(\d+)\s+min\s*\/\s+\w+\s\/\s+([\w+\s+,]*)\s+\/[\s\w+]*\)\s+\[([0-9]+\.[0-9]+\.[0-9]+\s[0-9]+:[0-9]+)\]/';
    
    /** Player information including with the country tag after name, was too lazy to build too complex regexp for now
     * 0 = whole line
     * 1 = result position
     * 2 = Team tag (could be empty '')
     * 3 = Name with the country tag (if there is one)
     * 4 = score
     */
    const PLAYER_INFORMATION = '/(\*\d+|\d+)\.\s+(\[.*\]\s){0,1}(.+)\s+(\d+)\s+g/';
    
    /** Player country
     * 0 = whole line
     * 1 = country code
     */
    const PLAYER_COUNTRY = '/\[(.*)\]\s+$/';
    
    /**
     * Fishes player name
     * Used in two cases, stripping out the name and when testing if the row is the player name or fish result
     */
    const FISHES_PLAYER_NAME = '/^(.*)(:)$/';
    
    /**
     * Individual fish row
     * 0 = whole line
     * 1 = Fish
     * 2 = How many
     * 3 = Total weight
     * 4 = Biggest
     */
    const FISHES_FOR_PLAYER = '/([a-zA-ZäöåÄÖÅ]*)\s+[a-zA-ZäöåÄÖÅ]+:\s+(\d+)\s+[a-zA-Z]+:\s+(\d+)\s+\([a-zA-Z]+:\s(\d+)\s+g\)/';
    
    /**
     * Biggest fish for the lake
     * 0 = whole line
     * 1 = player name
     * 2 = fish
     * 3 = weight
     */
    const BIGGEST_FISH_FOR_LAKE = '/(.*)\s\s([a-zA-ZäöåÄÖÅ]*)\s+(\d+)\s+g/';
    
    /*
     * Which part of round currently in process, easier to create clauses and predict what to do next
     */
    private $currentStage = self::IRRELEVANT;
    
    const IRRELEVANT = 0;
    const RESULTS = 1;
    const FISHES = 2;
    const BIGGEST = 3;
    const SKIP_UNTILL_NEW_ROUND = 4;
    
    // File contents (There is not actual file to be uploaded, only a string
    private $file = null;
    // File contents split to rows
    private $rows = null;
    
    /**
     * Set player with asterisk (*) as parsing player to identify "Omat kalat"
     * to know who to set the fishies to later on
     * @var string player name
     */
    private $_parsingPlayer = null;
    
    /**
     * 
     * @param miniPPBuddy $buddy
     */
    public function __construct(miniPPBuddy &$buddy) {
        $this->buddy = $buddy;
        $this->setStage(self::IRRELEVANT);
    }
    
    public function setFile($fileContents) {
        if ($this->file === null) {
            $this->file = $fileContents;
            $this->currentLine = 0;
        }

        return true;
    }
    
    
    public function setStage($stage) {
        $this->currentStage = $stage;
    }
    
    
    public function getStage() {
        return $this->currentStage;
    }
    
    public function getLineNum() {
        return $this->currentLine;
    }
    
    public function isNewRound($row) {
        if (strpos($row, self::NEW_ROUND_HOST) === 0 || strpos($row, self::NEW_ROUND_SELF) === 0) {
            return true;
        }
        return false;
    }
    
    /**
     * Return player details array
     * 0 = whole line
     * 1 = Result position
     * 2 = Team tag (could be empty '')
     * 3 = Name
     * 4 = Score
     * 5 = Country tag (can be null)
     * @param string $row
     * @return array
     */
    public function getPlayerDetails($row) {   
        // NetBeans complains for uninitialized variables
        $matches = null;
        $country = null;
        preg_match(self::PLAYER_INFORMATION, $row, $matches);
        preg_match(self::PLAYER_COUNTRY, $matches[3], $country);
        if (isset($country[1])) {
            $matches[] = $country[1];
            $matches[3] = trim(str_replace("[" . $country[1] . "]", '', $matches[3]));
        } else {
            $matches[] = null;
        }
        return $matches;
    }
    
    public function getBiggest($row) {
        $matches = array();
        preg_match(self::BIGGEST_FISH_FOR_LAKE, $row, $matches);
        return $matches;
    }
    
    public function isBiggestFish($row) {
        if (strpos($row, self::BIGGEST_FISH) === 0) {
            return true;
        }
        return false;
    }
    
    public function getLake($row) {
        // NetBeans complains for uninitialized variables
        $matches = null;
        preg_match(self::LAKE_INFORMATION, $row, $matches);
        return $matches;
    }
    
    /**
     * Skip empty rows
     * @param string $row
     * @return boolean
     */
    public function isEmpty($row) {
        if (empty($row)) {
            return true;
        }
        return false;
    }
    
    /**
     * Skip players leaving and joining information
     * @param string $row
     * @return boolean
     */
    public function isSkip($row) {
        if (strpos($row, self::JOIN) === 0 || 
                strpos($row, self::LEAVE) === 0) {
            return true;
        }
        return false;
    }
    
    /**
     * Skip this row but prepare for processing player results
     * @param string $row
     * @return boolean
     */
    public function isFinished($row) {
        if (strpos($row, self::FINISHED) === 0) {
            return true;
        }
        return false;
    }
    
    
    public function isResult($row) {
        if (preg_match(self::RESULT, $row)) {
            return true;
        }
        return false;
    }
    
/*    public function isDisqualified($row) { MOST LIKELY TO BE REMOVED
        if (preg_match(self::DISQUALIFIED, $row)) {
            return true;
        }
        return false;
    }
*/    
    public function isOwnFish($row) {
        if (strpos($row, self::SKIP_OWN) === 0) {
            return true;
        }
        return false;
    }
    
    public function isFishesPlayer($row) {
        if (preg_match(self::FISHES_PLAYER_NAME, $row) && !$this->isOwnFish($row)) {
            return true;
        }
        return false;
    }
    
    public function fishesPlayer($row) {
        $matches = array();
        preg_match(self::FISHES_PLAYER_NAME, $row, $matches);
        return $matches;
    }
    
    public function fishForPlayer($row) {
        $matches = array();
        preg_match(self::FISHES_FOR_PLAYER, $row, $matches);
        return $matches;
    }
    
    
    public function abandonRound($row) {
        if (strpos($row, self::ABANDON) === 0) {
            return true;
        }
        return false;
    }
    
    
    public function removeRound() {
        $this->buddy->removeLake($this->buddy->getRound());
        $this->buddy->setRound(($this->buddy->getRound() - 1));
    }
    
    public function process() {
        $this->rows = explode("\n", $this->file);
        $i = 0;
        $lake = null;
        $player = null;
        $rounds = $this->buddy->getRounds();

        foreach ($this->rows as $r) {
            if (!mb_detect_encoding($r, "UTF-8", true)) {
                $r = utf8_encode($r);
            }
            
            $this->currentLine++;
            $r = trim($r);
            if ($this->isEmpty($r) || $this->isSkip($r) ||
                    $this->getStage() == self::SKIP_UNTILL_NEW_ROUND && !$this->isNewRound($r)) {
                continue;
            }
            

            switch (true) {
                case $this->abandonRound($r):
                    $this->setStage(self::SKIP_UNTILL_NEW_ROUND);
                    break;
                case $this->isNewRound($r) && ($this->buddy->getRound() < $rounds || $rounds === 0):
                    $lakeInformation = $this->getLake($r);
                    $lake = $this->buddy->newLake();
                    $lake->setRoundNumber($this->buddy->getRound());
                    $lake->setName($lakeInformation[1]);
                    $lake->setIngameTime($lakeInformation[2]);
                    $lake->setRoundLength($lakeInformation[3]);
                    $lake->setGameType($lakeInformation[4]);
                    $lake->setRealTime($lakeInformation[5]);
                    $lake->setPoints($this->buddy->getPoints($this->buddy->getRound()));
                    $lake->setBiggestPoints($this->buddy->getBiggestPoints($this->buddy->getRound()));
                    break;
                case $this->isFinished($r):
                    $this->setStage(self::RESULTS);
                    break;
                case ($this->getStage() === self::RESULTS && $this->isResult($r)):
                    $plr = $this->getPlayerDetails($r);
                    if (!$player = $this->buddy->getPlayer($plr[3])) {
                        $player = $this->buddy->newPlayer($plr[3]);
                    }
                    $lake->setPlayer($player);
                    $player->setPosition($this->buddy->getRound(), $plr[1]);
                    $player->setTeam($plr[2]);
                    $player->setName($plr[3]);
                    $player->setScore($this->buddy->getRound(), $plr[4]);
                    $player->setCountry($plr[5]);
                    if ($player->isParser()) {
                        $this->_parsingPlayer = $player->getName();
                    }
                    break;
                case ($this->isOwnFish($r)) :
                    $this->setStage(self::FISHES);
                    $playerName = $this->buddy->getPlayer($this->_parsingPlayer)->getName();
                    break;
                // Two step parsing due to rows including first the player name and then the fishies
                // Note that the player name is without country or team tags! Bloody stupid!
                case ($this->getStage() === self::FISHES && $this->isFishesPlayer($r) && $this->isBiggestFish($r) === false) :
                    $playerName = $this->fishesPlayer($r);
                    break;
                case ($this->getStage() === self::FISHES && $this->isBiggestFish($r) === false) :
                    if (is_array($playerName)) {
                        $player = $this->buddy->getPlayer($playerName[1]);
                    } else {
                        $player = $this->buddy->getPlayer($playerName);
                    }
                    $fishDetails = $this->fishForPlayer($r);
                    $player->setFishes($this->buddy->getRound(), $fishDetails);
                    break;
                case ($this->getStage() === self::FISHES && $this->isBiggestFish($r)) :
                    $this->setStage(self::BIGGEST);
                    break;
                case ($this->getStage() == self::BIGGEST) :
                    $biggest = $this->getBiggest($r);
                    $lake->setBiggestFish($biggest);
                    $this->setStage(self::IRRELEVANT);
                    if ($this->buddy->getRound() == $rounds) { // Fix issue for loop going too far to next rounds individual fishies
                        break 2;
                    }
                    break;
            }
        }
    }
}