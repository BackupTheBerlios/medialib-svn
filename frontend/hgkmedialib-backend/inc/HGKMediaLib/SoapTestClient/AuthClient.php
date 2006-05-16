<?php

require_once('../../../conf/config.php');

header("content-type: text/plain");

try {
    $client = new SoapClient(HKGMEDIALIB_WSDL_BASEDIR . '/Auth.wsdl');
   
    echo "Functions available:\n";
    echo "********************\n\n";

    var_export($client->__getFunctions());
    
    echo "\n\n";
    echo "we get the following server:\n";
    echo "****************************\n\n";
    
    var_export($client);
    
    echo "\n\n";
    echo "we call getSession('user', 'pwd', 'domain') - Returns a new session id:\n";
    echo "*******************************************\n\n";
   
    $result = $client->getSession('user', 'pwd', 'domain');
    var_export($result);
   
    echo "\n\n";
    echo "we call dropSession('session') - Returns a new anonymous session id:\n";
    echo "******************************\n\n";
   
    $result = $client->dropSession('session');
    var_export($result);
    
    echo "\n\n";
    echo "we call getAnonymousSession() - Returns a new anonymous session id:\n";
    echo "*****************************\n\n";
   
    $result = $client->getAnonymousSession();
    var_export($result);
   
    echo "\n\n";
    echo "we call getUserData() - Returns a HGKMediaLib_Struct_User object:\n";
    echo "*********************\n\n";
   
    $result = $client->getUserData($client->getAnonymousSession());
    var_export($result);
   
   
   

} catch (Exception $e) {

    echo "Exception caught:\n";
    var_export($e);

}
?>
