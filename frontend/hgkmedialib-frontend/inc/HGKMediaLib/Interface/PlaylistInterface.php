<?php
/**
 * Central playlist interface for SOAP access to the backend
 *
 * Copyright 2005-2006 Pierre Spring, mediagonal Ag <pierre.spring@mediagonal.ch>
 *
 * See the enclosed file COPYING for license information ( GPL). If you
 * did not receive this file, see http://www.fsf.org/copyleft/gpl.html.)
 * 
 * Interface declaring the signatures of methods being called by the HGKZ video library's frontend component.
 *
 * The frontend reads one user's playlist and can get a playlist recomended by the admin and let's
 * one user add and remove an entity to her playlist.
 * 
 * Presumably there will be two classes that implement this interface: one on the serving side (connected to a SOAP server), 
 * and another one on the requesting side ( connected to a SOAP client generated from the server's WSDL).
 * 
 * @package HGKMediaLib
 * @author Pierre Spring <pierre.spring@mediagonal.ch>
 * @copyright 2005-2006 Pierre Spring, mediagonal ag <pierre.spring@mediagonal.ch>
 * @version $Id$
 */

interface HGKMediaLib_PlaylistInterface{

    /**
     * Add an entity ($entityID) to the playlist defined by $playlistID 
     * of the user ($sessionID) at a given $position. Return the name of 
     * the entity on success as a string, false else.
     * 
     * @param mixed $session 
     * @param mixed $playlistID 
     * @param mixed $entityID 
     * @param int $position
     * @access public
     * @return mixed
     */
    public function addEntityToPlaylist($session, $playlistID, $entityID, $position = 0);
    
    /**
     * Create a new playlist for a given user. Return the id of the new playlisit
     * on success, false else.
     * 
     * @param mixed $session 
     * @param mixed $playlistName 
     * @access public
     * @return mixed
     */
    public function createPlaylist($session, $playlistName);
    
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
    public function getPlaylists($session, $user = 'self', $lang = 'de');

    /**
     * Remove an entity ($entityID) from playlist defined by $playlistID of the user ($sessionID). 
     * Return true by success, false else.
     * 
     * @param mixed $session 
     * @param mixed $playlistID 
     * @param mixed $entityID 
     * @access public
     * @return boolean
     */
    public function removeEntityFromPlaylist($session, $playlistID, $entityID);
    
    /**
     * Update items in the playlist, given an array of entity id's
     * 
     * array(
     *         0 => 'id932nsds023345'  
     *         1 => 'id932nsds023234'
     *         2 => 'id932nsd234s023'
     *         ...
     *         n => 'id932ns22ds4023'
     * )
     * 
     * @param mixed $session 
     * @param mixed $playlistID 
     * @param mixed $array 
     * @access public
     * @return boolean
     */
    public function updatePlaylist($session, $playlistID, $array);
    
    /**
     * Remove a playlist defined by $playlistID of the user ($sessionID). 
     * Return true by success, false else.
     * 
     * @param mixed $session 
     * @param mixed $playlistID 
     * @access public
     * @return boolean
     */
    public function removePlaylist($session, $playlistID);

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
    public function setPlaylistAuthorization($session, $playlistID, $authorization);
}

?>
