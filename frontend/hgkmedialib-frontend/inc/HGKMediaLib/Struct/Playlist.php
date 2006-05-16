<?php
class HGKMediaLib_Struct_Playlist{

    /**
     * An associative array containing playlist data as follows:
     *
     * array
     * (
     *      ['IDooo03sdfw44'] => 'Sex And The City - Season 2'
     *      ['IDoo234sdg34vw4'] => 'Mozart - Requien - Slovak Philharmonica' 
     *      ['IDoooo23adcw43fv'] => 'Slayer - Tour 2005 - FriSon Fribourg, CH' 
     * )
     *
     * The array associates the name of entities (i.e. work, instanze or set)
     * to their winetDB id. The order in which the key/value pairs are given in this
     * array reflects the order a uses has given to her playlist.
     * 
     * @var array
     * @access public
     */
    public $array;

    /**
     * the authorization of a playlist, where
     * 
     *     'private'      is a list, that only the ures that created it can see
     *     'public read'  is a list, that everyone can read
     *     'public write' is a list, that everyone can read and add/remove
     *                    items to/from
     * 
     * @var string
     * @access public
     */
    public $authorization = 'private';
    
    /**
     * the winetDB id of the playlist
     *  
     * @var mixed
     * @access public
     */
    public $id;

    /**
     * the name of a playlist 
     * 
     * @var string
     * @access public
     */
    public $name;
}
?>
