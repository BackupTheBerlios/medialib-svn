<?php

require_once('../../../conf/config.php');

header("content-type: text/plain");

try {
    $client = new SoapClient(HKGMEDIALIB_WSDL_BASEDIR . '/Playlist.wsdl', array('trace' => 1));
    
    echo "Functions available:\n";
    echo "********************\n";

    var_export($client->__getFunctions());
    
    echo "\n\n";
    echo "we get the following server:\n";
    echo "****************************\n\n";
    
    var_export($client);

    echo "\n\n";
    echo "we call addEntityToPlaylist('session', 'pList', 'entityID'):\n";
    echo "************************************************************\n\n";
   
    $result = $client->addEntityToPlaylist('session', 'pList', 'entityID');
    var_export($result);
   
    echo "\n\n";
    echo "we call getPlaylists('session'):\n";
    echo "********************************\n\n";
   
    $result = $client->getPlaylists('session');
    var_export($result);
   
    echo "\n\n";
    echo "we call removeEntityFromPlaylist('session', 'pList', 'entityID'):\n";
    echo "*****************************************************************\n\n";
   
    $result = $client->removeEntityFromPlaylist('session', 'pList', 'entityID');
    var_export($result);
   
    echo "\n\n";
    echo "we call swapPositions('session', 'pList', 'entityID1', 'entityID2'):\n";
    echo "********************************************************************\n\n";
   
    $result = $client->swapPositions('session', 'pList', 'entityID1', 'entityID2');
    var_export($result);
   
    echo "\n\n";
    echo "we call setPlaylistAuthorization('session', 'pList', 'public'):\n";
    echo "***************************************************************\n\n";
   
    $result = $client->setPlaylistAuthorization('session', 'pList', 'public');
    var_export($result);
   
    
} catch (Exception $e) {
    
	var_export($client->__getLastResponse());
    echo "Exception caught:<br/>\n";
    var_export($e);
    
}
?>
