<?php
class HGKMediaLib_Struct_Attribute
{
   /**
     * $name containes the name of the data field
     * 
     * @var string 
     * @access public
     */
    public $name;
    
   /**
     * $value containes the content a data field
     * 
     * @var mixed
     * @access public
     */
    public $value;
   
    /**
     * $lang containes the language of the value of the data field
     * 
     * @var string 
     * @access public
     */
    public $lang
    
    /**
     * $data_type containes the data type of the value of the data field
     * 
     * @var string
     * @access public
     */
    public $data_type;
   
    public function __construct($name='',$value='',$lang='',$data_type='')
    {
        $this->name = $name;
        $this->value = $value;
        $this->lang = $lang;
        $this->data_type = $data_type;
    }

    private function setValue($value)
    {
        $this->value = $value;
    }
}
?
