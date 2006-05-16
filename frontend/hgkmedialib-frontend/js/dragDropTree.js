var dragDropTree = {

	dropItem: function(e){
		dragDropTree.setTreeItemClassNames(e.treeid, null);
		var parent = e.parentNode;
		// var ids = Sortable.sequence(parent.id);
		var e = Sortable;
        s = "";
        $A(e).each(function(x, i) {
                s+= "<b>" + i + "</b>: " + x + "<br />";
                });
		// $(parent.id + '_debug').innerHTML = s;
	
	},  

	branchOpenAndClose: function(e){
		var element = e.element;
		var trigger = element.trigger;
		var opened = Element.visible(element);
		element.parentNode.opened = opened;
		dragDropTree.setTreeItemClassNames(element.treeid, null);
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
		var s = "";
        $A(allLi).each(function(li, i) {
			if(li.nodeName.toLowerCase() == "li"){
				
				var li = li;
		
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
				//var cl = mode + opened + pos;
				var cl = mode + opened;
				//li.className = class;
				
			/*
			
				if(li.className.split(" ")[0] == "playlistItem"){
					li.className = "playlistItem " + cl;
				} else{
					li.className = cl;
				}
				
				var s = li.nodeicon ? li.nodeicon : li.opener;
				li = li.index*10;
				*/
				/*
				// set the connector to the nodeicon-span
				var icon = li.nodeicon;
				//icon.innerHTML = "<img src='/fluxcms/themes/3-cols/images/treeimgs/dark/"+cl+".png' />";
				icon.className = cl;
				li.className = cl;
					*/
				
				li.mode = mode;
				li.pos = pos;
				
				
				li.connectors.style.width = (li.index*20) - 1 + "px";
				
				var src = li.icon.src.split("/");
				src[src.length-1] = cl + ".png";
				src = src.join("/");
				li.icon.src = src;
				
				
				if(li.parentNode.parentNode.pos == "_last" && mode == "leaf"){
					li.connectors.style.width = "19px";
					li.connectors.style['marginLeft'] = ((li.index*20)-20) + "px";
				} else {
					li.connectors.style.width = (li.index*20) - 1 + "px";
					li.connectors.style['marginLeft'] = (0) + "px";
				}
				if(pos == "_last"){
					if(mode == "leaf"){
					 	li.connectors.style.height = "10px"; 			
					} else {
						li.connectors.style.height = "13px"; 			
					}
				} else {
					 li.connectors.style.height = "13px"; 
				}
				// li.icon.style['padding-left'] = (li.index-1)*20 + "px";
				// alert(li.icon.src);
				//li.icon.style['padding-left'] = (li.index-1)*20 + "px";
			
			}
		} 
        );
//		alert(s);

	},
	
	
	init: function(id, dragDrop, closed){
		// set the tree-item-classes
		var tree = document.getElementById(id);
		// styles	
		// toggle
		
		/*
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
		*/
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
			ul.treeid = tree.id;
			allUlIds.push(ul.id);
			i++;
		}
		if(dragDrop){
			var i = 0;		
            allUlIds.push("addItemContainer");
			// create sortables
			while (i < allUlIds.length){
				var id = allUlIds[i];
				Sortable.create(id, 
				{dropOnEmpty:true, handle:'handle', containment:allUlIds,constraint:false,
				onChange:dragDropTree.dropItem});
				i++;
			}	
		}
		
	
		var allLis = tree.getElementsByTagName("li");
		var i = 0;
		// create sortables
		while (i < allLis.length){
			var li = allLis[i];
			if(!li.parentNode.parentNode.index){
				li.index = 1;
			} else {
				li.index = li.parentNode.parentNode.index +1;
			}
			
			li.onmousedown = function (){
				// alert(this);	
			}
			
			var spans = li.getElementsByTagName("span");
			var x = 0;
			while (x < spans.length){
				
					/*
								
					if(spans[x].className == "buttons"){
						li.buttons = spans[x];
					}
					if(spans[x].className == "nodeicon"){
						li.nodeicon = spans[x];
						
					}	
						*/
					
					if(spans[x].parentNode === li){
					if(spans[x].className == "handle"){
						spans[x].style.cursor = "move";
						li.handle = spans[x];
					}	
						
					if(spans[x].className == "opener"){
						li.opener = spans[x];
						spans[x].style.cursor = "pointer";
						spans[x].onclick = function() {
							dragDropTree.toggle(this);
							return false;
						}
					}
					
				}
				x++;
			}
			
		
			var divs = li.getElementsByTagName("div");
			var x = 0;
			while (x < divs.length){
				if(divs[x].parentNode === li){					
					if(divs[x].className == "buttons"){
						li.buttons = divs[x];
					}					
					if(divs[x].className == "connectors"){
						li.connectors = divs[x];
					}	
					
				}
				x++;
			}
			
			var imgs = li.getElementsByTagName("img");
			var x = 0;
			while (x < imgs.length){
				if(imgs[x].parentNode.parentNode === li){					
					if(imgs[x].className == "nodeicon"){		
						li.icon = imgs[x];
						imgs[x].li = li;
						imgs[x].onclick = function() {
							dragDropTree.toggle(this.li.opener);
							return false;
						}
					}	
					
					if(imgs[x].className == "icon"){
						li.icon = imgs[x];
						imgs[x].li = li;
						imgs[x].onclick = function() {
							dragDropTree.toggle(this.li.opener);
							return false;
						}
					}	
					
				}
				x++;
			}
			
			li.treeid = tree.id;
			if(closed){
				li.opened = false;	
			}
			i++;
		}	
		// set first mode
		if(closed){
			var i = 0;
			while (i < allUls.length){
				ul = allUls[i];
				ul.style.display = "none";
				i++;
			}
		}		
		dragDropTree.setTreeItemClassNames(tree.id, null);	
	}
}



// window.onload = dragDropTree.init;
