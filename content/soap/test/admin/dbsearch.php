<?php

class cObjectViewer {
	var $auth_wsdl_loc	= "http://localhost/winet-backend/soap/wsdl/HgkMediaLib_Authentication.wsdl";
	var $session_id;
	var $clauses			= array ("connector" => '',"subject" => '',"predicate" => '',"object" => '');
	var $sort_order		= array ('Titel' => 'asc');
	var $limit			= '';
	var $language		= 'de';
	
	function cObjectViewer () {
	
		ini_set('soap.wsdl_cache_enabled', '0');
		include_once('../../../global/customize.php');
		// include_once('http://media1.hgkz.ch/winet-backend/global/customize.php');
	
		try {		
			session_start();
			if (!$_SESSION['HgkMediaLib_Session']) {
				$this->session_id = $auth_client->getSession('test', 'test', 'hgkz');
				$_SESSION['HgkMediaLib_Session'] = $this->session_id;
			} else {
				$this->session_id = $_SESSION['HgkMediaLib_Session'];
			}
			$this->addClauses('','Titel','~','Mord');
			/*
			$this->clauses = array(
				array(
					'connector' => '',
					'subject' => 'Titel',
					'predicate' => '~',
					'object' => 'Mord'
				)
			);
			*/
			
		} catch (SoapFault $f) {
			$fault  = "SOAP Fehler:<br>faultcode: {$f->faultcode}<br>";
			$fault .= "faultstring: {$f->faultstring}<br>";
			$fault .= "faultactor: {$f->faultactor}<br>";
			$fault .= "faultdetail: {$f->detail}<br>";
			die ($fault);
		}
	}
	
	function getClauses ($fItem) {
		return $this->clauses[$fItem];
	}
	
	function addClauses ($fConnector, $fSubject, $fPredicate, $fObject) {
		$clause['connector'] = $fConnector;
		$clause['subject'] = $fSubject;
		$clause['predicate'] = $fPredicate;
		$clause['object'] = $fObject;
		$this->clauses[] = $clause;
	}
	
	function getLanguage () {
		return $this->language;
	}

	function setLanguage($fString) {
		$this->language = $fString;
	}
	
	function showAccordionObject($fObject) {
		$aCounter = 0;
		$html = '<div id="accordionDiv">';

		$aBaseURL = 'http://media1.hgkz.ch' . $_SERVER["PHP_SELF"];
		foreach ($fObject as $aObjectItem) {
			$html .= '<div id="overview' . $aCounter . 'Panel">';
			$html .= '<div id="overview' . $aCounter . 'Header">';
			$html .= '<a href="' . $aBaseURL . '?action=getinformation&id=' . $aObjectItem->id . '">' . $aObjectItem->title . '</a>';
			$html .= '</div>';
			$html .= '<div id="panel' . $aCounter . 'Content">';
			$html .= '<table class="itemtable">';
			while (list ($key, $value) = each ($aObjectItem)) {
				$html .= '<tr><td class="label">' . $key . '</td><td class="objectitem">' . $value . '</td></tr>';
			}
			reset($aObjectItem);
			$html .= '</table>';
			$html .= '</div>';
			$html .= '</div>';
			$aCounter = $aCounter + 1;
		}
			$html .= '</div>';
			
			return $html;
		
	}
	
	function showHTMLHeader() {
	$html = '
<?xml version="1.0" encoding="utf-8"?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

	<head>
    <meta http-equiv="pragma" content="no-cache">
    <meta http-equiv="expires" content="0">
<!--    <meta http-equiv="content-type" content="text/html; charset=iso-8859-1"> -->
	<meta http-equiv="content-type" content="text/html; charset=utf-8" />	
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
			
}
	$aViewerObject = new cObjectViewer;
	$read_wsdl_loc = "http://media1.hgkz.ch/winet-backend/soap/wsdl/HgkMediaLib_Reading.wsdl";
	$read_client = new SoapClient($read_wsdl_loc);
	$function_list = $read_client->__getFunctions();
	$html = $aViewerObject->showHTMLHeader();
	$result_array = $read_client->find($aViewerObject->session_id,$aViewerObject->clauses,$aViewerObject->sort_order,$aViewerObject->limit,$aViewerObject->language);
	$html .= showAccordionObject($result_array);
	// $html .= showObject($result_array);
	echo $html;
?>