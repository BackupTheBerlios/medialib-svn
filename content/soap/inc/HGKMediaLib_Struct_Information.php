<?php
class HGKMediaLib_Struct_Information{

    /**
     * Name of the collection/genre the entity belongs to 
     * 
     * @var string
     * @access public
     */
    public $collection;
    
    /**
     * id of the collection in the database 
     * 
     * @var mixed
     * @access public
     */
    public $collectionId;

    /**
     * The $date field contains the date of creation of an entity
     * given as follows:
     * 
     *     YYYY:MM:DD:hh:mm:ss
     * 
     * @var string
     * @access public
     */
    public $date;

    /**
     * The $description variable contains a short description of the entity 
     * 
     * @var string 
     * @access public
     */
    public $description;

    /**
     * The $id is an identifier of the database
     * for a Set, Instance or Work
     * 
     * @var mixed
     * @access public
     */
    public $id;
    
    /**
     * There is a HGKMediaLib_Struct_InformationBlock object for
     * every node/entity between the entity referenced by $id and the
     * the work it is part of. The array reflects the top-down
     * order of these nodes/entities, e.g.
     * 
     * $informationBlocks[0]->id is the id of the work
     * $informationBlocks[1]->id is the id of the instance 
     * $informationBlocks[2]->id is the id of the first set
     * ...
     *
     * Each HGKMediaLib_Struct_InformationBlock object contains
     * a template, text that goes with it, the files linked to
     * the node, the id and the title.
     * 
     * @var array of HGKMediaLib_Struct_InformationBlock objects
     * @access public
     */
    public $informationBlocks;

    /**
     * This field contains the $subtree of the entity, which consists of
     * HGKMediaLib_Struct_EntityNode nodes and
     * HGKMediaLib_Struct_Media leafs.
     * 
     * @var array of HGKMediaLib_Struct_EntityNode or HGKMediaLib_Struct_Media objects
     * @access public
     */
    public $subtree;

    /**
     * This is the $title of an entity 
     * 
     * @var string
     * @access public
     */
    public $title;

}

?>
