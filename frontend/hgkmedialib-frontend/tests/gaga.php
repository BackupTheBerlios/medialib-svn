<?php

$ob = array (
  'SubSubSet no: 0' => 
  array (
    'SubSubSubSet no: 0' => 
    array (
      'SubSubSubSubSet no: 0' => 
      array (
        'MPEG0' => NULL,
        'MPEG1' => NULL,
        'MPEG2' => NULL,
        'VBM-HGKZ-3' => NULL,
        'COV-HGKZ-3' => NULL,
      ),
      'SubSubSubSubSet no: 1' => 
      array (
        'MPEG0' => NULL,
        'MPEG1' => NULL,
        'MPEG2' => NULL,
        'VBM-HGKZ-3' => NULL,
        'COV-HGKZ-3' => NULL,
      ),
    ),
    'SubSubSubSet no: 1' => 
    array (
      'SubSubSubSubSet no: 0' => 
      array (
        'MPEG0' => NULL,
        'MPEG1' => NULL,
        'MPEG2' => NULL,
        'VBM-HGKZ-3' => NULL,
        'COV-HGKZ-3' => NULL,
      ),
      'SubSubSubSubSet no: 1' => 
      array (
        'MPEG0' => NULL,
        'MPEG1' => NULL,
        'VBM-HGKZ-2' => NULL,
        'COV-HGKZ-2' => NULL,
      ),
      'SubSubSubSubSet no: 2' => 
      array (
        'MPEG0' => NULL,
        'MPEG1' => NULL,
        'VBM-HGKZ-2' => NULL,
        'COV-HGKZ-2' => NULL,
      ),
    ),
    'SubSubSubSet no: 2' => 
    array (
      'SubSubSubSubSet no: 0' => 
      array (
        'MPEG0' => NULL,
        'MPEG1' => NULL,
        'VBM-HGKZ-2' => NULL,
        'COV-HGKZ-2' => NULL,
      ),
      'SubSubSubSubSet no: 1' => 
      array (
        'MPEG0' => NULL,
        'MPEG1' => NULL,
        'VBM-HGKZ-2' => NULL,
        'COV-HGKZ-2' => NULL,
      ),
    ),
  ),
  'SubSubSet no: 1' => 
  array (
    'SubSubSubSet no: 0' => 
    array (
      'SubSubSubSubSet no: 0' => 
      array (
        'MPEG0' => NULL,
        'MPEG1' => NULL,
        'VBM-HGKZ-2' => NULL,
        'COV-HGKZ-2' => NULL,
      ),
      'SubSubSubSubSet no: 1' => 
      array (
        'MPEG0' => NULL,
        'MPEG1' => NULL,
        'MPEG2' => NULL,
        'VBM-HGKZ-3' => NULL,
        'COV-HGKZ-3' => NULL,
      ),
      'SubSubSubSubSet no: 2' => 
      array (
        'MPEG0' => NULL,
        'MPEG1' => NULL,
        'MPEG2' => NULL,
        'VBM-HGKZ-3' => NULL,
        'COV-HGKZ-3' => NULL,
      ),
    ),
    'SubSubSubSet no: 1' => 
    array (
      'SubSubSubSubSet no: 0' => 
      array (
        'MPEG0' => NULL,
        'MPEG1' => NULL,
        'MPEG2' => NULL,
        'VBM-HGKZ-3' => NULL,
        'COV-HGKZ-3' => NULL,
      ),
      'SubSubSubSubSet no: 1' => 
      array (
        'MPEG0' => NULL,
        'MPEG1' => NULL,
        'VBM-HGKZ-2' => NULL,
        'COV-HGKZ-2' => NULL,
      ),
      'SubSubSubSubSet no: 2' => 
      array (
        'MPEG0' => NULL,
        'MPEG1' => NULL,
        'VBM-HGKZ-2' => NULL,
        'COV-HGKZ-2' => NULL,
      ),
    ),
  ),
);


function recurse ($ob){
	foreach ($ob as $k => $v){
		if(is_array($v)){
			echo "<li>$k<ul>";
			recurse($v);
			echo "</ul></li>";			
		} else {
			echo "<li>$k</li>";
		}
	}

}

echo "<ul>";

recurse($ob);


echo "</ul>";



?>