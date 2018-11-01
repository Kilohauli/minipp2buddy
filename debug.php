<?php

require_once('./core/minippbuddy.class.php');

$config = require_once dirname(__FILE__) . '/config/config.php';
$buddy = new miniPPBuddy($config);

$request = $buddy->getRequest();
/**
 * Mimic the parameters passed from the website for round scores
 */
$scoresDummy = "12,10,8,7,6,5,4,3,2,1";
$biggestDummy = "0";
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

$f = file_get_contents('./score_demo.txt');
$exp->setFile($f);
echo "<pre>";
$exp->process();

$results = $request->process();
echo "START REQUEST PROCESS\n";
print_r($results);
echo "REQUEST PROCESS FINISHED\n";