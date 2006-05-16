Sortable.create("firstlist", {dropOnEmpty:true,containment:["firstlist","secondlist"],constraint:false,
      onEnd:dropItem});


Sortable.create("secondlist", 
	{dropOnEmpty:true,handle:'handle',containment:["firstlist","secondlist"],constraint:false,
    onEnd:dropItem});



function dropItem(e){
	setTreeItemClassNames("tree", null);

	var parent = e.parentNode;
	// var ids = Sortable.sequence(parent.id);
	var s = "";
	var e = Sortable;
	for(i in e){
		s += "<b>" + i + "</b>: " + e[i] + "<br />";
	}
	// $(parent.id + '_debug').innerHTML = s;

}  

function branchOpenAndClose(e){
	var element = e.element;
	var trigger = element.trigger;
	var opened = Element.visible(element);
	element.parentNode.opened = opened;
	setTreeItemClassNames('tree');
}

function toggle(trigger){
	//alert(trigger.nextSibling);
	var node = trigger.nextSibling;
	while (node){
		if(node.nodeName.toLowerCase() == "ul"){
			if(!node.id){
				node.id = Math.random() + "_itm";
			}
			node.trigger = trigger;
			var eff = new Effect.toggle(node.id, 'slide', {afterFinish:branchOpenAndClose});
			break;
		} 
		node = node.nextSibling;	
	}
//	
}

function setTreeItemClassNames(listId, ul){

	if(listId != null){
		var ul = document.getElementById(listId);		
	} else {
		var ul = ul;	
	}
	
	var allClasses = [];
	var allLi = ul.getElementsByTagName("li");
	
	for(i = 0; i < allLi.length; i++){
		if(allLi[i].nodeName.toLowerCase() == "li"){
			
			
			var li = allLi[i];
			// check if this li has child-nodes
			if(li.getElementsByTagName("li")){
				var l = li.getElementsByTagName("li").length;
			} else {
				var l = 0;	
			}
			
		
			
			if(l > 0){
				var mode = "node";
				if(li.opened === undefined){
					var opened = "_open";
				} else {
					if(li.opened){
						var opened = "_open";				
					} else{
						var opened = "_closed";	
					}
				}
				var next = li.nextSibling ? li.nextSibling : false;
				var prev = li.previousSibling ? li.previousSibling : false;
				
				
			} else {
				var mode = "leaf";
				var opened = "";	
				// position		
				var next = li.nextSibling ? li.nextSibling : false;
				var prev = li.previousSibling ? li.previousSibling : false;
			}
			
				
			if(next){
				next = next.nodeName ? next.nodeName.toLowerCase() : false; 
				if(next == "li"){
					next = true;
				} else {
					next = false;	
				}
			}			
			if(prev){
				prev = prev.nodeName ? prev.nodeName.toLowerCase() : false; 
				if(prev == "li"){
					prev = true;
				} else {
					prev = false;	
				}
			}
			
			
			
			if((!next && prev) || (!next && !prev)){
				var pos = "_last";
			} 
			else if (next && !prev){
				var pos = "_first";	
			}
			else if (next && prev){
				var pos = "";	
			} else {
				var pos = "";	
			}
			
			// now set the className
			var cl = mode + opened + pos;
			//li.className = class;
			
			if(li.className.split(" ")[0] == "movable"){
				li.className = "movable " + cl;
			} else{
				li.className = cl;
			}
			
		}
	} 
}


function init(){
	// set the tree-item-classes
	var lists = ['tree']; //, 'secondlist'];
	var i = 0;
	while (i < lists.length){
		setTreeItemClassNames(lists[i], null);		
		i++;
	}
}

window.onload = init;