<?php
/**
 * Interface for read / write SOAP access to the backend
 *  
 * Copyright 2005-2006 Franz-Ferdinand Lehnert, winet Network Solutions AG <franz.lehnert@winet.ch>
 *
 * See the enclosed file COPYING for license information (GPL). If you
 * did not receive this file, see http://www.fsf.org/copyleft/gpl.html.
 *
 * This Interface declares the methods used by the soap interface for the admin frontend of the HGKZ video library.
 * It contains methods for reading and writing. The read methods are the same like in the ReadingInterface for the user frontend.
 * The objects returned by the read methods are extended by further values.
 *
 * Generally speaking, there are three kinds of methods: find*(),get*() and set*(). While finder methods always return a list of pointers 
 * to entities, getter methods always return a data structure representing an entity.
 * Setter methods send the modified or new data structures back to the backend.
 * 
 * @package HGKMediaLib
 * @author Franz-Ferdinand Lehnert <franz.lehnert@winet.ch>
 * @version $Id$
 */

interface HGKMediaLib_AdminReadWriteInterface
{
    /**
     * Find using an extended query.
     *
     * One clause looks like this:
     * array(
     *  'connector' => {AND, OR}
     *  'subject'   => '..' // name of property to be matched against, set is to be defined
     *  'predicate' => {'=', '~', '!=', '!~', '<=>'} // equal, like, not equal, not like, between
     *  'object'    => '..' // property value to match
     *  )
     *
     *  exmaple for date BETWEEN date1 AND date2:
     *
     * $clauses = array(
     *      'connector' => 'AND',
     *      'subject' => 'date',
     *      'predicate' => '<=>',
     *      'object' => '2004[-06[-01]] AND 2004[-07[-03]]'
     * );
     *
     * $clauses can contain any number of these.
     * 
     * The $sortOrder is an associative array, with the lable as key, and the order as value, e.g.
     * array(
     *   'insertionDate' => 'descending'
     *   'workTitle'     => 'ascending'
     *   )
     *   
     * 
     * @param string $sessionId
     * @param array $clauses 
     * @param array $sortOrder
     * @param int $limit
     * @param string $lang 
     *
     * @access public
     * @return array of HGKMediaLib_Struct_AdminEntity objects
     */    
    public function find($sessionId, $clauses, $sortOrder, $limit='', $lang='de');

    /**
     * getInformation() returns a HGKMediaLib_Struct_WorkTree object containing
     * all data of all entities belonging to a work
     * 
     * @param string $sessionId 
     * @param mixed $id database entity id of a Set, Instance or Work
     * @param string $lang
     *
     * @access public
     * @return object HGKMediaLib_Struct_WorkTree
     */
    public function getInformation($sessionId,$id,$lang='de');

    /**
     * The setInformation() method sends a HGKMediaLib_Struct_AdminEntity object
     * to the backend
     * 
     * @param string $sessionId 
     * @param integer id data base entity id of work, instance, set, medium, 
     * person, group or collection
     * @param object HGKMediaLib_Struct_AdminEntity contains all data of the entity
     *
     * @access public
     * @return boolean true if update/insert was ok, otherwise false
     */
    public function setInformation($sessionId,$id,$AdminEntity);
  
   /**
     * getNebisData( ) method returns a HGKMediaLib_Struct_Nebis object
     * 
     * @param string $sessionId 
     * @param mixed $id database entity id of a Set, Instance or Work
     *
     * @access public
     * @return object HGKMediaLib_Struct_Nebis
     */
    public function getNebisData($sessionId,$id);

   /**
     * setNebisData( ) sends new/updated nebis data to the backend
     * 
     * @param string $sessionId 
     * @param mixed $id database entity id of a Set, Instance or Work
     * @param object HGKMediaLib_Struct_Nebis with the updated nebis data
     *
     * @access public
     * @return boolean true, if update/insert was ok, otherwise false
     */
    public function setNebisData($sessionId,$id,$Nebis);

    /**
     * The getSuggestions() methode is used to retrive information used for aided
     * input during advanced search in the frontend.
     *
     * the $mode is defined by one of the following strings:
     *
     * collection
     * language  (long version)
     * country   (long version)
     * actor    
     * director 
     * author   
     * publisher (i.e. sender OR studio)
     * keywords 
     * 
     * @param string $sessionId 
     * @param string $mode 
     *
     * @access public
     * @return array of String
     */
    public function getSuggestions($sessionId,$mode);
}
?>
