<?php

if (!class_exists('miniProcessor')) {
    require_once dirname(__FILE__) . '/miniProcessor.class.php';
}

class miniPlayerRanking extends miniProcessor {
    
    protected function calculate() {
        
    }
    
    protected function output($format = 'array') {
        
        if ($format === 'json') {
            $temp = json_encode($out);
        }
        
        return $out;
    }
}