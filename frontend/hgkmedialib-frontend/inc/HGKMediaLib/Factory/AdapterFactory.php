<?php
/**
 * Adapter Factory, produces mainly Ajax<->Soap Adapters
 *  
 * Copyright 2005-2006 Hannes Gassert, mediagonal Ag <hannes.gassert@mediagonal.ch>
 *
 * See the enclosed file COPYING for license information (GPL). If you
 * did not receive this file, see http://www.fsf.org/copyleft/gpl.html.
 *
 * @author  Hannes Gassert <hannes.gassert@mediagonal.ch>
 * @package HGKMediaLib_Frontend 
 * @version $Id$
 */
class HGKMediaLib_AdapterFactory{
	
	static $defaultAdapterType = 'Soap';
	
	public static function getAdapter(HGKMediaLib_AjaxExportable $targetObject){
		$className = get_class($targetObject);
		$adapterClassName = $className . self::$defaultAdapterType . 'Adapter';
		return new $adapterClassName($targetObject);
	}
	
}
?>
