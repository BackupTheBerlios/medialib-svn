<?php
class HGKMediaLib_Struct_Data{

    /**
     * This is the label of the data
     * 
     * @var string
     * @access public
     */
    public $label;
    
    /**
     * The $name specifies the language specific
     * representation of the label 
     * 
     * @var string
     * @access public
     */
    public $name;

    /**
     * The $value of the data 
     * 
     * @var string
     * @access public
     */
    public $value;

    /**
     * The id of the entity (person or group) 
     * 
     * @var integer
     * @access public
     */
    public $id;


    public function __construct($label='',$name='',$value='',$id='')
    {
        $this->label = $label;
        $this->name = $name;
        $this->value = $value;
        $this->id = $id;
    }
}

?>
