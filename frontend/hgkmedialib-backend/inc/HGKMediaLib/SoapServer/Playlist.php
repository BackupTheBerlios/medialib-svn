<?php
/**
 * DUMMY SOAP server for Playlist access to the backend
 *  
 * This is just a dummy, not the real thing. For documentation please consult
 * the interface definition.
 * 
 * Copyright 2005-2006 Pierre Spring, mediagonal Ag <pierre.spring@mediagonal.ch>
 *
 * See the enclosed file COPYING for license information (GPL). If you
 * did not receive this file, see http://www.fsf.org/copyleft/gpl.html.
 *
 * @author  Pierre Spring <pierre.spring@mediagonal.ch>
 * @subpackage HGKMediaLib_Backend_Dummy
 * @package HGKMediaLib
 * @version $Id$
 * @see HGKMediaLib_ReadingInterface
 */


require_once('../../../conf/config.php');

class HGKMediaLib_SoapServer_Playlist implements HGKMediaLib_PlaylistInterface {
    
    /**
     * Add an entity ($entityID) to the playlist defined by $playlistID 
     * of the user ($sessionID). Return the name of the entity on success
     * as a string, false else.
     * 
     * @param mixed $session 
     * @param mixed $playlistID 
     * @param mixed $entityID 
     * @access public
     * @return mixed
     */
    public function addEntityToPlaylist($session, $playlistID, $entityID, $position = 0) {
        return "added movie " . rand(1000, 9999);
    }
    
    /**
     * Create a new playlist for a given user. Return the id of the new playlisit
     * on success, false else.
     * 
     * @param mixed $session 
     * @param mixed $playlistName 
     * @access public
     * @return mixed
     */
    public function createPlaylist($session, $playlistName)
    {
        return "NewPlaylistID" . rand(10000, 99999);
    }
    
    /**
     * Get a $user's playlist. The $user has two predefined values:
     *     'self': the user refered to by the $session id
     *     'recommended': the playlist recommended by the admin
     * Any other string in the $user parameter specifies another user
     * by her login.
     *
     * The function returns an array of HGKMediaLib_Struct_Playlist on
     * success, O else.
     * 
     * @param mixed $session id
     * @access public
     * @return array of HGKMediaLib_Struct_Playlist
     */
    public function getPlaylists($session, $user = 'self', $lang = 'de') {
        $result = array();
        $amountOfPlaylists = rand(5,10);
        for ($i = 0; $i < $amountOfPlaylists; $i++) {
            $result[] = new HGKMediaLib_Struct_Playlist();
            $amountOfItems = rand(2,4);
            $result[$i]->array = array();
            for ($j = 0; $j < $amountOfItems; $j++) {
                $result[$i]->array["SetId9223201{$j}"] = "Set Nr $j";
            }
            $result[$i]->id = 'playlistID' . $i;
            $result[$i]->name = "Playlist $i";
        }
        return $result;
    }

    /**
     * Remove an entity ($entityID) from the playlist defined by $playlistID
     * of the user ($sessionID). Return treu by success, false else.
     * 
     * @param mixed $session 
     * @param mixed $playlistID 
     * @param mixed $entityID 
     * @access public
     * @return boolean
     */
    public function removeEntityFromPlaylist($session, $playlistID, $entityID) {
        return true;
    }
    
    public function updatePlaylist($session, $playlistID, $array) {
        return true;
    }

    /**
     * Remove a playlist defined by $playlistID of the user ($sessionID). 
     * Return true by success, false else.
     * 
     * @param mixed $session 
     * @param mixed $playlistID 
     * @access public
     * @return boolean
     */
    public function removePlaylist($session, $playlistID){
        return true;
    }

    /**
     * Swap the positions of two entities within a given playlist. This function returns
     * true if the swapping was sucessfull and false else.
     * 
     * @param mixed $session 
     * @param mixed $playlistID 
     * @param mixed $entityID1 
     * @param mixed $entityID2 
     * @access public
     * @return boolean
     */
    public function swapPositions($session, $playlistID, $entityID1, $entityID2) {
        return true;
    }

    /**
     * Set the authorization level of a playlist, where $authorization
     * is a string of the following list:
     * 
     *     'private'      is a list, that only the ures that created it can see
     *     'public read'  is a list, that everyone can read
     *     'public write' is a list, that everyone can read and add/remove
     * 
     * it returns true, false else
     * 
     * @param mixed $session 
     * @param mixed $playlistID 
     * @param string $authorization 
     * @access public
     * @return boolean
     */
    public function setPlaylistAuthorization($session, $playlistID, $authorization) {
        return true;
    }

        
}

if (strtoupper($_SERVER['REQUEST_METHOD']) == 'GET' && isset($_GET['wsdl'])) {
	header("content-type: text/xml");
	readfile(HKGMEDIALIB_WSDL_BASEDIR . '/Playlist.wsdl');
	exit;
}

$soapServerPlaylistInstance = new SoapServer(HKGMEDIALIB_WSDL_BASEDIR . 'Playlist.wsdl');
$soapServerPlaylistInstance->setClass("HGKMediaLib_SoapServer_Playlist");
$soapServerPlaylistInstance->handle();


?>
