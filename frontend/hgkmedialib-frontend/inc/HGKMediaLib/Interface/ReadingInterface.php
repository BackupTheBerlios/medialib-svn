<?php
/**
 * Interface for reading SOAP access to the backend
 *  
 * Copyright 2005-2006 Pierre Spring, mediagonal Ag <pierre.spring@mediagonal.ch>
 *
 * See the enclosed file COPYING for license information (GPL). If you
 * did not receive this file, see http://www.fsf.org/copyleft/gpl.html.
 *
 * 
 * Interface declaring the signatures of methods being called by the HGKZ video library's frontend component.
 * The frontend is supposed to be read-only, as 
 * Presumably there will be two classes that implement this interface: one on the serving side (connected to a SOAP server), 
 * and another one on the requesting side (connected to a SOAP client generated from the server's WSDL).
 * 
 * Generally speaking, there are two kinds of methods: find*() and get*(). While finder methods always return a list of pointers 
 * to entities, getter methods always return a data structure representing an entity.
 * 
 * This interface only specifies methods request data from the metadata database as built by Winet. Queries to the storage mgmt and
 * other components are specified elsewhere, although they might fit in here from the API user's perspective.
 * 
 * @package HGKMediaLib
 * @author Pierre Spring <pierre.spring@mediagonal.ch>
 * @version $Id$
 */
interface HGKMediaLib_ReadingInterface{
        
    /**
     * Find using an extended query.
     *
     * One clause looks like this:
     * 
     * array(
     *  'connector' => {AND, OR}
     *  'subject'   => '..' // name of property to be matched against, set is to be defined
     *  'predicate' => {'=', '~', '!=', '!~'} // equal, like, not equal, not like
     *  'object'    => '..' // property value to match
     *  )
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
     * @access public
     * @return array of HGKMediaLib_Struct_Entity objects
     */    
    public function find($sessionId, $clauses, $sortOrder, $limit = 40, $lang = 'de');

    /**
     * The getInformation() method returns a HGKMediaLib_Struct_Information object
     * 
     * @param string $sessionId 
     * @param mixed $id database entity id of a Set, Instance or Work
     * @param string $lang 
     * @access public
     * @return object HGKMediaLib_Struct_Information
     */
    public function getInformation($sessionId, $id, $lang = 'de');

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
     * @access public
     * @return array of String
     */
    public function getSuggestions($sessionId, $mode);

}



?>
