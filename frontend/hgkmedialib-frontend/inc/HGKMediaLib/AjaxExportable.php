<?php
abstract class HGKMediaLib_AjaxExportable{
    
    public $_adapter; 
    
    public function __construct($object){

        $this->_triggerAuth();
        $this->_adapter = HGKMediaLib_AdapterFactory::getAdapter($object);
    }   
    
    /**
     * _triggerAuth() checks if the client has a session at all.
     * the client gets an anonymous session if not.
     * 
     * @access protected
     * @return void
     */
    protected function _triggerAuth(){
        if (!isset($_SESSION['soapSession'])) {
            $authAdapter = new HGKMediaLib_AjaxServer_AuthSoapAdapter();
            $authAdapter->getAnonymousSession();
        }
        
    }   
    
    protected function _sanitizeInput(&$inputArray){        
        //$inputArray = array_map('utf8_encode', $inputArray);              
    }
    
}
?>
