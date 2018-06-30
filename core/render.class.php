<?php

class miniRender {
    private $_buddy;
    private $_config = array(
        'template_path' => BUDDY_ROOT_PATH . "/templates/",
        'template_name' => 'default',
        'template_file_ext' => 'html',
        'index_template' => 'index',
        'piece_file_ext' => 'piece.html'
    );
    
    public function __construct(miniPPBuddy &$buddy, $config = array()) {
        $this->_buddy  = $buddy;
        $this->_config = array_merge($this->config, $config);
        
        return $this;
    }
    
    public function getPiece($name) {
        
    }
    
    public function process() {
        
    }
    
    public function getTemplate($templateFileName, $templateVariables) {
        $tmpl = $this->_config['index_template'];
        if ($templateName) {
            $tmpl = $templateFileName;
        }
        
        if (!$tmplFile = file_exists($this->_config['template_path'] . 
                $this->_config['template_name'] . "/" .
                $tmpl . '.' . $this->_config['template_file_ext'])) {
            
        }
    }
}