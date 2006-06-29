<?php
	function showObject($aObject) {
		// print_r($aObject);
		$aCounter = 0;
		foreach ($aObject as $aObjectItem) {
			$html .= '<div class="leaf"><a href="javascript:toggleBox(' . $aCounter . ')" class="toggle">+</a>' . '<span class="linktitle">Movie</span>';
			$html .= '<div class="items" id="' . $aCounter . '">';
			$html .= '<table class="itemtable">';
			while (list ($key, $value) = each ($aObjectItem)) {
				$html .= '<tr><td class="label">' . $key . '</td><td class="objectitem">' . $value . '</td></tr>';
			}
			$html .= '</table>';
			$html .= '</div>';
			$html .= '</div>';
			$aCounter = $aCounter + 1;
		/*
		
			for (i = 0; i < count($aObjectItem); i++) {
			// print_r (get_object_vars($aObjectItem));
			if (is_object($aObjectItem)) {
				echo '<div class="leaf"><a href="javascript:toggleBox(' . $aCounter . ')" class="toggle">+</a>' . '<span class="linktitle">Object</span>';
				showObject($aObjectItem, $aCounter++);
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
	
	function showCollection($aObjectitem) {
		print_r(get_object_vars($aObjectItem));
		$html = showString($aObjectitem );
		
		return $html;
	}
	
	function showCollectionID($aObjectitem) {
		$html = showMixed($aObjectitem);
		
		return $html;
	}
	
	function showDate($aObjectitem) {
		$dateArray = explode(":", $aObjectitem);
		$html = $dateArray[2] . '.' . $dateArray[1] . '.' . $dateArray[0];
		
		return $html;
	}
	
	function showString($aString) {
		return '<p>' . $aString . '</p>';
	}

	function showMixed($aObjectitem) {
		return '<p>' . $aObjectitem . '</p>';
	}


	function showHTMLHeader() {
	$html = '
	<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
        "http://www.w3.org/TR/html4/loose.dtd">
       
<head>
    <meta http-equiv="pragma" content="no-cache">
    <meta http-equiv="expires" content="0">
    <meta http-equiv="content-type" content="text/html; charset=iso-8859-1">
	<script src="/javascripts/prototype.js" type="text/javascript"></script>
	<script src="/javascripts/rico.js" type="text/javascript"></script>
<style type="text/css">
<!--

.wrapper {
	border-bottom: 1px dotted grey;
	border-right: 1px dotted grey;
}
.leaf {
	background: #dddddd;
	margin: 0 0 0 20px;
	font-family: Verdana;
	font-size: 11px;
	padding: 4px;
}
.itemtable {
	width: 360px;
	background: #eeeeee;
	padding: 4px;
}
.itemtable tr {
	border-bottom: 1px dotted #aaaaaa;
}
.label {
	font-weight: bold;
	width: 120px;
	text-align: right;
	margin-right: 12px;
}

.leaf_0 {
	background: url(images/level_0.gif) #ffffff;
	margin: 0 0 0 20px;
	border-top: 1px dotted grey;
	border-left: 1px dotted grey;
	padding: 4px 0px 0px 4px;
}
.leaf_1 {
	background: url(images/level_1.gif) #ffffff;
	margin: 0 0 0 20px;
	border-top: 1px dotted grey;
	border-left: 1px dotted grey;
	padding: 4px 0px 0px 4px;
}
.leaf_2 {
	background: url(images/level_2.gif) #ffffff;
	margin: 0 0 0 20px;
	border-top: 1px dotted grey;
	border-left: 1px dotted grey;
	padding: 4px 0px 0px 4px;
}
.items
{
	display: none;
}
.wrapper .toggle {
	margin-left: 2px;
}
.wrapper .title {
	margin-left: 24px;
}
.wrapper .linktitle {
	margin-left: 12px;
}

-->
</style>


<script type="text/javascript" language="javascript">
<!--

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

<body bgcolor="#FFFFFF">
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
include_once('../../global/customize.php');
try {
//     $wsdl_loc = "http://media1.hgkz.ch/winet-backend/soap/wsdl/HgkMediaLib_MetaDataFeed.wsdl";

    /* get a soap session on media1 */
    $auth_wsdl_loc = "http://localhost/winet-backend/soap/wsdl/HgkMediaLib_Authentication.wsdl";

    session_start();
    if (!$_SESSION['HgkMediaLib_Session']) {
        $auth_client = new SoapClient($auth_wsdl_loc);
//         $function_list = $auth_client->__getFunctions();
//         echo '<pre>function_list:';
//         print_r($function_list);
//         echo '</pre>';
//         $type_list = $auth_client->__getTypes();
//         echo '<pre>type_list:';
//         print_r($type_list);
//         echo '</pre>';
//         exit;
        $session_id = $auth_client->getSession('test', 'test', 'hgkz');
        $_SESSION['HgkMediaLib_Session'] = $session_id;
    } else {
        $session_id = $_SESSION['HgkMediaLib_Session'];
    }
    $clauses = array(
        array(
            'connector' => '',
            'subject' => 'Titel',
            'predicate' => '~',
            'object' => 'Mord'
        )
	);
    $sort_order = array(
        'Titel' => 'asc'
    );
    $limit = '';
    $lang = 'de';
} catch (SoapFault $f) {
    $fault  = "SOAP Fehler:<br>faultcode: {$f->faultcode}<br>";
    $fault .= "faultstring: {$f->faultstring}<br>";
    $fault .= "faultactor: {$f->faultactor}<br>";
    $fault .= "faultdetail: {$f->detail}<br>";
    die ($fault);
}


//	$result_array = new cFoundInformation;
    /* search for entries in hgkmedialib db */
    $read_wsdl_loc = "http://media1.hgkz.ch/winet-backend/soap/wsdl/HgkMediaLib_Reading.wsdl";
    $read_client = new SoapClient($read_wsdl_loc);
    $function_list = $read_client->__getFunctions();

    $result_array = $read_client->find($session_id,$clauses,$sort_order,$limit,$lang);

	echo showHTMLHeader();
	echo showObject($result_array, 0);
	echo showHTMLFooter();
/*
	
	
    echo '<pre>result:';
    echo 'Result Array:<br/>';
    print_r($result_array);
    $result_array = $read_client->getInformation($session_id, 4545, "de");
    print_r($result_array);
    echo '</pre>';
*/
?>