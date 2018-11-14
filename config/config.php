<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
$root = dirname(dirname(__FILE__));
define("BUDDY_ROOT_PATH",  $root . "/");
define("BUDDY_CORE_PATH", BUDDY_ROOT_PATH. "core/");


$config = array(
    'templates_path' => BUDDY_ROOT_PATH . 'templates/',
    'template_name' => 'default',
    'template_file_ext' => 'html',
    'index_template' => 'index',
    'pieces' => array(
        'lake_information' => 'lake_row',
        'result_container' => 'result_container',
        'result_row' => 'result_row',
        'biggest_fish_container' => 'biggest_container',
        'biggest_fish_row' => 'biggest_row',
        'fishes_container' => 'fish_container',
        'fishes_row' => 'fish_row',
        'global_header' => 'header',
        'global_footer' => 'footer',
        
    ),
    'max_rounds' => 10,
    'processor_path' => BUDDY_ROOT_PATH . "processors/",
    'controller_path' => BUDDY_ROOT_PATH . "controllers/",
    'language_path' => BUDDY_ROOT_PATH . "languages/",
    // This is just for simplified installations
    'force_processor' => 'miniPlayerRanking',
    'force_controller' => 'ppIndexController',
    'override_regexp_path' => BUDDY_ROOT_PATH . 'buddyconnect/',
    'override_regexp_class' => 'connectRegexp',
    'language' => 'finnish',
    'db' => array( // Will add proper ORM (xPDO, Propel) have to see
        'name' => '',
        'user' => '',
        'passwd' => ''
    ),
    /* Absolute path to playlog when running cronjob. Commonly in:
     * /etc/pp2host/static/playlog.txt
     */
    'playlog_path' => '/home/timo/pp2host/playlog.txt'
);

return $config;