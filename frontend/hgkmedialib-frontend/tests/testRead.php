<?php
session_start();
header("content-type: text/plain;");

error_reporting(E_ALL);
echo "attempting to load config.php\n\n";
include_once("../conf/config.php");
echo "loaded config.php\n\n";

$readServer = new HGKMediaLib_AjaxServer_Read();

var_export($_SERVER["DOCUMENT_ROOT"]);
echo "\n \n";

echo "\n\$readServer = ";
var_export($readServer);
echo "\n \n";
echo "\$readServer->getByDate():\n";
var_export($readServer->getByDate());
echo "\n \n";
echo "\$readServer->getByTitle():\n";
var_export($readServer->getByTitle());
echo "\n \n";
echo "\$readServer->getByCollection():\n";
var_export($readServer->getByCollection());
echo "\n \n";

echo "\$readServer->getInformation('entityID'):\n";
var_export($readServer->getInformation('923ns'));
echo "\n \n";
echo "\$readServer->getSubTree('entityID'):\n";
var_export($readServer->getSubTree('923ns'));
echo "\n \n";
echo "\$readServer->getFiles('entityID'):\n";
var_export($readServer->getFiles('923ns'));
echo "\n \n";
?>
