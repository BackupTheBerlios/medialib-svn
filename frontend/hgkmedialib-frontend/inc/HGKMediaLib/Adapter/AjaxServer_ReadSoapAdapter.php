<?php
if (session_id() == '') {
    session_start();        
}

//require_once('../../../conf/config.php');

/**
 * adapter between the AjaxServer and the Soap Class for reading
 * 
 * @uses Adapter
 * @package HGKMediaLib_Frontend
 * @version $id$
 * @copyright mediagonal a.g.
 * @author Pierre Spring <pierre.spring@mediagonal.ch> 
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 */
class HGKMediaLib_AjaxServer_ReadSoapAdapter extends Adapter {

    public function __construct(){
        parent::__construct($this);
    }        

    public function getByCollection($string)
    {
        $order = array(
            'Title' => 'asc'
        );
        if (is_null($string)){
            // clause searching for TODAY
            $today = array(
                array(
                    'connector' => '',
                    'subject' => 'Title',
                    'predicate' => '~',
                    'object' => 'Mord'
                )
            );
            $result = $this->_soapClient->find($this->_getSoapSession(), $today, $order, 20, 'de'); 
        } else {
            $clause = array(
                array(
                    'connector' => '',
                    'subject' => 'Title',
                    'predicate' => '~',
                    'object' => $string
                )
            );
            $result = $this->_soapClient->find($this->_getSoapSession(), $clause, $order, 20, 'de'); 
        }

        // if there is no result, return null
        if (!$result) return NULL;

        // remove non-relevant information and group results by collections
        for ($i = 0; $i < count($result); $i++){
            $groupedResult[$result[$i]->collection][] = 
                array(
                    "title" => $result[$i]->title,
                    "id" => $result[$i]->id
                );
        }

        // ... and sort results by collectons
        ksort($groupedResult);
        
        return $groupedResult;
        
    }

    public function getByDate($date)
    {
        $clause = array(
            array(
                'connector' => '',
                'subject' => 'Title',
                'predicate' => '~',
                'object' => 'Mord'
            )
        );
        $order = array(
            'Title' => 'asc'
        );
        $result = $this->_soapClient->find($this->_getSoapSession(), $clause, $order, 20, 'de'); 
        
        // remove non-relevant information
        for ($i = 0; $i < count($result); $i++){
            $result[$i] = array("date" => $result[$i]->date, "title" => $result[$i]->title, "id" => $result[$i]->id);
        }
        return $result;
    }

    public function getByTitle($string)
    {
        $order = array(
            'Title' => 'asc'
        );
        if (is_null($string)){
            // clause searching for TODAY
            $today = array(
                array(
                    'connector' => '',
                    'subject' => 'Title',
                    'predicate' => '~',
                    'object' => 'Mord'
                )
            );
            $result = $this->_soapClient->find($this->_getSoapSession(), $today, $order, 20, 'de');
        } else {
            $clause = array(
                array(
                    'connector' => '',
                    'subject' => 'Title',
                    'predicate' => '~',
                    'object' => $string 
                )
            );
            $result = $this->_soapClient->find($this->_getSoapSession(), $clause, $order, 20, 'de');
        }
        // remove non-relevant information
        for ($i = 0; $i < count($result); $i++){
            $result[$i] = array("title" => $result[$i]->title, "id" => $result[$i]->id);
        }
        return $result;
    }
    
    public function getFiles($entityID)
    {
        $this->_cacheInformation($entityID);
        return $_SESSION[$entityID]['files'];
    }

    public function getInformation($entityID)
    {
        $this->_cacheInformation($entityID);
        return array('id' => $entityID, 'title' => $_SESSION[$entityID]['title'], 'path' => $_SESSION[$entityID]['path'], 'data' => $_SESSION[$entityID]['data'], 'VBM' => $_SESSION[$entityID]['VBM'], 'COV' => $_SESSION[$entityID]['COV']);
    }
    
    public function getSubTree($entityID)
    {
        $this->_cacheInformation($entityID);
        return $_SESSION[$entityID]['SubTree'];
    }

    public function getSuggestions($mode, $string){
        $this->_cacheSuggestions($mode);

        $array          = $_SESSION['suggestions'][$mode];
        $entry          = $string;
        $fullSerch      = false;
        $ignoreCase     = true;
        $maxResults     = 25;
        $partial        = array();
        $partialSearch  = true;
        $partialChars   = 2;
        $ret            = array();
        $count = 0;
        for ($i = 0; $i < count($array); $i++){
            if(count($ret) > $maxResults)break;
            $element = $array[$i];

            $foundPosition = $ignoreCase ? strpos(strtolower($element), strtolower($entry)) : strpos($element, $entry);       
            if ($foundPosition === false) $foundPosition = -1;
$j = 0;            
            while ($foundPosition != -1){
                if ($foundPosition == 0 && strlen($entry) != strlen($element)){
                    array_push($ret, "<li><strong>" . substr($element, 0, strlen($entry)) . "</strong>" . substr($element, strlen($entry)));
                    break;
                } elseif (strlen($entry) >= $partialChars && $partialSearch && $foundPosition != -1){
                    if ($fullSerch || preg_match('/\s/', substr($element, $foundPosition - 1, 1))){
                        array_push($partial, "<li>" . substr($element, 0, $foundPosition) . "<strong>" .
                            substr($element, $foundPosition, strlen($entry)) . "</strong>" . 
                            substr($element, $foundPosition + strlen($entry)) . "</li>");
                        break;
                    }
                }
                $newPosition = $ignoreCase ? strpos(strtolower(substr($element, $foundPosition + 1)), strtolower($entry)) : strpos(substr($element, $foundPosition + 1), $entry);       
                $foundPosition = ($newPosition === false) ? -1 : $newPosition + $foundPosition + 1;
            }
        }

        if (count($partial) && ($maxResults - count($ret)) > 0){
           for ($i = 0; $i < count($partial) && ($maxResults - count($ret)) > 0; $i++)
               array_push($ret, $partial[$i]);
        }
        return "<ul>" . implode("", $ret) . '</ul>';
        return "<ul><li>adapter</li></ul>";
    }
    
    public function getThumbs()
    {
        $today = array(
            array(
                'connector' => '',
                'subject' => 'Title',
                'predicate' => '~',
                'object' => 'Mord'
            )
        );
        $order = array(
            'Title' => 'asc'
        );
        $result = $this->_soapClient->find($this->_getSoapSession(), $today, $order, 6, 'de');
        for ($i = 0; $i < count($result); $i++){
            if (is_null($result[$i]->coverMedia)) $result[$i]->coverMedia = "http://media1.hgkz.ch/tmp/pictures/1.jpg";
            $result[$i] = array("coverMedia" => $result[$i]->coverMedia, "id" => $result[$i]->id);
        }
        return $result;
    }

    public function search($search, $page = 1)
    {
        $clause = array();
        foreach($search as $value){
            if($value[0] == 'keywords'){
                array_push($clause, array(
                        'connector' => 'AND',
                        'subject' => $value[0],
                        'predicate' => '=',
                        'object' => $value[1]
                    ));
            }else{
                array_push($clause, array(
                        'connector' => 'AND',
                        'subject' => $value[0],
                        'predicate' => '~',
                        'object' => $value[1]
                    ));
            }
        }
        $order = array(
            'title' => 'asc'
        );
        try {
            $result = $this->_soapClient->find($this->_getSoapSession(), $clause, $order, 1000, 'de');
        } catch (Exception $e) {
            return $e;
            $result = array();
        }


        $array = array();
        $array['result'] = array();
        $array['paging'] = array();

        $array['paging']['pages']  = ceil(count($result) / 10);
        $array['paging']['page']   = ($page > $array['paging']['pages']) ? $array['paging']['pages'] : $page;
        $array['paging']['search'] = $search;

        if (count($result) != 0){

            $lowerbound = ($array['paging']['page'] - 1) * 10;
            $upperbound = ($array['paging']['pages'] == $array['paging']['page']) ? count($result) : $array['paging']['page'] * 10;

            for ($i = $lowerbound; $i < $upperbound; $i++){
                if (is_null($result[$i]->coverMedia)) $result[$i]->coverMedia = "http://media1.hgkz.ch/tmp/pictures/1.jpg";
                $array['result'][] = array("coverMedia" => $result[$i]->coverMedia, "description" => $result[$i]->description, "title" => $result[$i]->title, "id" => $result[$i]->id);
            }
        }
        return $array;
    }
    
    private function _cacheInformation($entityID)
    {
        if (!isset($_SESSION[$entityID])) {
            $soapResult = $this->_soapClient->getInformation($this->_getSoapSession(), $entityID);
            if ($soapResult) {
                $_SESSION[$entityID] = array();
                $_SESSION[$entityID]['title'] = $soapResult->title;
                $_SESSION[$entityID]['VBM']   = array();
                $_SESSION[$entityID]['data']  = array();
                $_SESSION[$entityID]['COV']   = array();
                $_SESSION[$entityID]['files'] = array();
                // build subtree and get VBM and COV information
                $_SESSION[$entityID]['SubTree'] = $this->_subTree($soapResult, $entityID);
                if (count($_SESSION[$entityID]['VBM']) > 1 && count($_SESSION[$entityID]['COV']) > 0) {
                    $_SESSION[$entityID]['VBM'] = $_SESSION[$entityID]['COV']; 
                }
                if (count($_SESSION[$entityID]['COV']) > 1) $_SESSION[$entityID]['COV'] = $_SESSION[$entityID]['COV'][0]; 
                // gather files, path and data
                foreach ($soapResult->informationBlocks as $infoBlock) {
                    $_SESSION[$entityID]['data'] [$infoBlock->id] = array();
                    $_SESSION[$entityID]['path'] [$infoBlock->id] = $infoBlock->title;
                    foreach ($infoBlock->files as $file) {
                        $_SESSION[$entityID]['files'][] = array('name' => $file->name, 'urn' => $file->urn);
                    }
                    foreach ($infoBlock->data as $data) {
                        $_SESSION[$entityID]['data'][$infoBlock->id][] = array("label" => $data->label, "name" => $data->name, "value" => $data->value);
                    } 
                }
                return true;
            } else {
                return false;
            }
        }
    }

    private function _cacheSuggestions($mode)
    {
        if (!isset($_SESSION['suggestions'][$mode])){
            $_SESSION['suggestions'][$mode] = $this->_soapClient->getSuggestions($this->_getSoapSession(), $mode);
        }
    }

    private function _subTree($object, $entityID)
    {
        $result = array();
        //foreach ($object->subtree as $entity)  $result[] = $this->_subTree($entity);
        if (isset($object->subtree[0]->title)) {
            foreach ($object->subtree as $entity)  $result[$entity->title] = $this->_subTree($entity, $entityID);
        }
        if (isset($object->subtree[0]->name)) {
            foreach ($object->subtree as $entity) {
                $result[$entity->name] = NULL;
                if (strstr($entity->name, 'VBM') !== false) $_SESSION[$entityID]['VBM'][] = $entity->urn;
                if (strstr($entity->name, 'COV') !== false) $_SESSION[$entityID]['COV'][] = $entity->urn;
            }
        }
        return $result;
    }
}

//$ad = new HGKMediaLib_AjaxServer_ReadSoapAdapter();
//var_export($ad->getSuggestions('sfd', 't'));
?>
