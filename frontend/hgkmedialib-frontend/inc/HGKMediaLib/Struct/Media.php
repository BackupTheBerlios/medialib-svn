<?php
class HGKMediaLib_Struct_Media {
    
    /**
     * The $data field contains an array of 
     * HGKMediaLib_Struct_Data objects.
     * 
     * @var HGKMediaLib_Struct_Data  
     * @access public
     */
    public $data;

    /*
     * Contains an array of HGKMediaLib_Struct_Files objects 
     * 
     * @var array of HGKMediaLib_Struct_Files objects
     * @access public
    public $files;
     */

    /**
     * The database id for a media
     * 
     * @var mixed
     * @access public
     */
    public $id;
    
    /**
     * Contains the name/description of a media 
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

    /**
     * Conatains the mime_type of the media
     * 
     * @var string
     * @access public
     */
    public $mime_type;

    public function __construct($data='',$id='',$name='',$urn='',$mime_type='')
    {
        $this->data = $data;
        $this->id = $id;
        $this->name = $name;
        $this->urn = $urn;
        $this->mime_type = $mime_type;
    }
}
?>
