<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
define(BUDDY_ROOT_PATH, dirname(dirname(__FILE__)) . "/");
define(BYDDY_CORE_PATH, BUDDY_ROOT_PATH . "/core/");


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
        
    )
);

return $config;