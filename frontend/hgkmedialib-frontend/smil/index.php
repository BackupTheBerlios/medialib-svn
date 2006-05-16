<?php
$movies[] = "mov1.mov";
$movies[] = "mov1.mov";

$smil .= '<smil>
	<head>
		<layout>
			<root-layout id="rl" background-color="black"/>
			<region id="ad" left="0" top="0"/>
			<region id="bbc" width="100%" height="100%" fit="fill"/>
		</layout>
	</head>
	<body>
		<seq>';

foreach($movies as $movie)
	{
		$smil .= '<video src="http://web5.mediagonal.ch/yves/'.$movie.'" region="ad" duration="34s"/>';
	} // end of: foreach($movies as $movie)
					
					
$smil .= '</seq></body></smil>';


header("Content-Type: application/quicktime");
header('Content-Disposition: attachment; filename="video.smil"');

 
echo $smil;
?>
