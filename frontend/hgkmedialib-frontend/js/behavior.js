var ismousedown = false;

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
              if (!ismousedown) {                               // this global var is modified in /hgkmedialib-frontend/js/scriptaculous/src/dragdrop.js
                  element.style.backgroundColor = '#96B802';
                  element.buttons.style.visibility = "visible";
                  element.style.color = "#FFFFFF";
              }
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
              if (!ismousedown) {                               // this global var is modified in /hgkmedialib-frontend/js/scriptaculous/src/dragdrop.js
                  element.buttons.style.visibility = "visible";
              }
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
    //              return;
                    // alert("we are still inside...");
                }
                var reltg = (e.relatedTarget) ? e.relatedTarget : e.toElement;
                
                
                
                while (reltg != tg && reltg.nodeName != 'BODY'){
                    reltg= reltg.parentNode;
                }
//              document.getElementById("overallSearchField").value = tg.id +" :: " + reltg.id;
                
                
                if (reltg == tg) {
                    // alert(reltg.id);
//                  var eff = new Effect.toggle(element.id, 'slide');   
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
                var reltg = (e.relatedTarget) ? e.relatedTarget : e.toElement;
                
                while (reltg != tg && reltg.nodeName != 'BODY'){
                    reltg= reltg.parentNode;
                }
                if (reltg == tg) {
                    return;
                }
                if(reltg.id == "body" && tg.id == "browseTreeDiv"){
                    var eff = new Effect.toggle(element.id, 'slide');   
                }
          }
     }
);

// IE needs the behavior on the #informationBrowseContainer and #informationFilesContainer
// rather than on the #browseTreeDiv and #fileTreeDiv, thus the 2 following behaviors.

Behavior.register("#informationBrowseContainer", function(element)
{
    element.onmouseout = function(e) 
    {
        if (!e) var e = window.event;
        var tg = (window.event) ? e.srcElement : e.target;
        var reltg = (e.relatedTarget) ? e.relatedTarget : e.toElement;
        while (reltg != tg && reltg.nodeName != 'BODY'){
            reltg= reltg.parentNode;
        }
        if (reltg == tg) {
            return;
        };
        if(reltg.id == "body" && tg.id == "informationBrowseContainer"){
            var eff = new Effect.toggle('browseTreeDiv', 'slide');  
        }
        
    }
});

Behavior.register("#informationFilesContainer", function(element)
{
    element.onmouseout = function(e) 
    {
        if (!e) var e = window.event;
        var tg = (window.event) ? e.srcElement : e.target;
        var reltg = (e.relatedTarget) ? e.relatedTarget : e.toElement;
        while (reltg != tg && reltg.nodeName != 'BODY'){
            reltg= reltg.parentNode;
        }
        if (reltg == tg) {
            return;
        };
        if(reltg.id == "body" && tg.id == "informationFilesContainer"){
            var eff = new Effect.toggle('fileTreeDiv', 'slide');    
        }
        
    }
});

Behavior.register(
      "#informationFiles",
      function(element) {
          element.onmouseover = function(){
              element.lock = true;
          }
          element.onmouseout = function(e){
          
                var empty;
                if ($('fileTreeDiv') == empty || $('fileTreeDiv') == null) return;
                
                if (Element.getStyle($('fileTreeDiv'), 'display')=='none') return;
                if (!e) var e = window.event;
                var tg = (window.event) ? e.srcElement : e.target;
                
                var reltg = (e.relatedTarget) ? e.relatedTarget : e.toElement;

                // if leaving browser window through the element
                if(! reltg) return;
                
                if(e.toElement && e.toElement.id == 'informationFilesContainer') return;
                while (reltg.id != "fileTreeDiv" && reltg != "informationFilesContainer" && reltg.id != "informationFiles" && reltg.nodeName != 'BODY'){
                    reltg= reltg.parentNode;
                }
                
                if (reltg.id == "fileTreeDiv") {
                    return;
                };
                
                if(reltg.id == "body" && tg.id == "informationFiles"){
                    var eff = new Effect.SlideUp("fileTreeDiv");    
                }
          }
          
     }
);

Behavior.register(
      "#informationBrowse",
      function(element) {
          element.onmouseover = function(){
              element.lock = true;
          }
          element.onmouseout = function(e){
          
                var empty;
                if ($('browseTreeDiv') == empty || $('browseTreeDiv') == null) return;
                
                if (Element.getStyle($('browseTreeDiv'), 'display')=='none') return;
                
                if (!e) var e = window.event;
                var tg = (window.event) ? e.srcElement : e.target;
                
                var reltg = (e.relatedTarget) ? e.relatedTarget : e.toElement;

                // if leaving browser window through the element
                if(! reltg) return;
                
                if(e.toElement && e.toElement.id == 'informationBrowseContainer') return;
                
                while (reltg.id != "browseTreeDiv" && reltg != "informationBrowseContainer" && reltg.id != "informationBrowse" && reltg.nodeName != 'BODY'){
                    reltg= reltg.parentNode;
                }
                
                if (reltg.id == "browseTreeDiv") {
                    return;
                };
                
                if(reltg.id == "body" && tg.id == "informationBrowse"){
                    var eff = new Effect.SlideUp("browseTreeDiv");  
                }
          }
          
     }
);
