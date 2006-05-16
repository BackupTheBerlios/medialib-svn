<?php
/**
 * SOAP client singleton factory, or something like that.
 *  
 * Copyright 2005-2006 Hannes Gassert, mediagonal Ag <hannes.gassert@mediagonal.ch>
 *
 * See the enclosed file COPYING for license information (GPL2). If you
 * did not receive this file, see http://www.fsf.org/copyleft/gpl.html.
 *
 * @author  Hannes Gassert <hannes.gassert@mediagonal.ch>
 * @subpackage HGKMediaLib_Frontend
 * @package HGKMediaLib
 * @version $Id$
 * @see SoapClient 
 */
class SoapClientFactory{

	static $objectContainer = array();

	/**
	 * Create a new SoapClient
	 *
	 * @param string $wsdlUrl 
	 * @param array $clientOptions
	 * @return SoapClient
	 */
	static function getClient ($wsdlUrl, $clientOptions = array()) {
		$wsdlUrl = strval($wsdlUrl);

		// already stored, singletonish
		if (isset(self::$objectContainer[$wsdlUrl]) && self::$objectContainer[$wsdlUrl] instanceof SoapClient) {
			return self::$objectContainer[$wsdlUrl];
		}

		// create, store, return
		try {
			self::$objectContainer[$wsdlUrl] = new SoapClient($wsdlUrl, $clientOptions);
			return self::$objectContainer[$wsdlUrl];
		}
		catch (SoapFault $fault) {
			//! hmm.. we have to talk about error handling ..
			trigger_error("SOAP Fault: (faultcode: {$fault->faultcode}, faultstring: {$fault->faultstring})", E_ERROR);
			
		}
	}
}
?>