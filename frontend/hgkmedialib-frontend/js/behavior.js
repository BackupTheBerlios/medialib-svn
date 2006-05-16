Behavior.register(
      ".redWithA",
      function(element) {
          element.onmouseover = function(){
              element.style.backgroundColor = 'red';
              for (var e in element.childNodes) {
                  if (element.childNodes[e].tagName == 'A') element.childNodes[e].className = 'mouseOn';
              }
          }
          element.onmouseout = function(){
              element.style.backgroundColor = '#FFFFFF';
              for (var e in element.childNodes) {
                  if (element.childNodes[e].tagName == 'A') element.childNodes[e].className = 'mouseOff';
              }
          }
     }
);


Behavior.register(
      ".playlistItem",
      function(element) {
          element.onmouseover = function(){              
              element.style.backgroundColor = '#96B802';
              element.buttons.style.visibility = "visible";
			  element.style.color = "#FFFFFF";
          }
          element.onmouseout = function(){
              element.style.backgroundColor = '#FFFFFF';
              element.buttons.style.visibility = "hidden";
			  element.style.color = "#444444";
          }   
     }
);

Behavior.register(
      ".playlist",
      function(element) {
          element.onmouseover = function(){
              element.buttons.style.visibility = "visible";
          }
          element.onmouseout = function(){
              element.buttons.style.visibility = "hidden";
          }
     }
);

Behavior.register(
      "#fileTreeDiv",
      function(element) {
          element.onmouseover = function(){
			  element.lock = true;
          }
		  element.onmouseout = function(e){
				if (!e) var e = window.event;
				var tg = (window.event) ? e.srcElement : e.target;
				if (tg.id != element.id){ 
	//				return;
					// alert("we are still inside...");
				}
				var reltg = (e.relatedTarget) ? e.relatedTarget : e.toElement;
				
				
				
				while (reltg != tg && reltg.nodeName != 'BODY'){
					reltg= reltg.parentNode;
		  		}
//				document.getElementById("overallSearchField").value = tg.id +" :: " + reltg.id;
				
				
				if (reltg == tg) {
					// alert(reltg.id);
//					var eff = new Effect.toggle(element.id, 'slide');	
					return;
				};
				
				if(reltg.id == "body" && tg.id == "fileTreeDiv"){
					var eff = new Effect.toggle(element.id, 'slide');	
				}
				
				// Mouseout took place when mouse actually left layer
				// Handle event
				// var eff = new Effect.toggle(element.id, 'slide');				   
          }
		  
     }
);

Behavior.register(
      "#browseTreeDiv",
      function(element) {
          element.onmouseover = function(){
			  element.lock = true;
          }
		  element.onmouseout = function(e){
				if (!e) var e = window.event;
				var tg = (window.event) ? e.srcElement : e.target;
				if (tg.id != element.id){ 
	//				return;
					// alert("we are still inside...");
				}
				var reltg = (e.relatedTarget) ? e.relatedTarget : e.toElement;
				
				
				
				while (reltg != tg && reltg.nodeName != 'BODY'){
					reltg= reltg.parentNode;
		  		}
//				document.getElementById("overallSearchField").value = tg.id +" :: " + reltg.id;
				
				
				if (reltg == tg) {
					// alert(reltg.id);
//					var eff = new Effect.toggle(element.id, 'slide');	
					return;
				};
				
				if(reltg.id == "body" && tg.id == "browseTreeDiv"){
					var eff = new Effect.toggle(element.id, 'slide');	
				}
				
				// Mouseout took place when mouse actually left layer
				// Handle event
				// var eff = new Effect.toggle(element.id, 'slide');				   
          }
		  
     }
);


    
 
