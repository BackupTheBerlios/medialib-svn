<?php
/**
 * Interface for SOAP access to the urn resolver / adder / updater
 *  
 * Copyright 2005-2006 Pierre Spring, mediagonal Ag <pierre.spring@mediagonal.ch>
 *
 * See the enclosed file COPYING for license information (GPL). If you
 * did not receive this file, see http://www.fsf.org/copyleft/gpl.html.
 *
 * @package HGKMediaLib
 * @author Pierre Spring <pierre.spring@mediagonal.ch>
 * @version $Id$
 */
interface  HGKMediaLib_UrnResolverInterface{

    /**
     * the add() function adds an URN to the resolver's database.
     * 
     * @param string $user        user name for authentication
     * @param string $passwd      password for authentication
     * @param string $collection  the collection a media belongs to (e.g. mestore)
     * @param string $signature   unique id within a collection
     * @param string $media       defining the media type (e.g. VBM, COV...)
     * @param string $sequence    unique id within a $signature/$media
     * @param string $path        the path to the file an urn references to
	 * @param string $provider    the host where the media is placed
     * @access public
     * @return string   the urn? or boolean TODO
     */
    public function add($user, $passwd, $collection, $signature, $media, $sequence, $path, $provider);
	
    /**
	 * the update() function updates an URN in the resolver's database.
     * 
     * @param string $user        user name for authentication
     * @param string $passwd      password for authentication
	 * @param string $urn
	 * @param string $path        	the path to the file an urn references to
     * @access public
     * @return void
     */
    public function update($user, $passwd, $urn, $path);
	
	
	/**
	 * the delete() function removes an URN in the resolver's database.
     * 
     * @param string $user			user name for authentication
	 * @param string $passwd      	password for authentication
	 * @param string $urn
     * @access public
     * @return void
     */
    public function delete($user, $passwd, $urn);

    /**
     * the resolve methode is used for the acctual urn resolving.
     *
     * it returns an array of path strings, referencing the file locations.
     *
     * @param string $sessionId 
     * @param string $urn 
     * @access public
     * @return array
     */
    public function resolve($sessionId, $urn);
}
?>