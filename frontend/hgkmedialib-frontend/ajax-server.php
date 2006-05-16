<?php
/**
 * Ajax front to the lib's reading interface
 *  
 * Copyright 2005-2006 Hannes Gassert, mediagonal Ag <hannes.gassert@mediagonal.ch>
 *
 * See the enclosed file COPYING for license information (GPL). If you
 * did not receive this file, see http://www.fsf.org/copyleft/gpl.html.
 *
 * @author  Hannes Gassert <hannes.gassert@mediagonal.ch>
 * @package HGKMediaLib
 * @subpackage HGKMediaLib_Frontend
 * @version $Id$
 */
@session_start();
require_once('./inc/autoload.php');
require_once('./conf/config.php');
require_once('HTML/AJAX/Server.php'); // from PEAR

class HGKMedialib_AutoAjaxServer extends HTML_AJAX_Server {

	// don't add a constructor here, HTML_AJAX_Server is easily confused..

	/**
	 * Enter description here...
	 *
	 * @param array $classNames
	 */
	public function registerClasses($classNames){

		// no need to register all classes if we know what we're doing - which is the standard case of course :)
		if (isset($_GET['stub']) && in_array($_GET['stub'], $classNames)) {
			$classNames = array($_GET['stub']);
		}

		foreach ($classNames as $className){
			$this->registerClass(new $className);  //triggers __autoload, which might throw an Exception..
		}
	}
}


$srv = new HGKMedialib_AutoAjaxServer();
$srv->registerClasses(array('HGKMediaLib_AjaxServer_Auth'));
$srv->registerClasses(array('HGKMediaLib_AjaxServer_Read'));
$srv->registerClasses(array('HGKMediaLib_AjaxServer_Playlist'));
$srv->handleRequest();
?>
