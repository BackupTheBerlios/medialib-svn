<?php

require_once('../../../conf/config.php');

header("content-type: text/plain");

try {
    echo HKGMEDIALIB_WSDL_BASEDIR . 'Read.wsdl';
    $client = new SoapClient(HKGMEDIALIB_WSDL_BASEDIR . '/Read.wsdl');
    
    echo "Functions available:\n";
    echo "********************\n";

    var_export($client->__getFunctions());
    
    echo "\n\n";
    echo "we get the following server:\n";
    echo "****************************\n\n";
    
    var_export($client);
   
    echo "\n\n";
    echo "we call the findByTitle():\n";
    echo "**************************\n\n";
    
    $result =  $client->find(4000, array(), array());
    var_export($result);
    
    echo "\n\n";
    echo "we call getInformation():\n";
    echo "*************************\n\n";
    
    $result =  $client->getInformation(4, 'sdf');
    var_export($result);
} catch (Exception $e) {
    //  var_export($client->__getLastResponse());
    echo "Exception caught:\n";
    var_export($e);
}
?>
