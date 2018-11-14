<?php
/**
 * Cronjob to that can be run on host that has php-cli installed
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
$f = $exp->tail($buddy->getConfigKey('playlog_path'), 500);

$buddy->setRounds(3);
$exp->setFile($f);
$exp->process();
print_r($request->process());
