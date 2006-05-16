<?php

include('./SoapTest.php');

class SoapAuthTest extends SoapTest {

    function setUp()
    {
        parent::setUp('Auth');
    }

    function testGetSession()
    {
        $this->assertTrue(true);
    }
    
}

$test = new SoapAuthTest();
$test->setUp();
var_export($test);

?>
