<?php
session_start();
header("content-type: text/plain;");
error_reporting(E_ALL);
echo "attempting to load config.php\n\n";
include_once("../conf/config.php");
echo "loaded config.php\n\n";



$authServer = new HGKMediaLib_AjaxServer_Auth();

echo "authServerth server: \n\n";
var_export($authServer);
echo "\n \n";
echo "get user info: \n";
var_export($authServer->getUserData());
echo "\n \n";
echo "get a session: \n";
var_export($authServer->getSession("john", "Doe", "hgkz"));
echo "\n \n";
echo "get user info: \n";
var_export($authServer->getUserData());
echo "\n \n";
echo "now drop session: \n";
var_export($authServer->dropSession());
echo "\n \n";
echo "and get user info: \n";
var_export($authServer->getUserData());
echo "\n \n";
echo "and get domains: \n";
var_export($authServer->getDomains());

?>
