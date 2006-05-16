<?php
/**
 * Central classloader for the entire HGKMediaLib frontend, here used for the DUMMY backend
 * 
 * Copyright 2005-2006 mediagonal Ag <hannes.gassert@mediagonal.ch>
 *
 * See the enclosed file COPYING for license information (GPL 2). If you
 * did not receive this file, see http://www.fsf.org/copyleft/gpl.html.
 *
 * @author  Hannes Gassert <hannes.gassert@mediagonal.ch>
 * @package HGKMediaLib
 * @subpackage HGKMediaLib_Frontend 
 * @version $Id$
 * @param string Name of the class to be loaded
 */
function __autoload($className){

	$specialDirs = array('Exception', 'Interface', 'Adapter', 'Factory', );

	$path = str_replace(array('_', '..'), DIRECTORY_SEPARATOR, $className) . '.php';

	// fast an direct: load directly from the main lib dir
	if (file_exists(HGKMEDIALIB_INCLUDE . $path)){
		include_once(HGKMEDIALIB_INCLUDE . $path);
	}
	// if not found: search the include_path
	elseif (file_exists($path)) {
		include_once($path);
	}
	// if still not found: maybe it's some special case?
	else{
		foreach ($specialDirs as $dir){
			if (substr($className, - strlen($dir)) == $dir){

				if (strpos($className, HGKMEDIALIB_CLASSPREFIX) !== false) {
					$className = substr($className, strlen(HGKMEDIALIB_CLASSPREFIX) + 1);
				}

				$path = HGKMEDIALIB_INCLUDE . DIRECTORY_SEPARATOR . HGKMEDIALIB_CLASSPREFIX . DIRECTORY_SEPARATOR . $dir . DIRECTORY_SEPARATOR . $className . '.php';

				include_once($path);
				break; // no need to look farther
			}
		}
	}

	//throw a ClassNotFoundException here?
}


function setErrorLevel(){

	// we could define a custom error handler / logger here, too..

	if (defined('DEBUG_LEVEL')) {

		switch (DEBUG_LEVEL){

			case 0 : {
				ini_set('display_errors', 'off');
				error_reporting(0);
				break;
			}
			case 2 : {
				ini_set('display_errors', 'on');
				error_reporting(E_ERROR | E_WARNING | E_PARSE);
				break;
			}
			case 3 : {
				ini_set('display_errors', 'on');
				error_reporting(E_ALL | E_STRICT);
				break;
			}
			default : {
				ini_set('display_errors', 'off');
				error_reporting(0);
			}
		}
	}
}

function setCacheSettings(){
	if (!define('DO_CACHING')) {
		return ;
	}
	ini_set('soap.wsdl_cache_enabled', intval(DO_CACHING)); 
}

/**
 * Thrown when an application tries to load in a class through its
 * string name but no definition for the class with the specified name 
 * could be found.
 *
 * @author unascribed
 * @see ClassNotFoundException.java	
 
class ClassNotFoundException extends Exception {

	private $className;

	function __construct($className){
		$this->className = $className;
	}

	function toString(){
		return "Unable to load {$this->className} in " . HGKMEDIALIB_INCLUDE . ':' . get_include_path();
	}
}
*/ 
?>
