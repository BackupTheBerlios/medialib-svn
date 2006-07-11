// create our remote object
// note the lowercase mapping, this is because in php4 php classes and functions have no case
// in the server you can register each function adding the case back
var remoteAuth     = new hgkmedialib_ajaxserver_auth(authCallback);  
var remoteReader   = new hgkmedialib_ajaxserver_read(readerCallback);  
var remotePlaylist = new hgkmedialib_ajaxserver_playlist(playlistCallback);  

function get(mode, entityID) {
    switch(mode){
        case 'information':
            remoteReader.getInformation(entityID);
        break;
        
        case 'subtree':
			document.getElementById('informationBrowseContainer').innerHTML = "<ul><li style='list-style-type: none;' class='loading'>loading...</li></ul>";
            remoteReader.getSubTree(entityID);
        break;

        case 'files':
			document.getElementById('informationFilesContainer').innerHTML = "<ul><li style='list-style-type: none;' class='loading'>loading...</li></ul>";
            remoteReader.getFiles(entityID);
        break;
		
		case 'playlists':	
            remotePlaylist.getPlaylists();
        break;

    }
}

function search(mode) {
    var val = document.getElementById(mode).value;
    
    switch(mode){
        case 'news':        
            var location = dhtmlHistory.getCurrentLocation();
            if (location != null && location.split('&').length > 1){
                var newlocation = '';
                location.split('&').each(function(value, index){
                            if (value.length == 0) return;
                            if (value.split('=')[0] == "news") {
                                newlocation += 'news=' + val + '&';
                            } else {
                                newlocation += value + '&';
                            }
                        }); 
            } else {
                var newlocation = "news="+val+"&collections=&title=";
            }
            dhtmlHistory.add(newlocation, null);
            remoteReader.getByDate(val);    
        break;
        
        case 'collections':
            var location = dhtmlHistory.getCurrentLocation();
            if (location != null && location.split('&').length > 1){
                var newlocation = '';
                location.split('&').each(function(value, index){
                            if (value.length == 0) return;
                            if (value.split('=')[0] == "collections") {
                                newlocation += 'collections=' + val + '&';
                            } else {
                                newlocation += value + '&';
                            }
                        }); 
            } else {
                var newlocation = "news=&collections="+val+"&title=";
            }
            dhtmlHistory.add(newlocation, null);
            remoteReader.getByCollection(val);  
        break;

        case 'title':
            var location = dhtmlHistory.getCurrentLocation();
            if (location != null && location.split('&').length > 1){
                var newlocation = '';
                location.split('&').each(function(value, index){
                            if (value.length == 0) return;
                            if (value.split('=')[0] == "title") {
                                newlocation += 'title=' + val + '&';
                            } else {
                                newlocation += value + '&';
                            }
                        }); 
            } else {
                var newlocation = "news=&collections=&title="+val;
            }
            dhtmlHistory.add(newlocation, null);
            remoteReader.getByTitle(val);   
        break;

        case 'overallSearchField':
            var inputs = $A($('overallSearch').getElementsByTagName("input"));
            var searchArray = new Array();
            inputs.each(function(input, index){
                        if (input.value != "")
                            searchArray.push(new Array(input.getAttribute('searchType'), input.value));
                    });
            remoteReader.search(searchArray, 1);
        break;
    }        
}

function page(page, string){
    remoteReader.search(string, page);
}

function playlist(mode, id01, id02, data){
	switch (mode){
        case "remove":
            remotePlaylist.removePlaylist(id01);
        break;
        case "new":
            document.getElementById('playlistNew').innerHTML = newPlaylist('insert', '');
        break;
        case "create":
            remotePlaylist.createPlaylist(document.getElementById("newPlaylistInput").value);
        break;
		case "addItem":
			remotePlaylist.addEntityToPlaylist(id01, id02, data);
		break;
		case "removeItem":
            var playlist = $(id02).parentNode.parentNode.id.split("_")[1];
			remotePlaylist.removeEntityFromPlaylist(playlist, id02);
		break;
        case "update":
            remotePlaylist.updatePlaylist(id01, id02, data);
        break;
		
	}
}

function auth(type){
    switch (type){
        case 'in':
            remoteAuth.getSession($('loginFieldUser').value, $('loginFieldPassword').value, $('domainSelect').value);
        break;
        case 'out':
            remoteAuth.dropSession();
        break;
    }
}

function add(mode, data){
    switch (mode){
        case 'searchField':
            if (! $('selector')){
                var div = document.createElement('div');
                var attribute = document.createAttribute('id');
                attribute.nodeValue = 'selector'; 
                div.setAttributeNode(attribute);
                div.style.display= "none";
                div.style.top = mouseY(data) + 'px';
                div.style.left = mouseX(data) + 'px';
                var types = new Array();
                types.push(new Array('creation_date', '<i18n:text>creation date</i18n:text>'));
                types.push(new Array('title', '<i18n:text>title</i18n:text>'));
                types.push(new Array('collection', '<i18n:text>collection</i18n:text>'));
                types.push(new Array('production_date', '<i18n:text>production date</i18n:text>'));
                types.push(new Array('language', '<i18n:text>language</i18n:text>'));
                types.push(new Array('country', '<i18n:text>country</i18n:text>'));
                types.push(new Array('actor', '<i18n:text>actor</i18n:text>'));
                types.push(new Array('director', '<i18n:text>director</i18n:text>')); 
                types.push(new Array('author', '<i18n:text>author</i18n:text>'));
                types.push(new Array('publisher', '<i18n:text>publisher</i18n:text>'));
                types.push(new Array('keywords ', '<i18n:text>keywords</i18n:text>'));
                var htmlList = '<i18n:text>chosse a field type:</i18n:text>';
                htmlList += '<ul>';
                types.each(function(value, index){
                            htmlList += '<li class="redWithA">';
                            htmlList += '<a onclick="add(\'generateSearchField\', \''+value[0]+'\'); return false;" href="">'+value[1]+'</a>';
                            htmlList += '</li>';
                        }
                        );
                htmlList += '</ul>';
                div.innerHTML = htmlList;
                $('body').appendChild(div);
                Behavior.apply();
            }
            new Effect.Appear('selector');
            break;
        case 'generateSearchField':
            var type = document.createElement('span');
            var type_id = "type_" + Math.random();
            var attribute = document.createAttribute('id');
            attribute.nodeValue = type_id; 
            type.setAttributeNode(attribute);
            type.style.display = 'none';
            type.innerHTML = '<i18n:text>' + data + '</i18n:text>';
            var newField = document.createElement('input');
            attribute = document.createAttribute('onkeypress');
            attribute.nodeValue = "trapEnter(event, 'overallSearchField');";
            newField.setAttributeNode(attribute);
            var id = 'overallSearchField ' + Math.random();
            var attribute = document.createAttribute('id');
            attribute.nodeValue = id; 
            newField.setAttributeNode(attribute);
            attribute = document.createAttribute('searchType');
            attribute.nodeValue = data; 
            newField.setAttributeNode(attribute);
            newField.style.display= "none";
            $('overallSearch').insertBefore(type, $('addSearchField'));
            $('overallSearch').insertBefore(newField, $('addSearchField'));
            if (data.indexOf("_date") != -1) {
                new Autocompleter.Date(id, 'autocompleter', {});
            }else{
                new Autocompleter.PearAjax(id, 'autocompleter','remoteReader.getSuggestions', data, {});
            }
            new Effect.Fade('selector');
            new Effect.Appear(id);
            new Effect.Appear(type_id);
            setTimeout('$("'+id+'").focus();', 1250);
            return false;
    }
}

function addToPlaylist(e, id, title)
{
    id = createNewPlaylistItem(id, title);
    $(id).style.visibility = "visible"; 
    $(id).style.backgroundColor = '#96B802';
    $(id).buttons.style.visibility = "visible";
    $(id).style.color = "#FFFFFF";
    clickPosition =  Position.cumulativeOffset(Event.element(e));
    pointerPosition = [Event.pointerX(e), Event.pointerY(e)];
    var targ;
    if (!e) var e = window.event;
    if (document.createEvent) {
        var newEvt = document.createEvent("MouseEvents");
        if (newEvt.initMouseEvent){
            newEvt.initMouseEvent("mousedown",true,true, e.view, e.detail, e.screenX, e.screenY, e.clientX, e.clientY, e.ctrlKey, e.shiftKey, e.altKey, e.metaKey, e.button, e.relatedTarget);
        } else {
            // safari doesn't know initMouseEvent !
            newEvt.initEvent("mousedown",true,true, e.view, e.detail, e.screenX, e.screenY, e.clientX, e.clientY, e.ctrlKey, e.shiftKey, e.altKey, e.metaKey, e.button, e.relatedTarget);
        }
        $(id).childNodes[2].dispatchEvent(newEvt);
    } else if( document.createEventObject ) {
        var evt = document.createEventObject();
        evt.detail = 0;
        evt.screenX = e.screenX;
        evt.screenY = e.screenY;
        evt.clientX = e.clientX;
        evt.clientY = e.clientY;
        evt.ctrlKey = e.ctrlKey;
        evt.altKey = e.altKey;
        evt.shiftKey = e.shiftKey;
        evt.metaKey = false;
        evt.button = e.button;
        evt.relatedTarget = null;
        $(id).childNodes[2].fireEvent("onmousedown",evt);
        evt.cancelBubble = true;
    }

}

function init (){
	// get('playlists', null);	
   // remoteReader.getByTitle();
   // remoteReader.getByCollection();
   // remoteReader.getByDate();
    remoteAuth.getUserData();
    initSafari();
    // initialize the DHTML History
    // framework
    dhtmlHistory.initialize();
    // subscribe to DHTML history change
    // events
    dhtmlHistory.addListener(handleHistoryChange);
    if(dhtmlHistory.isFirstLoad()){
        handleHistoryChange(dhtmlHistory.getCurrentLocation(), null);
    }
     new Autocompleter.PearAjax('overallSearchField', 'autocompleter','remoteReader.getSuggestions', 'title', {});
}

function handleHistoryChange(newLocation, historyData) {
    if (newLocation.indexOf('detail=') == 0) {
        newLocation = newLocation.substr(7);
        get('information', newLocation);
    } else if (newLocation.split("&").length > 1){
        newLocation.split("&").each(function(value, index){
                var type =  value.split('=')[0];
                var searchString = value.split("=")[1];
                switch(type){
                    case "news":
                        var searchType = 'Date';
                    break;
                    case "collections":
                        var searchType = 'Collection';
                    break;
                    case "title":
                        var searchType = 'Title';
                    break;
                    default: return;
                }
                $(type).value = searchString;
                if (value.split('=')[1] != '') {
                    eval('remoteReader.getBy' + searchType + '("'+ searchString +'");');
                }
                });
    }else remoteReader.getThumbs();
}

function trapEnter(evt, type) {
    var keycode;
    if (evt);
    else if (window.event)
        evt = window.event;
    else if (event)
        evt = event;
    else
        return true;
    if (evt.charCode)
        keycode = evt.charCode;
    else if (evt.keyCode)
        keycode = evt.keyCode;
    else if (evt.which)
        keycode = evt.which;
    else
        keycode = 0;
    if (keycode == 13) {
        if (type == 'news' || type == 'collections' || type == 'title') search(type);    
        if (type.indexOf('overallSearchField') != -1 && !Element.visible('autocompleter')) search(type);    
        switch(type) {
            case 'login': auth('in'); break;
            case 'login_pw': auth('in'); break;
            case 'newPlayList': playlist('create','','',''); break;
        }
        return false;
    }
    if (type == 'login_pw' && keycode == 9){
        $('domainSelect').focus();
        return false;
    }
    else
        return true;
}

function makeSortable(li, tree)
{
			if(!li.parentNode.parentNode.index){
				li.index = 1;
			} else {
				li.index = li.parentNode.parentNode.index +1;
			}

            li.onmousedown = function (){}
			
			var spans = li.getElementsByTagName("span");
			var x = 0;
			while (x < spans.length){
                
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
}
function createNewPlaylistItem(newid, title){
if (false) {
    alert("you must create a playlist first");
    return false;
}
var newPlaylistItem = document.createElement("li");
var classVariable = document.createAttribute("class");
classVariable.nodeValue = "playlistItem";
newPlaylistItem.setAttributeNode(classVariable);
var id = document.createAttribute("id");
id.nodeValue = "playlistitem_" + newid +'_' + Math.random();
newPlaylistItem.setAttributeNode(id);
newPlaylistItem.innerHTML = '<div class="buttons"><img class="playbutton button" src="/fluxcms/themes/3-cols/images/play.png"></img><img class="removebutton button" onclick="playlist(\'removeItem\',\'\',\''+ newPlaylistItem.id +'\',\'\')" src="/fluxcms/themes/3-cols/images/remove.png"></img></div><div class="connectors"><img align="right" class="icon"  src="/fluxcms/themes/3-cols/images/treeimgs/dark/leaf.png"/></div><span class="handle">'+ title +'</span>';
$('addItemContainer').appendChild(newPlaylistItem);
var tree = $('playlistTree');
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
$('addItemContainer').treeid = tree.id;
allUlIds.push("addItemContainer");
allUlIds.each(function (ul) {
Sortable.create($(ul), {dropOnEmpty:true, handle:'handle', containment:allUlIds,constraint:false,onChange:dragDropTree.dropItem});
});
makeSortable(newPlaylistItem, tree);
Behavior.apply();
return id.nodeValue;
}
function mouseX(evt) {
    if (evt.pageX) 
        return evt.pageX;
    else if (evt.clientX)
        return evt.clientX + (document.documentElement.scrollLeft ?
        document.documentElement.scrollLeft :
        document.body.scrollLeft);
    else 
        return null;
}
function mouseY(evt) {
    if (evt.pageY) 
        return evt.pageY;
    else if (evt.clientY)
        return evt.clientY + (document.documentElement.scrollTop ?
        document.documentElement.scrollTop :
        document.body.scrollTop);
    else 
        return null;
}
