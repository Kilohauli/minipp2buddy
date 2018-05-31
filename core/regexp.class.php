<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class miniRegexp {
    private $buddy = false;
    
    /**
     * Next constants use strpos() to match beginning of line
     */
    // Catches biggest fish and who got it
    const BIGGEST_FIST = 'Isoin kala:';
    // Catches lake, time and so on
    const NEW_ROUND = 'New LAN SERVER';
    // Begin row processing after this row
    const FINISHED = 'Competition finished';
    // Skip plenty of unnecessary rows through the file
    const SKIP_OWN = 'Omat kalat:';
    // Joining players
    const JOIN = '-->';
    // Leaving players
    const LEAVE = '<--';
    
    /** 
     * Next constants are for regular expressions 
     */
    // Disqualified players (Failed to return to finish)
    const DISQUALIFIED = '/\(disq\)$/';
    // Lake name
    const LAKE = '/^[a-zA-ZäöåÄÖÅ\s-_]*/';
    // Ingame time
    const DATE = '/\[[0-9\.:\s]*\]$/';
    // Round length
    const ROUND_LENGTH = '';
    // Game type
    const GAME_TYPE = '';
    // Real time
    const REAL_TIME = '';
    
    public function __construct(miniPPBuddy &$buddy) {
        $this->buddy = $buddy;
    }
    
    public function getPlayer($row) {
        
    }
    
    public function getTeam($row) {
        
    }
    
    public function getScore($row) {
        
    }
    
    public function getBiggest($row) {
        
    }
    
    public function getLake($row) {
        
    }
    
    public function getGameType($row) {
        
    }
    
    public function getIngameTime($row) {
        
    }
    
    public function getRealTime($row) {
        
    }
    
    public function getRoundLength($row) {
        
    }
    
    public function debug() {
        
    }
    
}