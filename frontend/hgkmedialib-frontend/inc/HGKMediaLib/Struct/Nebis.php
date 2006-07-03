<?php
class HGKMediaLib_Struct_Nebis{

    public function __construct( 
        $channel = '',
        $collection = '', 
        $color = '', 
        $date = '', 
        $data = array(), 
        $decade = '', 
        $description = '', 
        $duration = '', 
        $id = '', 
        $langOne = '', 
        $langTwo = '', 
        $nebisID = '', 
        $originalTitle = '', 
        $productionCountry = '', 
        $productionCountryLong = '', 
        $productionYear = '', 
        $title = '', 
        $transmissionDate = ''
        )
        {
        $this->channel = $channel;
        $this->collection = $collection; 
        $this->color = $color ; 
        $this->date = $date ; 
        $this->data = $data ; 
        $this->decade = $decade ; 
        $this->description = $description ; 
        $this->duration = $duration ; 
        $this->id = $id ; 
        $this->langOne = $langOne ; 
        $this->langTwo = $langTwo ; 
        $this->nebisID = $nebisID ; 
        $this->originalTitle = $originalTitle ; 
        $this->productionCountry = $productionCountry ; 
        $this->productionCountryLong = $productionCountryLong ; 
        $this->productionYear = $productionYear ; 
        $this->title = $title;
        $this->transmissionDate = $transmissionDate;
    }
    
    /**
     * tv-channel 
     * short (nebis/marc standard)
     * 
     * @var string
     * @access public
     */
    public $channel;
    
    /**
     * Name of the collection/genre the entity belongs to 
     * 
     * @var string
     * @access public
     */
    public $collection;
    
    /**
     * color 
     * 
     * @var string
     * @access public
     */
    public $color;

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
     * contains all people related to an instance
     * (including the once of the work, set, sub-set...)
     * 
     * @var array of HGKMediaLib_Struct_Data
     * @access public
     */
    public $data;

    /**
     * decade 
     * 
     * @var string
     * @access public
     */
    public $decade;

    /**
     * The $description variable contains a short description of the entity 
     * 
     * @var string 
     * @access public
     */
    public $description;

    /**
     * duration 
     * 
     * @var string
     * @access public
     */
    public $duration;

    /**
     * The $id is an identifier of the database
     * for a Set, Instance or Work
     * 
     * @var mixed
     * @access public
     */
    public $id;

    /**
     * langOne 
     * short (nebis/marc standard)
     * 
     * @var string
     * @access public
     */
    public $langOne;

    /**
     * langTwo 
     * short (nebis/marc standard)
     * 
     * @var string
     * @access public
     */
    public $langTwo;

    /**
     * nebisID 
     * 
     * @var mixed
     * @access public
     */
    public $nebisID;

    /**
     * originalTitle 
     * 
     * @var string
     * @access public
     */
    public $originalTitle;
    
    /**
     * productionCountry 
     * short (nebis/marc standard) e.g. "ger"
     * 
     * @var string
     * @access public
     */
    public $productionCountry;
    
    /**
     * productionCountryLong, e.g. "Deutschland"
     * 
     * @var string
     * @access public
     */
    public $productionCountryLong;
    
    /**
     * productionYear 
     * 
     * @var string
     * @access public
     */
    public $productionYear;

    /**
     * This is the $title of an entity 
     * 
     * @var string
     * @access public
     */
    public $title;
    
    
    /**
     * transmission date yyyy-mm-dd 
     * 
     * @var string
     * @access public
     */
    public $transmissionDate;

}
