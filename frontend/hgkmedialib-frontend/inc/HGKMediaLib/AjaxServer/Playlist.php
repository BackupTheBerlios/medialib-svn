<?php
class HGKMediaLib_AjaxServer_Playlist extends HGKMediaLib_AjaxExportable{

    function __construct()
    {
        parent::__construct($this);               
    }

    function addEntityToPlaylist($playlistID, $entityID, $position)
    {
        return $this->_adapter->addEntityToPlaylist($playlistID, $entityID, $position);
    }
    
    function createPlaylist($playlistName)
    {
        return $this->_adapter->createPlaylist($playlistName);
    }
    
	function getPlaylist($playlistID)
    {
        return $this->_adapter->getPlaylist($playlistID);
	}
    
	function getPlaylists()
    {
        return $this->_adapter->getPlaylists();
	}

    function removeEntityFromPlaylist($playlistID, $entityID)
    {
        return $this->_adapter->removeEntityFromPlaylist($playlistID, $entityID);
    }

    function removePlaylist($playlistID)
    {
        return $this->_adapter->removePlaylist($playlistID);
    }

    function updatePlaylist($playlistID, $idArray, $nameArray)
    {
        return $this->_adapter->updatePlaylist($playlistID, $idArray, $nameArray);
    }
	
    function swapPositions($playlistID, $entityID01, $entityID02)
    {
        return $this->_adapter->swapPositions($playlistID, $entityID01, $entityID02);
    }
	
}

?>
