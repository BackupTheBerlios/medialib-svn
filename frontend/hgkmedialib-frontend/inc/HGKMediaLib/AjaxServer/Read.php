<?php
define("TODAY", date("Y M d"));

/**
 * Ajax front to the lib's reading interface
 *  
 * Copyright 2005-2006 Hannes Gassert, mediagonal Ag <hannes.gassert@mediagonal.ch>
 *
 * See the enclosed file COPYING for license information (GPL). If you
 * did not receive this file, see http://www.fsf.org/copyleft/gpl.html.
 *
 * @author  Gerhard Andrey <gerhard.andrey@mediagonal.ch>
 * @author  Hannes Gassert <hannes.gassert@mediagonal.ch>
 * @author  Pierre Spring <pierre.spring@mediagonal.ch>
 * @package HGKMediaLib_Frontend 
 * @version $Id$
 */
class HGKMediaLib_AjaxServer_Read extends HGKMediaLib_AjaxExportable  {
    
    /**
     * __construct 
     * 
     * @access protected
     * @return void
     */
    function __construct()
    {
        parent::__construct($this);
    }
	
	function getSuggestions($ref, $string){
        $array = explode('=', $string);
        $array[0] = urldecode($array[0]);
        $array[1] = urldecode($array[1]);
        return array("Ref" => $ref, "list" => $this->_adapter->getSuggestions($array[0], $array[1]));
    }
	function getByCollection($string = "")
    {
        $this->_sanitizeInput($string);
        $result = $this->_adapter->getByCollection($string);

        // return result, this may return NULL...
        return $result;
	}

	function getByDate($date=TODAY)
    {		
        $this->_sanitizeInput($date);
        return $this->_adapter->getByDate($date);
	}

    function getFiles($entityID)
    {
        $this->_sanitizeInput($entityID);
        return $this->_adapter->getFiles($entityID);
    }
    
	function getByTitle($string = "")
    {
        $this->_sanitizeInput($string);
        return $this->_adapter->getByTitle($string);
	}
    
	function getInformation($entityID)
    {
        $this->_sanitizeInput($entityID);
        return $this->_adapter->getInformation($entityID);
	}

    function getSubTree($entityID)
    {
        $this->_sanitizeInput($entityID);
        return $this->_adapter->getSubTree($entityID);
    }

    function getThumbs()
    {
        return $this->_adapter->getThumbs();
    }

    function search($search, $page = 1)
    {
        return $this->_adapter->search($search, $page);
    }
}
?>
