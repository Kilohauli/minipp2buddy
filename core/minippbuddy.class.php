<?php

class miniPPBuddy {
    protected $_config = array();
    
    /**
     * @var miniRegexp miniRegexp class
     */
    protected $regex = null;
    
    /**
     * @var boolean Enables console printing when set to true
     */
    private $debug = false;
    
    /**
     * @var array Lake objects which in simplicity are rounds
     */
    private $lakes = array();
    
    /**
     * @var int Current round number which miniRegexp parser is parsing
     */
    private $currentRound = 0;
    
    /**
     * @var array player objects array per round
     */
    private $players = array();

    /**
     * @var int Amount of rounds to be parsed, zero equals to infinite rounds
     */
    private $rounds = 0;
    
    /**
     * @var array Holds the points given per round
     */
    private $points = array();

    /**
     * @var array Holds the points for biggest fish given per round
     */
    private $biggestPoints = array();
    
    /**
     *
     * @var array Final results before sorting
     */
    private $finalResults = array();
    
    private $_renderer = '';
    
    private $_request = '';
    
    private $_lang = array();
    
    public function __construct($config) {
        // Currently not in use
        $this->_config = array_merge($this->_config, $config);
        $this->loadLanguageFile();
    }
    
    /**
     * 
     * @param string $name
     * @return mixed
     */
    public function getConfigKey($name) {
        $vars = explode(".", $name);
        if (count($vars) > 1) {
            $config = $this->_config[$vars[0]][$vars[1]];
        } else {
            $config = $this->_config[$vars[0]];
        }
        return $config;
    }
    
    /**
     * Load parser which goes through the playlog
     * @return miniRegexp
     */
    public function getRegexp() {
        if ($this->regex !== null) {
            return $this->regex;
        }
        
        require_once dirname(__FILE__) . '/regexp.class.php';
        $this->regex = new miniRegexp($this);
        return $this->regex;
    }
    
    public function getRenderer() {
        if (!class_exists('miniRender')) {
            require_once dirname(__FILE__) . '/render.class.php';
            $this->_renderer = new miniRender($this, $this->_config);
        }
        
        return $this->_renderer;
        
    }

    /**
     * 
     * @return miniRequest
     */
    public function getRequest() {
        if(!class_exists('miniRequest')) {
            require_once dirname(__FILE__) . '/request.class.php';
            $this->_request = new miniRequest($this);
        }
        return $this->_request;
    }
    
    /**
     * Remove odd characters for array keys
     * Characters include scandic ä, ö, å and so on
     * list can be adjusted when new issue arises, which will arise sooner than later
     */
    public function strip($string) {
        return (string) strtolower(
            str_replace(
                    array("[", "]", 'ä', 'ö', 'å', 'Ä', 'Ö', 'Å', ' ', '(', ')', '-', '*', ','), 
                    array('', '', 'a', 'o', 'a', 'A', 'O', 'A', '', '', '', '_', '', ''), 
                        $string));
    }
    
    public function setDebug(boolean $debug) {
        $this->debug = $debug;
    }
    
    public function isDebug() {
        return $this->debug();
    }
    
    public function debug() {
        print_r(array(
            array_keys($this->lakes),
            array_keys($this->players),
            $this->biggestFishes
        ));
    }
    
    /**
     * Create new miniLake object
     * @return miniLake
     */
    public function newLake() {
        if (!class_exists('miniLake')) {
            require_once dirname(__FILE__) . '/lake.class.php';
        }
        $this->currentRound++;
        $this->lakes[$this->currentRound] = new miniLake($this);
        return $this->lakes[$this->currentRound];
    }
    
    /**
     * 
     * @param int $round
     * @return boolean
     */
    public function removeLake($round) {
        $this->lakes[$round] = '';
        return true;
    }
    /**
     * Get current round number
     * @return int
     */
    public function getRound() {
        return $this->currentRound;
    }
    
    /**
     * Set round manually, used to reset abandoned rounds
     * @param int $round
     * @return boolean
     */
    public function setRound($round) {
        $this->currentRound = $round;
        return true;
    }
    
    /**
     * Set how many rounds are parsed from play log
     * @param int $rounds
     */
    public function setRounds($rounds) {
        if ($rounds > 0 && $rounds <= $this->_config['max_rounds']) {
            $this->rounds = $rounds;
        } else {
            $this->rounds = $this->_config['max_rounds'];
        }
    }
    
    /**
     * Return amount of rounds will be played, even there is basically unlimited
     * round option just by slamming in huge playlog.
     * @return int
     */
    public function getRounds() {
        return $this->rounds;
    }
    
    /**
     * 
     * @param string $name
     * @return miniPlayer
     */
    public function newPlayer($name) {
        $stripped = $this->strip($name);
        if (!class_exists('miniPlayer')) {
            require_once dirname(__FILE__) . '/player.class.php';
        }
        $this->players[$stripped] = new miniPlayer($this);
        return $this->players[$stripped];
    }
    
    /**
     * 
     * @param type $name
     * @return false|miniPlayer
     */
    public function getPlayer($name) {
        $stripped = $this->strip($name);
        if (!array_key_exists($stripped, $this->players)) {
            return false;
        }
        return $this->players[$stripped];
    }
    
    /**
     * Get array of parsed lakes
     * @return array
     */
    public function getLakes() {
        return $this->lakes;
    }
    
    /**
     * Get array of players for round/lake
     * @param int $round
     * @return array
     */
    public function getPlayers($round) {
        return $this->players[$round];
    }
    
    /**
     * Get all players regardless of round (eases zero score output if needed)
     * @return array|miniPlayer
     */
    public function getAllPlayers() {
        return $this->players;
    }
    
    /**
     * Set points for player results
     * @param array|string $points
     */
    public function setPoints($points) {
        if (!is_array($points)) {
            /** 
             * Index starts from 1 which corresponds to current round which
             * oddly enough is not a zero 
             **/
            $this->points[1] = array_map("trim", explode(",", $points));
        } else {
            /**
             * Same as above but due to multiple rounds some loopy loopy has to
             * be done
             */
            foreach ($points as $key => $p) {
                $this->points[($key + 1)] = array_map("trim", explode(",", $p));
            }
        }
    }
    
    /**
     * Return points array for round/lake, always returns first round points if
     * only one pattern for round points defined
     * @param int $round
     * @return array
     */
    public function getPoints($round = 1) {
        if (count($this->points) == 1) {
            return $this->points[1];
        }
        return $this->points[$round];
    }
    
    /**
     * Set points for biggest fish
     * @param array|string $points
     */
    public function setBiggestPoints($points) {
        if (!is_array($points)) {
            $this->biggestPoints[1] = array_map("trim", explode(",", $points));
        } else {
            foreach ($points as $key => $p) {
                $this->biggestPoints[($key + 1)] = array_map("trim", explode(",", $p));
            }
        }
    }
    
    /**
     * Return biggest fish points array for round/lake, always returns first
     * round points if only one pattern for biggest fish points defined
     * @param int $round
     * @return array
     */
    public function getBiggestPoints($round) {
        if (count($this->biggestPoints) == 1) {
            return $this->biggestPoints[1];
        }
        return $this->biggestPoints[$round];
    }
    
    /**
     * Calculates final scores, will be deprecated most likely
     * @return array
     */
    public function finalScore() {
        $lakes = $this->getLakes();
        foreach ($lakes as $key => $lake) {
            $scores[] = $lake->process();
        }
        $finalScore = array();
        foreach ($scores as $key => $score) {
            foreach($score as $id => $result) {
                if (array_key_exists($id, $finalScore)) {
                    $finalScore[$id] += (int) $result['points'];
                } else {
                    $finalScore[$id] = (int) $result['points'];
                }
            }

        }
        arsort($finalScore, SORT_NUMERIC);
        return $finalScore;
    }
    
    /**
     * 
     * @return array
     */
    public function loadLanguageFile() {
        $lang = $this->getConfigKey('language');
        $language_path = $this->getConfigKey('language_path');
        
        $this->_lang = require_once($language_path.$lang.".php");
        
        return $this->_lang;
    }
    
    /**
     * Translate string, _lang keys are stripped original strings
     * @param string $str
     * @return string
     */
    public function translate($str) {
        return $this->_lang[$this->strip($str)];
    }
    
    /**
     * Date to string
     * @return string
     */
    public function dateToString($date) {
        $date = explode(" ", $date);
        $date = explode(".", $date[0]);
        $str = '';
        switch ($date[1]) {
            case '1' :
                $str = $this->translate('january') . " " . $date[0];
                break;
            case '3' :
                $str = $this->translate('march') . " " . $date[0];
                break;
            case '11' :
                $str = $this->translate('november') . " " . $date[0];
                break;
        }
        return $str;
    }
    
    public function timeToString($time) {
        $time = explode(" ", $time);
        $time = explode(":", $time[1]);
        $str = '';
        switch ($time[0]) {
            case '9' :
                $str = $this->translate('morning');
                break;
            case '12' :
                $str = $this->translate('midday');
                break;
            case '14' :
                $str = $this->translate('evening');
                break;
            case '18' :
                $str = $this->translate('night') . " " . $date[0];
                break;
        }
        return $str;
    }
}