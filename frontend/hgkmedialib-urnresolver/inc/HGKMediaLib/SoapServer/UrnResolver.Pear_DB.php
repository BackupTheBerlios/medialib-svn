<?php
/**
 * Implementation of the URN resolver
 * 
 * Copyright 2005-2006 mediagonal Ag <info@mediagonal.ch>
 *
 * See the enclosed file COPYING for license information (GPL 2). If you
 * did not receive this file, see http://www.fsf.org/copyleft/gpl.html.
 *
 * @author  Yves Peissard <yves@mediagonal.ch>
 * @package HGKMediaLib
 * @subpackage HGKMediaLib_Urnresolver
 * @version $Id$
 */

	
require_once('../../../conf/config.php');

class HGKMediaLib_SoapServer_UrnResolver implements HGKMediaLib_UrnResolverInterface{


	
	public function __construct(){
		$this->mysqlConnect(USER, PASS, TABLE, HOST);
	
	}


	public function add($user, $passwd, $collection, $signature, $media, $sequence, $path, $provider){	
	$urnString = "urn://".$provider."/".$collection."/".$signature."/".$media."/"; // composing the urn string
	
	if ($sequence != '' OR $sequence == NULL) 										// if a sequence is specified, add it to the urn
		{
			$urnString .= $sequence;
		}
	
	mysqlConnect(USER, PASS, TABLE, HOST); 					// connecting mysql
	
	$query = "SELECT urn FROM urn WHERE urn = '$urnString'"; 						// build query string
	$result = mysql_query($query);	   												// execute mysql query
	$rows = mysql_num_rows($result);  												// count the number of results
	
	if ($rows == 0) 																// verifying if the entry already exists
		{
			$query = "INSERT INTO urn (path, urn) VALUES ('".$path."', '".$urnString."')";
			$result = mysql_query($query);
		}
	
	mysql_close(); 																	// close mysql connection			
	return $urnString;  
	} // end of: public function add()
	
	public function update($user, $passwd, $urn, $path){
	mysqlConnect(USER, PASS, TABLE, HOST); 					//connecting mysql
	$query= "UPDATE urn SET path = '$path' WHERE urn = '$urn'"; 					// building query string
	$result = mysql_query($query);													// execute mysql query
	mysql_close(); // close mysql connection
	} // end of: public function update()
	
	public function delete($user, $passwd, $urn){
	mysqlConnect(USER, PASS, TABLE, HOST); 					//connecting mysql
	$query = "DELETE FROM urn WHERE urn = '$urn'";				 					// building query string
	$result = mysql_query($query);													// execute mysql query
	mysql_close(); // close mysql connection
	} // end of function remove
	
	public function resolve($sessionId, $urn){	
		mysqlConnect(USER, PASS, TABLE, HOST); 					// connecting mysql
		$urls = array(); 																// initialise the urls array to be returned
		
		// wildcard parsing
		//TODO: if there is more than one *, this doesnt work anymore, but first we have to know how the possible wildcard could be. To know the wildcard design, we should take contact with Silvan (BitFlux)
		$wildcard = explode("*", $urn);	 												// make array with hashed values, separator: *
		$substring = $wildcard[0];														// take everything before the * in the urn string
		
		$query = "SELECT path FROM urn WHERE urn LIKE '".$substring."%'";				// building query string
		$result = mysql_query($query);													// execute mysql query
		$rows = mysql_num_rows($result);												// count the number of results
		
		if ($rows == 0) 																// check if the specified urn exists
			{
				$urls[] = "URN not found!";												// put error message into urls array witch is returned
			}
		else
			{
				while ($row = mysql_fetch_row($result))
					{
						$urls[] = $row[0]; 												// push the paths into the urls array
					}
				
			}
		
		mysql_close(); 																	// close mysql connection
		return $urls;
		
	} // end of: public function resolve()
	
} // end of: class HGKMediaLib_SoapServer_URN implements HGKMediaLib_URNInterface
	
	if (strtoupper($_SERVER['REQUEST_METHOD']) == 'GET' && isset($_GET['wsdl'])) {
	header("content-type: text/xml");
	readfile(HKGMEDIALIB_WSDL_BASEDIR . '/UrnResolver.wsdl');
	exit;
}

// should this be in an external file? where to put the file? is there a standard?
function mysqlConnect($user, $passwd, $db, $host){
	#make the connection.  If there is a problem, print out a helpful error message
	mysql_connect($host, $user, $passwd) or die(mysql_error('Can not connect to mysql'));
	mysql_select_db($db) or die(mysql_error('Could not connect to mysql database'));
}

$soapServerReadInstance = new SoapServer(HKGMEDIALIB_WSDL_BASEDIR . 'UrnResolver.wsdl');
$soapServerReadInstance->setClass("HGKMediaLib_SoapServer_UrnResolver");
$soapServerReadInstance->handle();

?>
