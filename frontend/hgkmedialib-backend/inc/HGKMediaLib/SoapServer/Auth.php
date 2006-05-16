<?php
/**
 * DUMMY Implementation of the SOAP authentication interface to the MediaLib backend
 * 
 * Copyright 2005-2006 mediagonal Ag <info@mediagonal.ch>
 *
 * See the enclosed file COPYING for license information (GPL 2). If you
 * did not receive this file, see http://www.fsf.org/copyleft/gpl.html.
 *
 * @author  Pierre Spring <pierre.spring@mediagonal.ch>
 * @package HGKMediaLib_DummyBackend
 * @version $Id$
 */


require_once('../../../conf/config.php');


class HGKMediaLib_SoapServer_Auth implements HGKMediaLib_AuthInterface{

    /**
     * Drop a given $session on the server side (winet)
     * Return a new anonymous session on success, 0 else.
     * 
     * @param mixed $session id
     * @access public
     * @return mixed session id
     */
    public function dropSession($session){
        return $this->getAnonymousSession();
    }

	/**
	 * Get session for an unknown, anonymous user.
     * Return session id on success, 0 else.
	 *
	 * @param void
     * @access public
	 * @return mixed session id
	 */
	public function getAnonymousSession(){
		return $this->getSession('anonymous', 'anonymous', 'default') + 1; 
	}

    /**
     * get a list of available domains
     * 
     * @access public
     * @return array of string
     */
    public function getDomains()
    {
        return array("hgkz", "university of fribourg", "mediagonal");
    }

	/**
	 * Authenticate using standard credentials.
     * Return session id on success, 0 else.
	 *
	 * @param string $userName
	 * @param string $password
	 * @param string $domain
     * @access public
	 * @return mixed session Id
	 */
	public function getSession($userName, $password, $domain){

		return 1234567890;
	}

	/**
	 * Get user data (real name, name of institution, etc.), given her $session id.
     * Return a HGKMediaLib_Struct_User object on success, 0 else.
	 *
	 * @param mixed $session id
     * @access public
	 * @return HGKMediaLib_Struct_User object
	 */
	public function getUserData($session){

        $result = "error: no session, given:" . $session . "\n";

        if ($session == $this->getAnonymousSession()){
            $result = 0;
        } 
        if ($session == $this->getSession('x','y','z')) {
            $result = new HGKMediaLib_Struct_User();
            $result->first  = 'Pierre';
            $result->last   = 'Spring';
            $result->domain = 'mediagonal';
            $result->email  = 'pierre.spring@mediagonal.ch';
        } 

        return $result;
    }

}
if (strtoupper($_SERVER['REQUEST_METHOD']) == 'GET' && isset($_GET['wsdl'])) {
	header("content-type: text/xml");
	readfile(HKGMEDIALIB_WSDL_BASEDIR . '/Auth.wsdl');
	exit;
}

$soapServerPlaylistInstance = new SoapServer(HKGMEDIALIB_WSDL_BASEDIR . 'Auth.wsdl');
$soapServerPlaylistInstance->setClass("HGKMediaLib_SoapServer_Auth");
$soapServerPlaylistInstance->handle();

?>
