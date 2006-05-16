var dragDropTree = {

	dropItem: function(e){
		dragDropTree.setTreeItemClassNames("tree", null);
	
		var parent = e.parentNode;
		// var ids = Sortable.sequence(parent.id);
		var s = "";
		var e = Sortable;
		for(i in e){
			s += "<b>" + i + "</b>: " + e[i] + "<br />";
		}
		// $(parent.id + '_debug').innerHTML = s;
	
	},  

	branchOpenAndClose: function(e){
		var element = e.element;
		var trigger = element.trigger;
		var opened = Element.visible(element);
		element.parentNode.opened = opened;
		dragDropTree.setTreeItemClassNames('tree');
	},
	
	toggle: function(trigger){
		//alert(trigger.nextSibling);
		var node = trigger.nextSibling;
		while (node){
			if(node.nodeName.toLowerCase() == "ul"){
				if(!node.id){
					node.id = Math.random() + "_itm";
				}
				node.trigger = trigger;
				var eff = new Effect.toggle(node.id, 'slide', {afterFinish:dragDropTree.branchOpenAndClose});
				break;
			} 
			node = node.nextSibling;	
		}
	//	
	},
	
	setTreeItemClassNames: function(listId, ul){
	
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
	},
	
	
	init: function(id){
		// set the tree-item-classes
		var tree = document.getElementById(id);
		// styles
		dragDropTree.setTreeItemClassNames(tree.id, null);		
		// toggle
		var allDivs = tree.getElementsByTagName("div");
		var i = 0;
		while (i < allDivs.length){
			var div = allDivs[i];
			div.style.cursor = "pointer";
		  	div.onclick = function() {
				dragDropTree.toggle(this);
				return false;
			 }
			i++;
		}
		// dragDrop --> get all Uls to 
		var allUls = tree.getElementsByTagName("ul");
		var allUlIds = [];
		var i = 0;
		while (i < allUls.length){
			var ul = allUls[i];
			/*ul.style.height = 100;*/
			if(!ul.id){
				ul.id = "ul_" + Math.random();
			}
			allUlIds.push(ul.id);
			i++;
		}
		var i = 0;
		// create sortables
		while (i < allUlIds.length){
			var id = allUlIds[i];
			Sortable.create(id, 
			{dropOnEmpty:true, handle:'handle', containment:allUlIds,constraint:false,
			onChange:dragDropTree.dropItem});
			i++;
		}	
		
		var allLis = tree.getElementsByTagName("li");
		var i = 0;
		// create sortables
		while (i < allLis.length){
			var li = allLis[i];
			li.style.cursor = "move";
			i++;
		}	
		dragDropTree.setTreeItemClassNames(null, tree);
	}
}



// window.onload = dragDropTree.init;