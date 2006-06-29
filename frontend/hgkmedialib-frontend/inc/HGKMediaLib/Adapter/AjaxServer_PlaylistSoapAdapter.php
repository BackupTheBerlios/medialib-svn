<?php
if (session_id() == '') {
    session_start();        
}

class HGKMediaLib_AjaxServer_PlaylistSoapAdapter extends Adapter {

    public function __construct(){
        parent::__construct($this);
        $this->_cachePlaylists();
    }        

    public function addEntityToPlaylist($playlist, $entityID, $position = 0)
    {
        $result = $this->_soapClient->addEntityToPlaylist($this->_getSoapSession(), $playlist, $entityID, $position);
        if($result !== false){
            $uniquID = 'playlistitem_' . $entityID . '_' . mt_rand();
            $_SESSION['playlists'][$playlist] = $this->_addItemToArrayAtPosition($_SESSION['playlists'][$playlist], $position, $result, $uniquID); 
            return $uniquID;
        }
        return $result;
    }

    public function createPlaylist($playlistName)
    {
        $result = $this->_soapClient->createPlaylist($this->_getSoapSession(), $playlistName);
        if ($result !== false){
            $_SESSION['playlistNames'][$result] = $playlistName;
            $_SESSION['playlists'][$result] = array();
            
        }
        return $result;
    }

    public function getPlaylist($playlistID)
    {
        return $_SESSION['playlists'][$playlistID];
    }

    public function getPlaylists()
    {
        $result = array();
        foreach ($_SESSION['playlistNames'] as $key => $value) {
            $result[$key] = array('name' => $value, 'playlist' => $_SESSION['playlists'][$key]);
        }
        return $result;
    }

    public function removeEntityFromPlaylist($playlistID, $entityID)
    {
        $lalala = explode('_', $entityID) ;
        $result = $this->_soapClient->removeEntityFromPlaylist($this->_getSoapSession(), $playlistID, $lalala[1]);
        if (isset($_SESSION['playlists'][$playlistID][$entityID])) {
            unset($_SESSION['playlists'][$playlistID][$entityID]);   
            return $entityID;
        }
        return $result;
    }

    public function removePlaylist($playlistID)
    {
        $result = $this->_soapClient->removePlaylist($this->_getSoapSession(), $playlistID);

        if ($result) {
            if (isset($_SESSION['playlists'][$playlistID])) unset($_SESSION['playlists'][$playlistID]);
            if (isset($_SESSION['playlistNames'][$playlistID])) unset($_SESSION['playlistNames'][$playlistID]);
            return $playlistID;
        }

        return $result;
    }

    public function updatePlaylist($playlistID, $idArray, $nameArray)
    {
        $serverIDArray = array();
        foreach($idArray as $val) $serverIDArray[] = $val;
        $result = $this->_soapClient->updatePlaylist($playlistID, $serverIDArray);
        //file_put_contents('/srv/www/htdocs/hgkmedialib-frontend/inc/HGKMediaLib/Adapter/test', "lalal" . var_export($array));
        if($result){
            $array = array();
            for ($i=0; $i<count($idArray); $i++){
                $array[$idArray[$i]] = $nameArray[$i];
            }
            $_SESSION['playlists'][$playlistID] = $array;
        }
        return $result;
    }

    /**
     * check if the playlists are in the chache (i.e. in $_SESSION['playlistNames'] 
     * and $_SESSION['playlists']) and load them into it if not.
     * 
     * @access private
     * @return void
     */
    private function _cachePlaylists()
    {
        if (!isset($_SESSION['playlistNames']) || !isset($_SESSION['playlists'])) {
            $_SESSION['playlistNames'] = array();
            $_SESSION['playlists'] = array();
            
            $soapResult = $this->_soapClient->getPlaylists($this->_getSoapSession());
            if ($soapResult) {
                for ($i = 0; $i < count($soapResult); $i++) {
                    $_SESSION['playlistNames'][$soapResult[$i]->id] =  $soapResult[$i]->name;
                    foreach($soapResult[$i]->array as $key => $value){;
                        $_SESSION['playlists'][$soapResult[$i]->id]['playlistitem_' . $key . '_' . mt_rand()] = $value;
                    }
                }
            }
        }
    }

    private function _addItemToArrayAtPosition($array, $position, $newValue, $newKey) {
        $result = array();
        $i = 0;
        foreach ($array as $key => $value){
            if ($i++ == $position) $result[$newKey] = $newValue;
            $result[$key] = $value;
        }
        return $result;
    }

    /**
     * Swap 2 elements in array preserving keys and return 
     * array on success, false else.
     * 
     * @param array $array 
     * @param mixed $firstKey 
     * @param mixed $secondKey 
     * @access private
     * @return mixed
     */
    private function _swapPositionsInArray($array, $firstKey, $secondKey){
        if (array_key_exists($firstKey, $array) && array_key_exists($secondKey, $array)) {
            $firstValue = $array[$firstKey];
            $secondValue = $array[$secondKey];
            $result = array();
            foreach($array as $i => $v) {
                if ($i == $firstKey) {
                    $i = $secondKey;
                    $v = $secondValue;
                } elseif ($i == $secondKey) {
                    $i = $firstKey;
                    $v = $firstValue;
                }
                $result[$i] = $v;
            }
        } else {
            $result = false;
        }
        return $result;
    }

}
