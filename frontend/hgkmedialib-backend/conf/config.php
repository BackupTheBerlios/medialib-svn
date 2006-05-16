<?php
/**
 * Main configuration file for "dummy" backend
 *   
 * Copyright 2005-2006 Hannes Gassert, mediagonal Ag <hannes.gassert@mediagonal.ch>
 *
 * See the enclosed file COPYING for license information (GPL 2). If you
 * did not receive this file, see http://www.fsf.org/copyleft/gpl.html.
 *
 * @author  Hannes Gassert <hannes.gassert@mediagonal.ch>
 * @version $Id$
 * @package HGKMediaLib
 * @subpackage HGKMediaLib_DummyBackend
 */

define('HGKMEDIALIB_INCLUDE', realpath(dirname(__FILE__) . '/../inc') . DIRECTORY_SEPARATOR); // be careful here..

define('HGKMEDIALIB_CLASSPREFIX', 'HGKMediaLib');

define('HKGMEDIALIB_WSDL_BASEDIR', $_SERVER['DOCUMENT_ROOT'] .'/hgkmedialib-backend/wsdl/');

define('HKGMEDIALIB_WSDL_BASEURL', $_SERVER['HTTP_HOST'] . '/hgkmedailib-backend/wsdl/');

define('DEBUG_LEVEL', 3); // 0 = off = default, 3 = max
	

// automated settings
include_once(HGKMEDIALIB_INCLUDE . 'autoload.php');
setErrorLevel();
set_include_path(HGKMEDIALIB_INCLUDE . PATH_SEPARATOR . get_include_path());

?>
