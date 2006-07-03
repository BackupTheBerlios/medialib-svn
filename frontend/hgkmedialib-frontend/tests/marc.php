<?php
header( "Content-type: text/plain\n\n" );
require_once('../conf/config.php');

$color = 's/w';
$data = array(
            new HGKMediaLib_Struct_Data('actor', 'Schauspieler', 'Spring, Pierre'),
            new HGKMediaLib_Struct_Data('actor', 'Schauspieler', 'Perroud, Rachel'),
            new HGKMediaLib_Struct_Data('director', 'Regie', 'Spring, Ronald'),
        );


$nebis = new HGKMediaLib_Struct_Nebis('SF 1', 'Dokumentarfilm', $color, '2006-04-19', $data, '1970', 'dieser film ist sehr langweilig. ja.', '184', 'winetID', 'ger', 'jpn', 'nebisID', 'Tschi Yu Xong', 'jp', 'Japan', '1978', '<<Der>> Weisse Riese', '1985-05-29');

$string = '';

$string = sprintf( "%s L %s\n", 'FMT  ', 'VM' );
$string .= sprintf( "%s L %s\n", 'LDR  ',  '-----ngm--22-----uu-4500');
/*******
 * 008 *
 *******/
$data = "-----s";
$data .= $nebis->productionYear;
$data .= "----";
$data .= $nebis->productionCountry;
for( $i = strlen( $nebis->productionCountry); $i < 3; $i++ ) $data .='-';
$data .= '---------------v-';
$data .= $nebis->langOne;
for( $i = strlen($nebis->langOne); $i < 3; $i++ ) $data .='-';
$data .= '-';
$string .= sprintf( "%s L %s\n", '008  ', $data );
/*******
 * 019 *
 *******/
$data = '$$aNicht visioniert';
$data .= '$$525.05.2006/E65';
$string .= sprintf( "%s L %s\n", '019  ', $data );
/*******
 * 040 *
 *******/
$data = '$$aSzZuIDS NEBIS HGKZ-VHS';
$string .= sprintf( "%s L %s\n", '040  ', $data );
/*******
 * 245 *
 *******/
$data = '$$a';
$data .= $nebis->originalTitle;
$data .= '$$hFilmmaterial';
$data .= '$$c';
$data .= regie($nebis);
$data .= '$$d';
$data .= $nebis->title;
$string .= sprintf( "%s L %s\n", '245  ', $data );
/*******
 * 260 *
 *******/
$data = '$$c' . $nebis->decade;
$string .= sprintf( "%s L %s\n", '260  ', $data );
/*******
 * 300 *
 *******/
$data = '$$a1 DVD-Video (' . $nebis->duration . ' Min.)';
$string .= sprintf( "%s L %s\n", '300  ', $data );
/*******
 * 500 *
 *******/
$data = '$$aAufzeichnung der Ausstrahlung vom Fernsehsender ' . $nebis->channel . ' in ' . $nebis->langOne ;
if ($nebis->langOne != $nebis->langTwo && $nebis->langTwo != '')
    $data .= '. und ' . $nebis->langTwo;
$data .= '. Sprache am ' . switchDate($nebis->transmissionDate) . '. Der Film wurde ' . $nebis->decade . ' in ';
if ($nebis->color != '') $data .= $nebis->color . ' in ';
$data .= $nebis->productionCountryLong . ' produziert.';
$string .= sprintf( "%s L %s\n", '500  ', $data );
/*******
 * 511 *
 *******/
$data = '$$a' . actors($nebis);
$string .= sprintf( "%s L %s\n", '511  ', $data );
/*******;
 * 690 * E4
 *******/
$data = '';
$string .= sprintf( "%s L %s\n", '690E4', $data );
/*******;
 * 690 * E5
 *******/
$data = '$$a' . regieWithComma($nebis);
$string .= sprintf( "%s L %s\n", '690E5', $data );
/*******
 * 906 *   
 *******/
$data = '$$hMP DVD-Video';
$string .= sprintf( "%s L %s\n", '906  ', $data );
/*******;
 * 909 * ET
 *******/
$data = '';
$string .= sprintf( "%s L %s\n", '909ET', $data );
/*******
 * 920 * 1 
 *******/
foreach ($nebis->data as $person){
    $data = $person->value;
    $string .= sprintf( "%s L %s\n", '9201 ', $data );
}
/*******;
 * 940 *   
 *******/
$data = '';
$string .= sprintf( "%s L %s\n", '940  ', $data );
/*******;
 * 856 *   
 *******/
$data = '$$uhttp://library.hgkz.ch/media.php?sig='.urlencode( 'xxx' ).'$$zFH-HGK - Videoarchiv - Zugriff über: http://library.hgkz.ch/media.php?sig='.urlencode( 'xxxx' );
$string .= sprintf( "%s L %s\n", '856  ', $data );
/*******;
 * 856 *   
 *******/
$data = '$$uhttp://library.hgkz.ch/dvdbenutzung.php'.'$$zFH-HGK - Zugriffsberechtigung Videoarchiv: http://library.hgkz.ch/dvdbenutzung.php';
$string .= sprintf( "%s L %s\n", '856  ', $data );



 

echo $string;

function regie($nebis) 
{
    $result = "";
    foreach ($nebis->data as $data){
        if ($data->label == 'director') {
            $result .=  switchOnComma($data->value) . "; ";
        }
    }
    return trim($result, "; ");
}

function regieWithComma($nebis) 
{
    $result = "";
    foreach ($nebis->data as $data){
        if ($data->label == 'director') {
            $result .=  $data->value . "; ";
        }
    }
    return trim($result, "; ");
}

function actors($nebis)
{
    $result = "";
    foreach ($nebis->data as $data){
        if($data->label == 'actor') {
            $result .=  switchOnComma($data->value) . ", ";
        }
    }
    return trim($result, " ,");
}

function switchOnComma($string){
    $result = explode(',', $string);
    return trim($result[1]) . ' ' .  trim($result[0]);
}

function switchDate($string){
    $result = explode('-', $string);
    return $result[2] . '.' . $result[1] . '.' . $result[0];
}

?>
