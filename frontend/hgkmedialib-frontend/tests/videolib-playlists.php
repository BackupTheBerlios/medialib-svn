<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<title>script.aculo.us sortable functional test file</title>
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<script src="../js/scriptaculous/lib/prototype.js" type="text/javascript"></script>
<script src="../js/scriptaculous/src/scriptaculous.js" type="text/javascript"></script>
<script src="../js/scriptaculous/src/unittest.js" type="text/javascript"></script>
<style type="text/css" media="screen">

body{
	background-color: #333333;
	font-size:11px;
	font-family:Georgia, "Times New Roman", Times, serif;
	color:white;
}
  

/* Tree-Nodes....*/
/* closed */
.node_closed_first{
	background-image: url(treeimgs/node_closed_first.png);
	background-repeat:no-repeat;
}

.node_closed_last{
	background-image: url(treeimgs/node_closed_last.png);
	background-repeat:no-repeat;
}
.node_closed{
	background-image: url(treeimgs/node_closed.png);
	background-repeat:no-repeat;
}
/* open */
.node_open_first{
	background-image: url(treeimgs/node_open_first.png);
	background-repeat:no-repeat;
}

.node_open_last{
	background-image: url(treeimgs/node_open_last.png);
	background-repeat:no-repeat;
}
.node_open{
	background-image: url(treeimgs/node_open.png);
	background-repeat:no-repeat;
}
/* .................*/


/* Tree-Leafs....*/
.leaf_last{
	background-image: url(treeimgs/leaf_last.png);
	background-repeat:no-repeat;
}
.leaf_first{
	background-image: url(treeimgs/leaf_first.png);
	background-repeat:no-repeat;
}
.leaf{
	background-image: url(treeimgs/leaf.png);
	background-repeat:no-repeat;
}
/* .................*/



span.handle {
	background-color:#D6EED5;
	cursor: move;
}

.treelist li {
	list-style-type: none;
	margin-left: -35px;
	padding-left: 25px;
}




</style>
</head>
<body>
<!-- <div class="menu_header" id="menu_header1"><a href="#" onClick="upAndDown(this, 'menu_block1'); return false;">List 1</a></div>
  <div class="menu_block" id="menu_block1">-->


<ul class="treelist" id="tree"><li id="a">
    <div >List 1</div>
	
    <ul id="firstlist"  class="treelist">
      <?php
	
	$i = 0;
	$max = rand(1, 5);
	while($i < $max){
		echo "				<li >item a {$i} (first list)</li>"; 
		$i++;
	}
	
	?>
    </ul>
  </li><li id="b">
    <div>List 2</div>
    <ul id="secondlist"  class="treelist">
      <?php
	
	$i = 0;
	$max = rand(1, 5);
	while($i < $max){
		echo "				<li  >item b {$i} (second list)</li>"; 
		$i++;
	}
	
	?>
    </ul>

  </li><li id="c">
	<div>List</div>
	<ul class="treelist">
      <?php
	
	$i = 0;
	$max = rand(1, 3);
	while($i < $max){
		echo "				<li><div>item</div><ul><li>item</li><li>item</li><li>item</li></ul></li>"; 
		$i++;
	}
	
	?>
    </ul>
	</li>

  
</ul>


<hr style="clear:both" />
<pre id="firstlist_debug"></pre>
<pre id="secondlist_debug"></pre>
<script src="./gui.js"type="text/javascript"></script>
</body>
</html>
