<?php

class ppIndexController {
    private $_buddy = '';
    
    public function __construct(miniPPBuddy &$buddy) {
        $this->_buddy = $buddy;
    }
}