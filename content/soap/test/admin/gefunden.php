<?php
	function showInformationHeader($aObject) {
		// Gibt die Headerdaten eines Objects aus
		// Ausgabeform: Tabelle
		// CSS: für label (informationobjectlabel), für value (informationobjectvalue)

		$aLabelCSS = 'informationobjectlabel';
		$aValueCSS = 'informationobjectvalue';
		$html .= '<h3>Information Header-Daten</h3>';
		$html .= makeTableOpen ('informationTable');
		$html .= makeTableRow ('collection', $aObject->collection, $aLabelCSS, $aValueCSS);
		$html .= makeTableRow ('collectionID', $aObject->collectionId, $aLabelCSS, $aValueCSS);
		$html .= makeTableRow ('date', $aObject->date, $aLabelCSS, $aValueCSS);
		$html .= makeTableRow ('description', $aObject->description, $aLabelCSS, $aValueCSS);
		$html .= makeTableRow ('id', $aObject->id, $aLabelCSS, $aValueCSS);
		$html .= makeTableClose();
		
		return $html;
	}
	
	function makeTableOpen($aCSS) {
		$html = '<table class="' . $aCSS . '">';
		
		return $html;
	}
	

	function makeTableClose() {
		$html = '</table>';
		
		return $html;
	}

	
	function makeTableRow ($label, $value, $aLabelCSS, $aValueCSS) {
		$html .= '<tr><td class="' . $aLabelCSS . '">' . $label . '</td><td class="' . $aValueCSS . '">' . $value . '</td></tr>';
		
		return $html;
	}
	
	
	function showObjectsData ($aDataObject, $aItem) {
		// Gibt innerhalb des «informationBlocks» das an item $aItem liegende «data»-Array aus
		// Ausgabeform: Tabelle
		// CSS: für label (dataobjectlabel), für value (dataobjectvalue)
		
		$html .= '<h3>objects data innerhalb des «informationBlocks» für item ' . $aItem . '</h3>';
		$html .= makeTableOpen('');
		
		foreach ($aDataObject as $aDataObjectItem) {
			$aLabelCSS = 'dataobjectlabel';
			$aValueCSS = 'dataobjectvalue';
			$html .= makeTableRow ($aDataObjectItem->name, $aDataObjectItem->value, $aLabelCSS, $aValueCSS);
		}
		$html .= makeTableClose();
		
		return $html;
	}
	
	function showSubtreeData ($aSubtreeData, $aItem) {
		// Gibt innerhalb eines subtree das an item $aItem liegende «data»-Array aus
		// Ausgabeform: Tabelle
		// CSS: für label (dataobjectlabel), für value (dataobjectvalue)
		
//		$html .= makeTableOpen('subtreedata');
		
		$aLabelCSS = 'dataobjectlabel';
		$aValueCSS = 'dataobjectvalue';
		if ($aSubtreeData->value) {
			$html .= makeTableRow ($aSubtreeData->name, $aSubtreeData->value, $aLabelCSS, $aValueCSS);
		} else if ($aSubtreeData->urn) {
			$html .= makeTableRow ($aSubtreeData->name, $aSubtreeData->urn, $aLabelCSS, $aValueCSS);
		}
//		$html .= makeTableClose();
		
		return $html;
	}
	
/*
	function showInformationBlock ($aObject, $aItem) {
		$html = showInformationData($aObject);
		$html .= showInformationBlocksData($aObject->informationBlocks[$aItem]->data);
		
		return $html;
	}
*/

	function showsubtree($aObject, $aNodeLevel) {
		global $gCounter, $gLevelBackground;
		
		
		for($i=0;$i<count($aObject);$i++) {
			if ($aObject[$i]->subtree) {
				$html .= '<div class="node" id="node' . $gCounter . '" style="background: ' . $gLevelBackground[$aNodeLevel] . '"><a href="javascript:toggleBox(\'ID' . $gCounter . '\')" class="toggle">&nbsp;&#8226;&nbsp;' . '<span class="linktitle">' . $aObject[$i]->title . '</span></a>';
				$html .= '<div class="nodeitems" id="ID' . $gCounter . '">';
				$html .= '<div class="nodedescription"><span class="nodedescription">Description</span>' . $aObject[$i]->description . '</div> <!-- nodedescription -->';
				
				$gCounter = $gCounter + 1;
				$aNodeLevel = $aNodeLevel + 1;
				$html .= showsubtree($aObject[$i]->subtree, $aNodeLevel);
				$aNodeLevel = $aNodeLevel - 1;
			} else {
				$html .= '<div class="nodecontent">';
				$html .= makeTableOpen('subtreedata');
				for ($j=0;$j<count($aObject[$i]->data);$j++) {
					$html .= showSubtreeData($aObject[$i]->data[$j], $j);
				}
				$html .= showSubtreeData($aObject[$i], $j);
//				$html .= '<p><span class="label" id="' . $aObject[$i]->id . '">' . $aObject[$i]->name . ': </span>' . $aObject[$i]->urn . '</p>';
/*
				$html .= '<p><b>id: </b>' . $aObject[$i]->id . '</p>';
				$html .= '<p><b>name: </b>' . $aObject[$i]->name . '</p>';
				$html .= '<p><b>urn: </b>' . $aObject[$i]->urn . '</p>';
*/
				$html .= makeTableClose();
				$html .= '</div> <!-- nodecontent -->';
			}
		}
		$html .= '</div> <!-- nodeitems -->';
		$html .= '</div> <!-- node -->';
		return $html;
	}


	function showObject($aObject) {
		// print_r($aObject);
		$gCounter = 0;
		foreach ($aObject as $aObjectItem) {
			$html .= '<div class="leaf"><a href="javascript:toggleBox(' . $gCounter . ')" class="toggle">+</a>' . '<span class="linktitle">' . $aObjectItem->title . '</span>';
			$html .= '<div class="items" id="' . $gCounter . '">';
			$html .= '<table class="itemtable">';
			while (list ($key, $value) = each ($aObjectItem)) {
				$html .= '<tr><td class="label">' . $key . '</td><td class="objectitem">' . $value . '</td></tr>';
			}
			$html .= '</table>';
			$html .= '</div>';
			$html .= '</div>';
			$gCounter = $gCounter + 1;
		/*
		
			for (i = 0; i < count($aObjectItem); i++) {
			// print_r (get_object_vars($aObjectItem));
			if (is_object($aObjectItem)) {
				echo '<div class="leaf"><a href="javascript:toggleBox(' . $gCounter . ')" class="toggle">+</a>' . '<span class="linktitle">Object</span>';
				showObject($aObjectItem, $gCounter++);
				echo '</div>';
			}
			else if (get_object_vars($aObjectItem)) {
				print_r (get_object_vars($aObjectItem));
			} else {
				echo '<p>' . $aObjectItem . '</p>';
			}
		*/
		}
		return $html;
	}
	
	function showAccordionObject($aObject) {
		$gCounter = 0;
		$html = '<div id="accordionDiv">';
//		echo 'DOCUMENT_ROOT: ' . $_SERVER["DOCUMENT_ROOT"] . '<br/>';
//		echo 'PHP_SELF: ' . $_SERVER["PHP_SELF"] . '<br/>';

		$aBaseURL = 'http://media1.hgkz.ch' . $_SERVER["PHP_SELF"];
		foreach ($aObject as $aObjectItem) {
			$html .= '<div id="overview' . $gCounter . 'Panel">';
			$html .= '<div id="overview' . $gCounter . 'Header">';
			$html .= '<a href="' . $aBaseURL . '?action=getinformation&id=' . $aObjectItem->id . '">' . $aObjectItem->title . '</a>';
			$html .= '</div>';
			$html .= '<div id="panel' . $gCounter . 'Content">';
			$html .= '<table class="itemtable">';
			while (list ($key, $value) = each ($aObjectItem)) {
				$html .= '<tr><td class="label">' . $key . '</td><td class="objectitem">' . $value . '</td></tr>';
			}
			reset($aObjectItem);
			$html .= '</table>';
			$html .= '</div>';
			$html .= '</div>';
			$gCounter = $gCounter + 1;
		}
			$html .= '</div>';
			
			return $html;
	}
	
	function showArray($aArray) {
	    echo '<pre>';
	    echo '========================================================================';
    	echo 'Result Array:<br/>';
    	print_r($aArray);
    	echo '</pre>';
	}
	


	function showHTMLHeader() {
	$html = '
';
/*
<?xml version="1.0"?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//DE"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
----
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
        "http://www.w3.org/TR/html4/loose.dtd">
*/
$html .= '
<html>
<head>
    <meta http-equiv="pragma" content="no-cache">
    <meta http-equiv="expires" content="0">
    <meta http-equiv="content-type" content="text/html; charset=iso-8859-1">
<!--	<meta http-equiv="content-type" content="text/html; charset=utf-8" />	 -->
	<script src="javascripts/prototype.js" type="text/javascript"></script>
	<script src="javascripts/rico.js" type="text/javascript"></script>
	<link href="css/styles.css" type="text/css" rel="stylesheet" />

<script type="text/javascript" language="javascript">
<!--
	
	function bodyOnLoad() {
		new Rico.Accordion( $("accordionDiv") );
	}

	function toggleBox(theID)
	{
		if (!document.getElementById(theID).style.display || document.getElementById(theID).style.display == "none")
		{
			document.getElementById(theID).style.display="block";
		}
		else
		{
			document.getElementById(theID).style.display="none";
		}
	}


//-->
</script>
</head>

<body bgcolor="#FFFFFF" onload="javascript:bodyOnLoad()">
';
return $html;
	}
	
	
	function showHTMLFooter() {
		$html = '
				</body>
			</html>
		';
		return $html;
	}
	
	
	
	
ini_set('soap.wsdl_cache_enabled', '0');
include_once('../../../global/customize.php');

/*
try {
} catch {
}
*/

//	$result_array = new cFoundInformation;
    /* search for entries in hgkmedialib db */
//    $read_wsdl_loc = "http://media1.hgkz.ch/winet-backend/soap/wsdl/HgkMediaLib_Reading.wsdl";

/*
	$read_client = new SoapClient($read_wsdl_loc);
    $function_list = $read_client->__getFunctions();
*/

	$html = showHTMLHeader();
	switch ($_GET['action']) {
		case 'getinformation':
		  // $result_array = $read_client->getInformation($session_id, $_GET['id'], "de");
		  include('getinfo.php');
		  $result_array = getInformation($session_id, $_GET['id'], "de");
		  // $result_array->subtree = initArray();
		    
/*
			$html .= showInformationHeader($result_array);
			$aItem = 0;
			$html .= showObjectsData($result_array->informationBlocks[$aItem]->data, $aItem);
*/

			$aLevelBackground = array(
									array("#afa8a3","#bab4af","#c6c2bd","#d7d4d1","#ddd8d8"),
									array("#be9994","#c7a6a2","#d0b5b2","#dfccca","#e3d3d1"),
									array("#a0a4ac","#bec2c8","#d8d8df","#e6e8eb","#f0f1f2","#f6f6f7"),
									array("#888d95","#a8acb3","#c4c7cb","#d3d5d8")
								);
			$gLevelBackground = $aLevelBackground[2];
			$gCounter = 0;
			$aNodeLevel = 0;
		    $html .= showsubtree($result_array->subtree, $aNodeLevel);
		break;
		default:
			$result_array = $read_client->find($session_id,$clauses,$sort_order,$limit,$lang);
			$html .= showAccordionObject($result_array);
			$html .= showObject($result_array);
	}
	$html .= showHTMLFooter();
	
	echo $html;
	$result = showArray($result_array->subtree);
?>