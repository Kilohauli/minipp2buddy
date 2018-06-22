<?php

require_once('./core/minippbuddy.class.php');

$mini = new miniPPBuddy(array());

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

$mini->setPoints($scoresDummy);
$mini->setBiggestPoints($biggestDummy);
$exp = $mini->getRegexp();
$mini->setRounds(1);
$f = file_get_contents('./score_demo.txt');

$exp->setFile($f);

$exp->process();

$lakes = $mini->getLakes();

foreach ($lakes as $key => $lake) {
    $lake->debug();
    $lake->process();

}