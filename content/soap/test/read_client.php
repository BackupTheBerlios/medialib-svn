<?php
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
    echo 'session_id: '.$session_id.'<br>';

    /* search parameter */
    $clauses = array(
        array(
            'connector' => '',
            'subject' => 'title',
            'predicate' => '~',
            'object' => 'Mord'
        )
       ,
       array(
           'connector' => 'AND',
           'subject' => 'publisher',
           'predicate' => '~',
           'object' => 'ZDF'
       )
    );
    $sort_order = array(
        'Titel' => 'asc'
    );
    $limit = '';
    $lang = 'de';

    /* search for entries in hgkmedialib db */
    $read_wsdl_loc = "http://media1.hgkz.ch/winet-backend/soap/wsdl/HgkMediaLib_Reading.wsdl";
    $read_client = new SoapClient($read_wsdl_loc);/*,array(
        "trace"      => 1,
        "exceptions" => 0)
    );*/
    $function_list = $read_client->__getFunctions();
    echo '<pre>';
    echo 'Function-List:<br/>';
    print_r($function_list);
    echo '</pre>';
//     exit;

    $result_array = $read_client->find($session_id,$clauses,$sort_order,$limit,$lang);
//     $result_array = $read_client->getInformation($session_id,589,$lang);
//     print "<pre>\n";
//     print "Request :\n".htmlspecialchars($read_client->__getLastRequest()) ."\n";
//     print "Response:\n".htmlspecialchars($read_client->__getLastResponse())."\n";
//     print "</pre>";
    echo '<pre>result:';
    print_r($result_array);
    echo '</pre>';
//     $result_array = $read_client->getInformation($session_id, 4545, "de");
//     print_r($result_array);
//     echo '</pre>';

} catch (SoapFault $f) {
    $fault  = "SOAP Fehler:<br>faultcode: {$f->faultcode}<br>";
    $fault .= "faultstring: {$f->faultstring}<br>";
    $fault .= "faultactor: {$f->faultactor}<br>";
    $fault .= "faultdetail: {$f->detail}<br>";
    die ($fault);
}
?>
