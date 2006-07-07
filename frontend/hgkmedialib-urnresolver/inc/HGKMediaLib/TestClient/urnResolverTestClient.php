<?php
require_once('../../../conf/config.php');

$SoapServer = SoapClientFactory::getClient(HKGMEDIALIB_WSDL_BASEDIR . 'UrnResolver.wsdl');
//echo var_export($SoapServer->__getFunctions());
//var_export ($SoapServer->add());
//var_export ($SoapServer->delete(NULL, NULL, "urn://provider/collection/signature/media2/sequence1"));
var_export ($SoapServer->resolve(NULL, 'urn://provider/collection1/*/media1/*'));
//var_export ($SoapServer->update(NULL, NULL, 'urn://provider/collection/signature/media1/sequence1', 'path6'));
//var_export ($SoapServer->add('urn_master', NULL, 'collection1', 'signature1', 'media2', 'sequence1', 'path9', 'provider'));

?>
