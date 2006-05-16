<?php

abstract class Adapter{
	private $_adaptee;	

    protected $_soapClient;

    public function __construct($object){
        $adapterType = substr(get_class($this), 23, strlen(get_class($this)) - 34);
        $this->_soapClient = SoapClientFactory::getClient(HKGMEDIALIB_WSDL_BASEDIR . $adapterType . '.wsdl');
    }        

    protected function _getSoapSession()
    {
        if (!isset($_SESSION['soapSession'])){
            die('error in HGKMediaLib_AjaxServer_AuthSoapAdapter::_getSoapSession()');
        }
        return $_SESSION['soapSession'];
    }
}

?>
