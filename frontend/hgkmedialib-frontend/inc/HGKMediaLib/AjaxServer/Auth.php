<?php
class HGKMediaLib_AjaxServer_Auth extends HGKMediaLib_AjaxExportable{

    /**
     * __construct 
     * 
     * @access protected
     * @return void
     */
    function __construct(){
        parent::__construct($this);
    }

	/**
	* @return boolean
	*/
	public function dropSession(){
        return $this->_adapter->dropSession();
	
	}

    public function getDomains()
    {
        return $this->_adapter->getDomains();
    }
    
	/**
	* @param string $userName
	* @param string $password
	* @param string $domain
	* @return int $sessionId
	*/
	public function getSession($userName, $password, $domain){
        $this->_sanitizeInput($userName);
        $this->_sanitizeInput($password);
        $this->_sanitizeInput($domain);
        return $this->_adapter->getSession($userName, $password, $domain);
	
	}

	/**
	* Get user data (real name, name of institution, etc.)
	*
	* @return array
	*/
	public function getUserData(){
        return $this->_adapter->getUserData();
	
	}

}
?>
