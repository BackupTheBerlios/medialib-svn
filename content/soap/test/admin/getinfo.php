<?

include ("../../inc/HGKMediaLib_Struct_Data.php");
include ("../../inc/HGKMediaLib_Struct_Entity.php");
include ("../../inc/HGKMediaLib_Struct_EntityNode.php");
include ("../../inc/HGKMediaLib_Struct_Files.php");
include ("../../inc/HGKMediaLib_Struct_Information.php");
include ("../../inc/HGKMediaLib_Struct_InformationBlock.php");
include ("../../inc/HGKMediaLib_Struct_Media.php");

	function getInformation($sessionId, $id, $lang = 'de')
    {
        $count = rand(3,5);

        
        $info = new HGKMediaLib_Struct_Information();
        $info->collection = 'Drama';
        $info->collectionId = 'cIDooo456';
        $info->date = '2006:04:19:23:23';
        $info->description = 'this movie is goooooooood';
        $info->id = 'entityXooo8738742949459282';
        $info->informationBlocks = array();
        $info->title = 'Set'; 

        for ($i = 3; $i < $count; $i++){
            $info->title = 'Sub' . $info->title;
        }
        
        $info->subtree = generateSubTree(7 - $count, $info->title);

        for ($i = 0; $i < $count; $i++){
            $info->informationBlocks[] = new HGKMediaLib_Struct_InformationBlock();
            $info->informationBlocks[$i]->files = array();
            for ($j = 0; $j < 7; $j++){
                $info->informationBlocks[$i]->files[] = new HGKMediaLib_Struct_Files();
                $info->informationBlocks[$i]->files[$j]->name = 'file 00' . $i . "-" . $j;
                $info->informationBlocks[$i]->files[$j]->urn = 'urn  00' . $i . "-" . $j;
            }

            // generate dummy names
            $info->informationBlocks[$i]->title = ($i == 0) ? 'Werk' : (($i == 1) ? 'Instanz' : 'Set');
            if ($i > 2) {
                for ($j = 2; $j < $i; $j++){
                    $info->informationBlocks[$i]->title = 'Sub' . $info->informationBlocks[$i]->title;
                }
            }
            
            // generate some dummy data
            $info->informationBlocks[$i]->data = array();
            for ($j = 0; $j < 6; $j++){
                $info->informationBlocks[$i]->data[] = new HGKMediaLib_Struct_Data;
                $info->informationBlocks[$i]->data[$j]->label = 'label' . $i . "-" . $j;
                $info->informationBlocks[$i]->data[$j]->name = 'name' . $i . "-" . $j;
                $info->informationBlocks[$i]->data[$j]->value = 'value' . $i . "-" . $j;
            }

            // generate dummy id
            $info->informationBlocks[$i]->id = 'idOf' . $info->informationBlocks[$i]->title; 

        }

        for ($i = $count; $i < 7; $i++) {

        }
        return $info;
    }

    function generateSubTree($level, $title){
        if ($level <= 0) {
            $result = array();
            $count = rand(2,3);
            for ($i = 0; $i < $count; $i++) {
                $result[] = new HGKMediaLib_Struct_Media();
                $result[$i]->data = array();
                for ($j = 0; $j < 2; $j++){
                    $result[$i]->data[] = new HGKMediaLib_Struct_Data;
                    $result[$i]->data[$j]->label = 'Media label' . $j;
                    $result[$i]->data[$j]->name = 'Media name' . $j;
                    $result[$i]->data[$j]->value = 'Media value' . $j;
                }
                $result[$i]->id     = 'mediaId' . $i;
                $result[$i]->name   = 'MPEG' . $i;
                $result[$i]->urn    = 'urn://blah';
            }
            $result[] = new HGKMediaLib_Struct_Media();
            end($result)->data = array();
            end($result)->id = 'vbmID';
            end($result)->name = 'VBM-HGKZ-' . $i;
            end($result)->urn = 'media1.hgkz.ch/tmp/pictures';
            
            $result[] = new HGKMediaLib_Struct_Media();
            end($result)->data = array();
            end($result)->id = 'covID';
            end($result)->name = 'COV-HGKZ-' . $i;
            end($result)->urn = 'media1.hgkz.ch/tmp/pictures/1.jpg';
            
            return $result;
        }
        $result = array();
        $count = rand(2,3);
        for ($i = 0; $i < $count; $i++) {
            $result[] = new HGKMediaLib_Struct_EntityNode();
            $result[$i]->description = "This is the description of Sub$title no: $i";
            $result[$i]->id = "SomeSetID $level : $i";
            $result[$i]->title = "Sub$title no: $i";
            $result[$i]->subtree = generateSubTree($level - 1, "Sub" . $title);
        }
        return $result;
    }
    
   ?>
