<?php

class miniRequest {
    private $_buddy = '';
    private $_variables = array(
        'get' => array(),
        'post' => array()
    );
    private $_headers = '';
    private $_controller = '';
    
    public function __construct(miniPPBuddy $buddy) {
        $this->_buddy = $buddy;
        $this->_headers = $this->getallheaders();
        //$this->sanitize();
        $this->_variables['get'] = $_GET;
        $this->_variables['post'] = $_POST;
        $this->getController();
    }
        
    /**
     * Sanitize _GET and _POST
     * Need to find better solution. If player has <- script dies
     */
    public function sanitize() {
        $_GET = filter_input_array(INPUT_GET, FILTER_SANITIZE_STRING);
        $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
    }
    
    /**
     * Remove possible characters that could affect path and direct
     * to file system
     */
    public function sanitizePageQuery() {
        if (isset($_GET['q'])) {
            preg_match('/([a-zA-Z])/', $_GET['q'], $q);
            print_r($q);
            if ($q) {
                $_GET['q'] = filter_input('', 'q', '');
            }
            
        }
    }
    
    public function isXHR() {
        if (isset($this->_headers['x-header-xhr'])) {
            return true;
        }
        return false;
    }
    
    public function getController() {
        if ($this->_controller !== '') {
            return $this->_controller;
        }
        if (!class_exists('miniXHRController') && $this->isXHR()) {
            require_once dirname(dirname(__FILE__)) . '/controllers/xhrController.class.php';
            $this->_controller = new ppXHRController($this->_buddy, $this);
        } else {
            require_once dirname(dirname(__FILE__)) . '/controllers/indexController.class.php';
            $this->_controller = new ppIndexController($this->_buddy, $this);
        }
        return $this->_controller;
    }
    
    public function process() {
        return $this->_controller->out();
    }
    
    /**
     * Fix for nginx and getallheaders
     * @return array
     */
    public function getallheaders() {
        if (!function_exists('getallheaders')) { 
            $headers = []; 
            foreach ($_SERVER as $name => $value) { 
                if (substr($name, 0, 5) == 'HTTP_') { 
                    $headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value; 
                } 
            } 
        } else {
            $headers = getallheaders();
        }
        return $headers; 
    } 
   
}