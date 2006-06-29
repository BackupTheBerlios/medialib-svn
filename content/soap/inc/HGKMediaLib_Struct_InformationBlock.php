<?php
class HGKMediaLib_Struct_InformationBlock{

/**
* The $data field contains an array of
* HGKMediaLib_Struct_Data objects.
*
* @var mixed
* @access public
*/
public $data;

/**
* The $files field contains an array of
* HGKMediaLib_Struct_Files objects
*
* @var array of HGKMediaLib_Struct_Files objects
* @access public
*/
public $files;

/**
* The $id is an identifier of the database
* for a Set, Instance or Work
*
* @var mixed
* @access public
*/
public $id;

/**
* The $title contains the title of a given
* Set, Instance or Work
*
* @var string
* @access public
*/
public $title;
}
?>