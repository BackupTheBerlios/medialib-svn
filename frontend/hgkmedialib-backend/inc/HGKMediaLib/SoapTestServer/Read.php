<?php
/**
 * DUMMY SOAP server for reading access to the backend
 *  
 * This is just a dummy, not the real thing. For documentation please consult
 * the interface definition.
 * 
 * Copyright 2005-2006 Pierre Spring, mediagonal Ag <pierre.spring@mediagonal.ch>
 *
 * See the enclosed file COPYING for license information (GPL). If you
 * did not receive this file, see http://www.fsf.org/copyleft/gpl.html.
 *
 * @author Pierre Spring <pierre.spring@mediagonal.ch>
 * @subpackage HGKMediaLib_Backend_Dummy
 * @package HGKMediaLib
 * @version $Id$
 * @see HGKMediaLib_ReadingInterface
 */
require_once('../../../conf/config.php');
class HGKMediaLib_SoapServer_Read implements HGKMediaLib_ReadingInterface{
    
    /**
     * Find using an extended query.
     *
     * One clause looks like this:
     * 
     * array(
     *  'connector' => {AND, OR}
     *  'subject'   => '..' // name of property to be matched against, set is to be defined
     *  'predicate' => {'=', '~', '!=', '!~'} // equal, like, not equal, not like
     *  'object'    => '..' // property value to match
     *  )
     * 
     * $clauses can contain any number of these.
     * 
     * The $sortOrder is an associative array, with the lable as key, and the order as value, e.g.
     * array(
     *   'insertionDate' => 'descending'
     *   'workTitle'     => 'ascending'
     *   )
     *   
     * 
     * @param int $sessionId
     * @param array $clauses 
     * @param array $sortOrder
     * @param int $limit
     * @param string $lang 
     * @access public
     * @return array of HGKMediaLib_Struct_Entity objects
     */    
    public function find($sessionId, $clauses, $sort, $limit = 40, $lang = 'de'){
        
        // somehow, the default values are not read when
        // called from Soap.
        if ($limit === NULL) $limit = 40;
        if ($lang === NULL) $lang = 'de';

        if ($limit > 100) $limit = rand(100, 200);
        
        $result = array();
        if ($limit > 35){
            $count = rand(35, intval($limit));
        }else{
            $count = $limit;
        }

        $collectionArray = array("comedy", "drama", "box movie");
        
        for($i=0; $i < $count; $i++){
            $entity = new HGKMediaLib_Struct_Entity();
            $entity->collection     = $collectionArray[rand(0,2)];
            $entity->collectionId   = 666;
            $entity->coverMedia     = "http://media1.hgkz.ch/tmp/pictures/1.jpg";
            $entity->date           = ((($i % 30) + 1) < 10) ? "0" . (($i % 30) + 1) . "/" : ($i % 30) + 1 . "/";
            $entity->date           = ((floor($i/30) + 1) < 10) ? $entity->date . "0" . (floor($i/30) + 1) . "/" : $entity->date . (floor($i/30) + 1) . "/";
            $entity->date           = $entity->date . "2006";
//            $entity->date           = $entity->date . (floor($i/30)+1) % 12 . "/" . 2006;
            $entity->description    = "For the ${i}th time, Rocky is back... Yeah!";
            $entity->id             = '4ks3503k2' . $i;
            $entity->title          = "Rocky $i Rocky $i Rocky $i Rocky $i Rocky $i Rocky $i";
            $result[] = $entity;
        }

        return $result;
    }

    public function getSuggestions($sessionId, $mode){
        return array('ABBA', 'AC/DC', 'Cinderella', 'Aerosmith', 'America', 'Bay City Rollers', 'Black Sabbath', 'Boston', 'David Bowie', 'Can', 'The Carpenters', 'Chicago', 'The Commodores', 'Crass', 'Deep Purple', 'The Doobie Brothers', 'Eagles', 'Fleetwood Mac', 'Haciendo Punto en Otro Son', 'Heart', 'Iggy Pop and the Stooges', 'Journey', 'Judas Priest', 'KC and the Sunshine Band', 'Kiss', 'Kraftwerk', 'Led Zeppelin', 'Lindisfarne (band)', 'Lipps, Inc', 'Lynyrd Skynyrd', 'Pink Floyd', 'Queen', 'Ramones', 'REO Speedwagon', 'Rhythm Heritage', 'Rush', 'Sex Pistols', 'Slade', 'Steely Dan', 'Stillwater', 'Styx', 'Supertramp', 'Sweet', 'Three Dog Night', 'The Village People', 'Wings (fronted by former Beatle Paul McCartney)');
    }

    /**
     * The getInformation() method returns a HGKMediaLib_Struct_Information object
     * 
     * @param int $sessionId 
     * @param mixed $id database entity id of a Set, Instance or Work
     * @param string $lang 
     * @access public
     * @return object HGKMediaLib_Struct_Information
     */
    public function getInformation($sessionId, $id, $lang = 'de'){

        $count = rand(3,5);
        
        $info = new HGKMediaLib_Struct_Information();
        $info->collection = 'Drama';
        $info->collectionId = 'cIDooo456';
        $info->date = '2006:04:19:23:23';
        $info->description = 'this movie is goooooooood';
        $info->id = 'entityXooo8738742949459282';
        $info->informationBlocks = array();
        $info->title = 'Set'; 

        for ($i = 3; $i < $count; $i++){
            $info->title = 'Sub' . $info->title;
        }
        
        $info->subtree = $this->_generateSubTree(7 - $count, $info->title);

        for ($i = 0; $i < $count; $i++){
            $info->informationBlocks[] = new HGKMediaLib_Struct_InformationBlock();
            $info->informationBlocks[$i]->files = array();
            for ($j = 0; $j < 7; $j++){
                $info->informationBlocks[$i]->files[] = new HGKMediaLib_Struct_Files();
                $info->informationBlocks[$i]->files[$j]->name = 'file 00' . $i . "-" . $j;
                $info->informationBlocks[$i]->files[$j]->urn = 'urn  00' . $i . "-" . $j;
            }

            // generate dummy names
            $info->informationBlocks[$i]->title = ($i == 0) ? 'Werk' : (($i == 1) ? 'Instanz' : 'Set');
            if ($i > 2) {
                for ($j = 2; $j < $i; $j++){
                    $info->informationBlocks[$i]->title = 'Sub' . $info->informationBlocks[$i]->title;
                }
            }
            
            // generate some dummy data
            $info->informationBlocks[$i]->data = array();
            for ($j = 0; $j < 6; $j++){
                $info->informationBlocks[$i]->data[] = new HGKMediaLib_Struct_Data;
                $info->informationBlocks[$i]->data[$j]->label = 'label' . $i . "-" . $j;
                $info->informationBlocks[$i]->data[$j]->name = 'name' . $i . "-" . $j;
                $info->informationBlocks[$i]->data[$j]->value = 'value' . $i . "-" . $j;
            }

            // generate dummy id
            $info->informationBlocks[$i]->id = 'idOf' . $info->informationBlocks[$i]->title; 

        }

        for ($i = $count; $i < 7; $i++) {

        }

        return $info;
    }

    private function _generateSubTree($level, $title){
        if ($level <= 0) {
            $result = array();
            $count = rand(2,3);
            for ($i = 0; $i < $count; $i++) {
                $result[] = new HGKMediaLib_Struct_Media();
                $result[$i]->data = array();
                for ($j = 0; $j < 2; $j++){
                    $result[$i]->data[] = new HGKMediaLib_Struct_Data;
                    $result[$i]->data[$j]->label = 'Media label' . $j;
                    $result[$i]->data[$j]->name = 'Media name' . $j;
                    $result[$i]->data[$j]->value = 'Media value' . $j;
                }
                $result[$i]->id     = 'mediaId' . $i;
                $result[$i]->name   = 'MPEG' . $i;
                $result[$i]->urn    = 'urn://blah';
            }
            $result[] = new HGKMediaLib_Struct_Media();
            end($result)->data = array();
            end($result)->id = 'vbmID';
            end($result)->name = 'VBM-HGKZ-' . $i;
            end($result)->urn = 'media1.hgkz.ch/tmp/pictures';
            
            $result[] = new HGKMediaLib_Struct_Media();
            end($result)->data = array();
            end($result)->id = 'covID';
            end($result)->name = 'COV-HGKZ-' . $i;
            end($result)->urn = 'media1.hgkz.ch/tmp/pictures/1.jpg';
            
            return $result;
        }
        $result = array();
        $count = rand(2,3);
        for ($i = 0; $i < $count; $i++) {
            $result[] = new HGKMediaLib_Struct_EntityNode();
            $result[$i]->description = "This is the description of Sub$title no: $i";
            $result[$i]->id = "SomeSetID $level : $i";
            $result[$i]->title = "Sub$title no: $i";
            $result[$i]->subtree = $this->_generateSubTree($level - 1, "Sub" . $title);
        }
        return $result;
    }

}

if (strtoupper($_SERVER['REQUEST_METHOD']) == 'GET' && isset($_GET['wsdl'])) {
	header("content-type: text/xml");
	readfile(HKGMEDIALIB_WSDL_BASEDIR . '/Read.wsdl');
	exit;
}

$soapServerReadInstance = new SoapServer(HKGMEDIALIB_WSDL_BASEDIR . 'Read.wsdl');
$soapServerReadInstance->setClass("HGKMediaLib_SoapServer_Read");
$soapServerReadInstance->handle();
?>
