<?php

require_once('./core/minippbuddy.class.php');

$mini = new miniPPBuddy(array());


$exp = $mini->getRegexp();

$f = file_get_contents('./score_demo.txt');

$exp->setFile($f);

$exp->process();
