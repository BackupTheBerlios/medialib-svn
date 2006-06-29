<?php
ini_set('soap.wsdl_cache_enabled', '0');
include_once('../global/customize.php');
try {
//     $wsdl_loc = "http://media2.hgkz.ch/hgkmedialib-backend/soap/inc/auth.wsdl";
    $wsdl_loc = "http://localhost/winet-backend/soap/wsdl/HgkMediaLib_Authentication.wsdl";
    echo $wsdl_loc.'<br>';

    $feed_client = new SoapClient($wsdl_loc);
    $func = $feed_client->__getFunctions();
    echo '<pre>';
    print_r($func);
    echo '</pre>';
    $feed_client = new SoapClient($wsdl_loc);
    $types = $feed_client->__getTypes();
    echo '<pre>';
    print_r($types);
    echo '</pre>';

} catch (SoapFault $f) {
    $fault  = "SOAP Fehler:<br>faultcode: {$f->faultcode}<br>";
    $fault .= "faultstring: {$f->faultstring}<br>";
    $fault .= "faultactor: {$f->faultactor}<br>";
    $fault .= "faultdetail: {$f->detail}<br>";
    die ($fault);
}
?>