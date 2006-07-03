<?php
class HGKMediaLib_Struct_Files{
    /**
     * The $name describes a file 
     * 
     * @var string
     * @access public
     */
    public $name;
    
    /**
     * The $urn links to the given file
     * 
     * @var string
     * @access public
     */
    public $urn;

    public function __construct($name='',$urn='')
    {
        $this->name = $name;
        $this->urn = $urn;
    }
}

?>
