<?php
class HGKMediaLib_Struct_EntityData {

    /**
     * The $id identifies the entity in the backend database,
     * i.e. a work, instance or set
     * 
     * @var int $id
     * @access public
     */
    public $id;
    
    /**
     * $attributes contains a array of HGKMediaLib_Struct_Attribute 
     * 
     * @var array
     * @access public
     */
    public $attributes;

    public function __construct($id,$attributes)
    {
        $this->id = $id;
        $this->attributes = $attributes;
    }
}
?>
