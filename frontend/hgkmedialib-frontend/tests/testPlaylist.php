<?php
session_start();
header("content-type: text/plain;");

error_reporting(E_ALL);
echo "attempting to load config.php\n\n";
include_once("../conf/config.php");
echo "loaded config.php\n\n";

$playlistServer = new HGKMediaLib_AjaxServer_Playlist();

echo "playlist Server server: \n\n";
var_export($playlistServer);
echo "\n \n";
echo "playlistServer->getPlaylists(): \n\n";
var_export($playlistServer->getPlaylists());
echo "\n \n";
echo "playlistServer->getPlaylist(): \n\n";
$playlistKeys = array_keys($playlistServer->getPlaylists());
var_export($playlistServer->getPlaylist($playlistKeys[0]));
echo "\n \n";
echo "playlistServer->addEntityToPlaylist(): \n\n";
$playlistKeys = array_keys($playlistServer->getPlaylists());
var_export($playlistServer->addEntityToPlaylist($playlistKeys[0], "SetId".rand(100000,999999), 5));
echo "\n \n";
echo "playlistServer->removeEntityFromPlaylist(<theFirstPlaylist>, <theFirstItemOnTheList>): \n\n";
$playlistKeys = array_keys($playlistServer->getPlaylists());
$entityKeys = array_keys($playlistServer->getPlaylist($playlistKeys[0]));
var_export($playlistServer->removeEntityFromPlaylist($playlistKeys[0], $entityKeys[0]));
echo "\n \n";
echo "playlistServer->createPlaylist(): \n\n";
var_export($playlistServer->createPlaylist('new playlist ' . rand(10000, 99999)));
echo "\n \n";
echo "playlistServer->removePlaylist(<theSecondPlaylist>): \n\n";
$playlistKeys = array_keys($playlistServer->getPlaylists());
var_export($playlistServer->removePlaylist($playlistKeys[1]));
echo "\n \n";

echo "playlistServer->getPlaylist(): \n\n";
$playlistKeys = array_keys($playlistServer->getPlaylists());
var_export($playlistServer->getPlaylist($playlistKeys[0]));
echo "\n \n";

//  echo "playlistServer->swapPositions(<theFirstPlaylist>, <theFirstOnTheList>, <theSecondItemOnTheList>): \n\n";
//  $playlistKeys = array_keys($playlistServer->getPlaylists());
//  $entityKeys = array_keys($playlistServer->getPlaylist($playlistKeys[0]));
//  var_export($playlistServer->swapPositions($playlistKeys[0], $entityKeys[0], $entityKeys[1]));
//  echo "\n \n";


echo "playlistServer->getPlaylist(): \n\n";
$playlistKeys = array_keys($playlistServer->getPlaylists());
var_export($playlistServer->getPlaylist($playlistKeys[0]));
echo "\n \n";
?>
