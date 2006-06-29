<?php
class HGKMediaLib_Struct_EntityNode {

/**
* The $description variable contains a short description of the entity
*
* @var string
* @access public
*/
public $description;

/**
* The $id identifies the entity in the backend database,
* i.e. a work, instance or set
*
* @var int
* @access public
*/
public $id;

/**
* This is the $title of a entity
*
* @var string
* @access public
*/
public $title;

/**
* this field contains the $subtree of the entity, which consists of
* HGKMediaLib_Struct_EntityNode nodes and
* HGKMediaLib_Struct_Media leafs.
*
* @var array of HGKMediaLib_Struct_EntityNode or HGKMediaLib_Struct_Media objects
* @access public
*/
public $subtree;   
}
?>