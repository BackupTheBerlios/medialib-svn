<?php
class HGKMediaLib_Struct_Entity {

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
     * The $coverMedia contains the urn to the cover media 
     * (e.g. cover of a DVD, flyer of a concert, etc...)
     * 
     * @var string
     * @access public
     */
    public $coverMedia;

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
     * The $id identifies the entity in the backend database,
     * i.e. a work, instance or set
     * 
     * @var int
     * @access public
     */
    public $id;
    
    /**
     * This is the $title of an entity 
     * 
     * @var string
     * @access public
     */
    public $title;

    public function __construct($collection='',$collection_id='',$cover_media='',$date='',$description='',$id='',$title='')
    {
        $this->collection = $collection;
        $this->collectionId = $collection_id;
        $this->coverMedia = $cover_media;
        $this->date = $date;
        $this->description = $description;
        $this->id = $id;
        $this->title = $title;
    }
}
?>
