<?php
echo "<pre>";

$config = require_once dirname(__FILE__) . '/config/config.php';

require_once BUDDY_CORE_PATH.'/minippbuddy.class.php';

$buddy = new miniPPBuddy($config);

/**
 * Mimic the parameters passed from the website for round scores
 */
$scoresDummy = "14,12,10,9,8,7,6,5,4,3,2,1";
$biggestDummy = "1";

/**
 * Mimic the parameters passed from the websites for round scores as an array
 */
$scoresDummyArray = array();
$scoresBiggestArray = array();

$buddy->setPoints($scoresDummy);
$buddy->setBiggestPoints($biggestDummy);

$exp = $buddy->getRegexp();

$buddy->setRounds(1);
$f = file_get_contents('./score_demo.txt');
$exp->setFile($f);

$exp->process();

print_r($buddy->finalScore());