<?php
if (session_id() == '') {
    session_start();        
}

class HGKMediaLib_AjaxServer_AuthSoapAdapter extends Adapter {
            
    public function __construct()
    {
        //parent::__construct($this);
        parent::__construct($this);
    }        

	/**
	* @return boolean
	*/
	public function dropSession()
    {
        if (!isset($_SESSION['soapSession']) || !isset($_SESSION['logged'])){
            // todo
            return false;
        }elseif($_SESSION['logged'] != true){
            // todo
            return false;
        }
        $this->getAnonymousSession();
        return true;
	
	}
    
    /**
     * getAnonymousSession 
     * 
     * @access public
	 * @return int $sessionId
     */
    public function getAnonymousSession()
    {
        $soapSession = $this->_soapClient->getAnonymousSession();
        if ($soapSession != 1){
            $_SESSION = array();
            $_SESSION['soapSession'] = $soapSession;
            $_SESSION['logged'] = false;
            return true;
        }
        return -1;
    }

    public function getDomains()
    {
        return $soapSession = $this->_soapClient->getDomains();
    }

	/**
	* @param string $userName
	* @param string $password
	* @param string $domain
	* @return int $sessionId
	*/
	public function getSession($userName, $password, $domain)
    {
        $soapSession = $this->_soapClient->getSession($userName, $password, $domain);
        if ($soapSession != 1){
            $_SESSION = array();
            $_SESSION['soapSession'] = $soapSession;
            $_SESSION['logged'] = true;
            return true;
        }
        return -1;
	
	}
	
	
	/**
	* Get user data (real name, name of institution, etc.)
	*
	* @return array
	*/
	public function getUserData()
    {
        if (!isset($_SESSION['soapSession'])){
            //TODO
            return false;
        }else{
            return $this->_soapClient->getUserData($_SESSION['soapSession']);
        }
	
	}


}
?>
