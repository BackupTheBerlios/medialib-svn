<?php
require_once 'PHPUnit2/Framework/TestCase.php';
if (!isset($_SERVER['DOCUMENT_ROOT'])) $_SERVER['DOCUMENT_ROOT'] = "/srv/www/htdocs";
if (!isset($_SERVER['HTTP_HOST'])) $_SERVER['HTTP_HOST'] = "media1.hgkz.ch";
$_SERVER['DOCUMENT_ROOT'] = "/srv/www/htdocs";
$_SERVER['HTTP_HOST'] = "media1.hgkz.ch";
require_once('../conf/config.php');

abstract class SoapTest extends PHPUnit2_Framework_TestCase {

    private $_client;
    private $_server;
    private $_testType;

    function setUp($type)
    {
        $this->_testType = $type;
        $wsdlPath = HKGMEDIALIB_WSDL_BASEDIR . $this->_testType . '.wsdl';
        $this->_client = SoapClientFactory::getClient($wsdlPath);
        $serverClassName = 'HGKMediaLib_SoapServer_' . $this->_testType;
        $this->_server = new $serverClassName();
    }
    
}

?>
