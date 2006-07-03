<?php

header('Content-Type: text/xml');

$feedName = 'Example Feed';
$feedDescr = 'bla';
$feedUrl = 'http://' .  $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

$rssBase =<<<EOT
<?xml version="1.0" encoding="UTF-8"?>
<rdf:RDF
    xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#"
    xmlns:dc="http://purl.org/dc/elements/1.1/"
    xmlns="http://purl.org/rss/1.0/"
>
	
	<channel rdf:about="$feedUrl">
	    <title>$feedName</title>
	    <link>$feedUrl</link>
	    <description>$feedDescr</description>
	    
	    %s
	    
	</channel>	

%s	
	
</rdf:RDF>
EOT;

$itemBase = <<<EOT
<item rdf:about="%s">
		<link>%s</link>
	    <title>%s</title>	    
	    <description>%s</description>
	    <dc:date>%s</dc:date>
</item>
EOT;

$itemListBase =<<<EOT
<items>
      <rdf:Seq>
      %s
      </rdf:Seq>
</items>
EOT;

$listItemBase=<<<EOT
	<rdf:li resource="%s"/>        
EOT;


$d = 
array(
array('url'=> 'http://foo.bar/', 'link'=>'link', 'title'=>'foobar',   'descr'=>'bla', 'date'=>'2005-03-02'),
array('url'=> 'http://foo.bar/1', 'link'=>'link1', 'title'=>'foobar2', 'descr'=>'bla3', 'date'=>'2005-04-04'),
);



$items = '';
$listitems = '';
foreach ($d as $itemData){
	$items .= sprintf($itemBase, $itemData['url'], $itemData['url'], $itemData['title'], $itemData['descr'], $itemData['date']);
	$listitems .= sprintf($listItemBase, $itemData['url']);	
}

printf($rssBase, sprintf($itemListBase, $listitems), $items);


?>