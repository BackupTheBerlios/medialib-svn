<?php
/**
 * Central authentication interface for SOAP access to the backend
 *  
 * Copyright 2005-2006 Pierre Spring, mediagonal Ag <pierre.spring@mediagonal.ch>
 *
 * See the enclosed file COPYING for license information (GPL). If you
 * did not receive this file, see http://www.fsf.org/copyleft/gpl.html.
 *
 * 
 * Interface declaring the signatures of methods being called by the HGKZ video library's frontend component.
 * Presumably there will be two classes that implement this interface: one on the serving side (connected to a SOAP server), 
 * and another one on the requesting side (connected to a SOAP client generated from the server's WSDL).
 * 
 * Generally speaking, there are two kinds of methods: get*() and dump*().
 * 
 * This interface only specifies methods request data from the metadata database as built by Winet. Queries to the storage mgmt and
 * other components are specified elsewhere, although they might fit in here from the API user's perspective.
 * 
 * @package HGKMediaLib
 * @author Pierre Spring <pierre.spring@mediagonal.ch>
 * @version $Id$
 */
interface  HGKMediaLib_AuthInterface{

    /**
     * The dropSession() methode unvalidates the given $session ID
     * 
     * @param mixed $session id
     * @access public
     * @return boolean
     */
    public function dropSession($session);

	/**
	 * Get session for an unknown, anonymous user
	 *
	 * @param void
     * @access public
	 * @return mixed session id
	 */
	public function getAnonymousSession();

    /**
     * get a list of available domains
     * 
     * @access public
     * @return array of string
     */
    public function getDomains();

	/**
	 * Authenticate using standard credentials
	 *
	 * @param string $userName
	 * @param string $password
	 * @param string $domain
     * @access public
	 * @return mixed session Id
	 */
	public function getSession($userName, $password, $domain);

	/**
	 * Get user data (real name, name of institution, etc.)
	 *
	 * @param mixed $session id
     * @access public
	 * @return mixed user data.
	 */
	public function getUserData($session);

}
?>
