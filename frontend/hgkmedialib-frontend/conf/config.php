<?php
/**
 * Main configuration file 
 *  
 * Copyright 2005-2006 Hannes Gassert, mediagonal Ag <hannes.gassert@mediagonal.ch>
 *
 * See the enclosed file COPYING for license information (GPL 2). If you
 * did not receive this file, see http://www.fsf.org/copyleft/gpl.html.
 *
 * @author  Hannes Gassert <hannes.gassert@mediagonal.ch>
 * @package HGKMediaLib
 * @version $Id$
 */
define('HGKMEDIALIB_INCLUDE', realpath(dirname(__FILE__) . '/../inc') . DIRECTORY_SEPARATOR);

define('HGKMEDIALIB_CLASSPREFIX', 'HGKMediaLib');

define('HKGMEDIALIB_WSDL_BASEDIR', $_SERVER['DOCUMENT_ROOT'] .'/hgkmedialib-backend/wsdl/');

define('HKGMEDIALIB_WSDL_BASEURL', 'http://'. $_SERVER['HTTP_HOST'] . '/hgkmedialib-backend/wsdl/');
	
set_include_path(HGKMEDIALIB_INCLUDE . PATH_SEPARATOR . get_include_path());

require_once('autoload.php');

define('DEBUG_LEVEL', 2); // 0 = off = default, 3 = max ..
define('DO_CACHING', false); 

// automated settings
include_once(HGKMEDIALIB_INCLUDE . 'autoload.php');
setErrorLevel();
setCacheSettings();
set_include_path(HGKMEDIALIB_INCLUDE . PATH_SEPARATOR . get_include_path());
?>
