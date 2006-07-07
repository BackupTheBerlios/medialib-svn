<?php

require_once('../../../conf/config.php');

header("content-type: text/plain");
    $clause = array(
        array(
            'connector' => '',
            'subject' => 'Title',
            'predicate' => '~',
            'object' => 'mord'
        )
    );
    $order = array(
        'Title' => 'asc'
    );


try {
    $client = new SoapClient(HKGMEDIALIB_WSDL_BASEDIR . '/Auth.toDummyServer.wsdl');
    //$client = new SoapClient('http://media1.hgkz.ch/winet-backend/soap/wsdl/HgkMediaLib_Authentication.wsdl');
    echo "we call getSession('user', 'pwd', 'domain') - Returns a new session id:\n";
    echo "*******************************************\n\n";
   
    $session  = $client->getSession('user', 'pwd', 'domain');
    var_export($session);
    echo "\n";
    echo "\n";

    echo HKGMEDIALIB_WSDL_BASEDIR . 'Read.wsdl';
    $client = new SoapClient(HKGMEDIALIB_WSDL_BASEDIR . '/Read.toDummyServer.wsdl');
    //$client = new SoapClient('http://media1.hgkz.ch/winet-backend/soap/wsdl/HgkMediaLib_Reading.wsdl');
    
    echo "Functions available:\n";
    echo "********************\n";

    var_export($client->__getFunctions());
    
    echo "\n\n";
    echo "we get the following server:\n";
    echo "****************************\n\n";
    
    var_export($client);
   
    echo "\n\n";
    echo "we call the getSuggestions():\n";
    echo "*****************************\n\n";
    
    $result =  $client->getSuggestions($session, "actor");
    var_export($result);
    
    echo "\n\n";
    echo "we call the findByTitle():\n";
    echo "**************************\n\n";
    
    $result =  $client->find($session, $clause, $order, 20, 'de');
    var_export($result);
    
    echo "\n\n";
    echo "we call getInformation():\n";
    echo "*************************\n\n";
    
   $result =  $client->getInformation($session, 99840, 'de');
    var_export($result);
} catch (Exception $e) {
    //  var_export($client->__getLastResponse());
    echo "Exception caught:\n";
    var_export($e);
}
?>
