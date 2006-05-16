<?php
class HGKMediaLib_Struct_Media {
    
    /**
     * The $data field contains an array of 
     * HGKMediaLib_Struct_Data objects.
     * 
     * @var mixed
     * @access public
     */
    public $data;

    /*
     * Contains an array of HGKMediaLib_Struct_Files objects 
     * 
     * @var array of HGKMediaLib_Struct_Files objects
     * @access public
     */
    // public $files;

    /**
     * The database id for a media
     * 
     * @var mixed
     * @access public
     */
    public $id;
    
    /**
     * Contains the name of a media 
     * 
     * @var string
     * @access public
     */
    public $name;
    
    /**
     * Conatains the urn of a media
     * 
     * @var string
     * @access public
     */
    public $urn;

}
?>
