<?php

/**
 * Cronjob for debugging in browser as no scrollback restrictions/settings
 */

$config = require_once dirname(dirname(__FILE__)) . '/config/config.php';
require_once BUDDY_CORE_PATH . 'minippbuddy.class.php';

$buddy = new miniPPBuddy($config);

$request = $buddy->getRequest();
/**
 * Mimic the parameters passed from the website for round scores
 */
$scoresDummy = "12,10,8,7,6,5,4,3,2,1";
$biggestDummy = "1";
$teamPoints = "2,1,0";

/**
 * Mimic the parameters passed from the websites for round scores as an array
 */
$scoresDummyArray = array();
$scoresBiggestArray = array();

$buddy->setPoints($scoresDummy);
$buddy->setBiggestPoints($biggestDummy);
$exp = $buddy->getRegexp();
$buddy->setRounds(3);

$f = file_get_contents(BUDDY_ROOT_PATH . 'score_demo.txt');
$exp->setFile($f);

$exp->process();
echo "<pre>";
print_r($request->process());