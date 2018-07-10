<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

if (!class_exists('ppIndexController')) {
    require_once dirname(__FILE__) . '/indexController.class.php';
}

class ppXHRController extends ppIndexController {
    public function out() {
        $scores = $this->_buddy->finalScore();
        $lakes = $this->processLakesOutput();
        return array(
            'test' => 'test',
            'final' => $scores,
            'lakes' => $lakes
        );
    }
}