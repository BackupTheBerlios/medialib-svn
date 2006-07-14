function renderInformation(type, data){
    switch(type)
    {
        case 'data':
            var result = "";
            $H(data).each(function(entity){
                result += '<dl class="info">';
                entity.value.each(function(element){
                    result += '<dd class="info">' + " " + element['name'] + ':&nbsp;</dd><dt class="info"> ' + " " + element['value']+ '</dt>';
                });       
                result += '</dl>';
                }); 
//                result += '<p>';
//                entity.value.each(function(element){
//                    result += '<span class="strong">' + element['name'] + '</span>:&nbsp;<span> ' + element['value'] + '</span><br/>';
//                });       
//                result += '<p/>';
//                }); 
            return result;
        break;

        case 'images':
            var images = '';
            data.each(function(image, i){
                images += '<img src="http://' + image + '" style="margin-top: 10px;" width="160px"\><br\>';
            });
            return images;
        break;

        case 'path':
            var path = '';
            for (var entity in data){
                path += ' / ';
                path += '<a class="informationPathLink" href="" onclick="dhtmlHistory.add(\'detail='+entity+'\', {id: \''+ entity +'\'});get(\'information\', \'' + entity + '\'); return false;">';
                path += data[entity];
                path += '<a\>';
            }
            return path;
        break;

        case 'picture':
            return '<img src="http://' + data + '" width="100%"\>'
        break;

        case 'play':
            return '<a onclick="location.href=\'http://media1.hgkz.ch/hgkmedialib-frontend/smil/index.php\'; return false;" class="playMovieLink"  href="">Play movie<a>';
        break;

        case 'playlist':
               return '<a onmousedown="addToPlaylist(event, \''+ data['id'] +'\', \''+ data['title'] +'\'); return false;" onclick="return false;" class="addToPlaylistLink" href="">Add to playlist</a>';
        break;
		
		   
		case 'browse':
                return '<a onclick="get(\'subtree\', \''+data['id']+'\'); return false;" class="browser" href="">Browse</a>';
        break;
		
		case 'files':
                return '<a onclick="get(\'files\', \''+data['id']+'\'); return false;" class="browser" href="">Files</a>';
        break;
		
		case 'subtree':
		
		
		
		
		/*
		
			var result = "<ul id='playlistTree'>";
	var s = "";
	for(i in data){	
		result += '<li class="playlist" id="playlist_' + i + '"><div class="buttons"><img class="playbutton button" src="/fluxcms/themes/3-cols/images/play_inv.png"></img><img class="removebutton button" src="/fluxcms/themes/3-cols/images/remove_inv.png"></img></div><div class="connectors"><img class="nodeicon"  src="/fluxcms/themes/3-cols/images/treeimgs/dark/node_closed.png"/></div><span class="opener">' +  data[i]['name'] + '</span><hr /><ul>';
		
		if(data[i]['playlist'].length != 0){
			for (var x in data[i]['playlist']){
					s +=data[i]['playlist'][x] + ":\n";
					
					var uniqueid = "playlistitem_" + x + "_" + Math.random();
					result += '<li class="playlistItem" id="'+ uniqueid +'"><div class="buttons"><img class="playbutton button" src="/fluxcms/themes/3-cols/images/play.png"></img><img class="removebutton button" src="/fluxcms/themes/3-cols/images/remove.png"></img></div><div class="connectors"><img align="right" class="icon"  src="/fluxcms/themes/3-cols/images/treeimgs/dark/leaf.png"/></div><span class="handle">' + data[i]['playlist'][x] + "</span></li>";
			}
		}
		result += "</ul></li>";
		
	}
	result += "</ul>";
		
		
		*/
		
			s = '<div id="browseTreeDiv"><ul  id="browseTree">';
			x = 0;
			var recurse = function (obj){
				var i;
				for (i in obj){
					

					if(typeof(obj[i]) == "object"){						
						s += '<li><div class="connectors"><img class="nodeicon" align="right"  src="/fluxcms/themes/3-cols/images/treeimgs/bright/node_closed.png"/></div><span class="opener"></span><span>' +  i + '</span><ul>';
						recurse(obj[i]);
						s += '</ul></li>';
					} else {
						s += '<li><div class="connectors"><img align="right" class="icon"  src="/fluxcms/themes/3-cols/images/treeimgs/bright/leaf.png"/></div><span class="handle"></span><span>' + obj[i] + '</span></li>'; 
					}

				}
			}
			
			recurse (data);
			s += "</ul><div>"; 
			return s;
		break;

        case 'fileTree':
			var result = '<div id="fileTreeDiv"><ul id="fileTree">';
            data.each(function(file){
                        result += '<li><a href="'+ file.urn +'">'+ file.name + '</li>';
                    });
            result += '</ul></div>';
            return result;
        break;
		
		
    }
}





function renderSearch(type, data)
{
    switch (type)
    {
        case 'thumbs':
            var result = '';
            data.each(function(entity, i){
                result += '<img src="' + entity.coverMedia + '" onclick="dhtmlHistory.add(\'detail='+ entity.id +'\', {id: \''+ entity.id +'\'});get(\'information\', \'' + entity.id + '\'); return false;" class="img""\><br\>';
            });
            return result;
        break;
        case 'advancedsearchbrowser':
            if (isSet(data)){
                var result = "Keyword: \"" + data.search + "\"<br/>Displayed " + data.page + " of " + data.pages + "<br/>";
                var searchString = "new Array(";
                var historyString = "";
                data.search.each(function(value){
                            searchString += "new Array(\\'" + value[0] +  "\\', \\'" + value[1] + "\\'),"
                            historyString += "::" + value[0]+"::"+value[1];
                        });
                searchString = searchString.substr(0, searchString.length - 1);
                searchString += ");";

                for (var i = 1; i <= data.pages; i++)
                {
                    var history= "search=" + i + historyString;
                        
                    result += '<a href="" onclick="dhtmlHistory.add(\''+history+'\'); page(' + i + ', eval(\''+searchString+'\')); return false;" class="';
                    result += (i == data.page) ? 'redlink' : 'whitelink' ;
                    result += '">' + i + '</a>';
                }
            }else{
                var result = "";
            }
            
            return result;
        break;

        case 'advancedsearch':
            var result = '';
            var i = 1;
            document.getElementById("advancedSearchData").innerHTML = toString(data, "", 1);
            data.each(function(entity, i) {
                        result += '<div ';
                        if (i++ % 2 == 0){
                            result += "class='odd'>";
                        }else{
                            result += "class='even'>";
                        }
                        result += '<img src="';
                        result += entity.coverMedia;
                        result += '" class="advancedThumbs"/>';
                        result += '<span class="advancedTitle">';
                        result += entity.title;
                        result += '</span><br/>';
                        result += '<span>';
                        result += entity.description;
                        result += '</span><br/>';
                        result += '<a href="" onclick="location.href=\'http://media1.hgkz.ch/hgkmedialib-frontend/smil/index.php\'; return false;" class="playMovieLink">Play This Movie</a>';
                        result += '<a onmousedown="addToPlaylist(event, \''+ entity.id +'\', \''+ entity.title +'\'); return false;" class="addToPlaylistLink" href="">Add to playlist</a>';
                        result += '<a onclick="dhtmlHistory.add(\'detail='+ entity.id +'\', {id: \''+ entity.id +'\'});get(\'information\', \'' + entity.id + '\'); return false;" class="addToPlaylistLink" href="">More Info</a>';
                        result += '</div>';
                    });
            return result;
        break;

        case 'collections':
            var result = '<dl>';
            var j = 0;
            for (var collection in data) {
                if (j++ > 0) 
                    result += '<dt class="withSeparation">';
                else
                    result += '<dt>';
                result += collection;
                result += '</dt>';
                for (var i = 0; i < data[collection].length; ++i) {
                    result += '<dd class="redWithA">';
                    result += '<a href="" onclick="dhtmlHistory.add(\'detail='+data[collection][i]['id']+'\', {id: \''+data[collection][i]['id']+'\'});get(\'information\', \'' + data[collection][i]['id'] + '\'); return false;">';
                    result += data[collection][i]['title'];
                    result += '</a>';
                    result += '</dd>';
                    }
            }
            result += '</dl>';
            return result;
        break;

        case 'news':
            /* version with definition lists
            var result = '<dl>';
            for (var i = 0; i < data.length; ++i) {
                result += '<dt>';
                result += '<a href="" onclick="get(\'information\', \'' + data[i]['id'] + '\'); return false;">';
                result += data[i]['date'];
                result += '</a>';
                result += '</dt>';
                result += '<dd>';
                result += '<a href="" onclick="get(\'information\', \'' + data[i]['id'] + '\'); return false;">';
                result += data[i]['title'];
                result += '</a>';
                result += '</dd>';
            }
            result += '</dl>';
            return result;
            */
            var result = '<ul>';
            for (var i = 0; i < data.length; ++i) {
                result += '<li class="redWithA">';
                result += '<a href="" onclick="dhtmlHistory.add(\'detail='+data[i]['id']+'\', {id: \''+data[i]['id']+'\'});get(\'information\', \'' + data[i]['id'] + '\'); return false;">';
                result += data[i]['date'];
                result += '&nbsp;';
                result += '&nbsp;';
                result += data[i]['title'];
                result += '</a>';
                result += '</li>';
            }
            result += '</ul>';
            return result;

        break;

        case 'title':
            var result = '<ul>';
                for (var i = 0; i < data.length; ++i) {
                    result += '<li class="redWithA">';
                    result += '<a href="" onclick="dhtmlHistory.add(\'detail='+data[i]['id']+'\', {id: \''+data[i]['id']+'\'});get(\'information\', \'' + data[i]['id'] + '\'); return false;">';
                    result += data[i]['title'];
                    result += '</a>';
                    result += '</li>';
                }
            result += '</ul>';
            return result;
        break;
    }
}

function newPlaylist(type, data)
{
    switch(type)
    {
        case "insert":
            return '<input id="newPlaylistInput" onkeypress="trapEnter(event, \'newPlayList\');" value="new playlist name" type="text"><input id="newPlaylistButton" value="new" onclick="playlist(\'create\',\'\',\'\',\'\'); return false;" type="button">';
        break;
        case 'create':
            return "<a href=\"\" onclick=\"playlist('new', '', '', ''); return false;\">New Playlist</a>";
        break;
        case 'add':
//            var clone = document.getElementById('playlistTree').firstChild.cloneNode(true);
//            clone.setAttribute("id", "playlist_"+data);
//            clone.childNodes[2].innerHTML = document.getElementById("newPlaylistInput").value;
//            clone.childNodes[4].innerHTML = '';
//            clone.childNodes[4].setAttribute("id", "ul_" + Math.random());
            var clone = document.createElement("li");
            clone.setAttribute('id', 'playlist_' + data);
            clone.setAttribute('class', 'playlist');
            clone.innerHTML = 
                '<div class="buttons">'+
                    '<img class="playbutton button" onclick="location.href=\'http://media1.hgkz.ch/hgkmedialib-frontend/smil/index.php\'; return false;" src="/fluxcms/themes/3-cols/images/play_inv.png"></img>'+
                    '<img class="removebutton button" onclick="playlist(\'remove\',\''+ data +'\',\'\',\'\')" src="/fluxcms/themes/3-cols/images/remove_inv.png"></img>'+
                '</div>'+
                '<div class="connectors">'+
                    '<img class="nodeicon"  src="/fluxcms/themes/3-cols/images/treeimgs/dark/node_closed.png"/>'+
                '</div>'+
                '<span class="opener">' + document.getElementById("newPlaylistInput").value + '</span><hr/>'+
                '<ul>'+
                    '<li class="playlistItem" style="display: none; visibility: hidden; bottom: 0px;" id="dummy_'+ Math.random() +'">'+
                        '<div class="connectors" style="width: 39px; margin-left: 0px; height: 13px;">'+
                            '<img align="right" src="http://media1.hgkz.ch/fluxcms/themes/3-cols/images/treeimgs/dark/leaf.png" class="icon"/>'+
                        '</div>'+
                        '<span class="handle" style="cursor: move;">dummy</span>'+
                    '</li>'+
                    '<li id="dropper_'+ Math.random() +'">'+
                        '<div class="connectors" style="width: 39px; margin-left: 0px; height: 13px;">'+
                            '<img align="right" src="http://media1.hgkz.ch/fluxcms/themes/3-cols/images/treeimgs/dark/leaf.png" class="icon"/>'+
                        '</div>'+
                        '<span>drop element here</span>'+
                    '</li>'+
                '</ul>';
            if(document.getElementById("playlistTree").innerHTML.length == 0){
                document.getElementById("playlistTree").appendChild(clone);
            }else{
                document.getElementById('playlistTree').insertBefore(clone, document.getElementById('playlistTree').firstChild);
            }
//            return '<li id="playlist_'+data+'" class="playlist"><div class="buttons"><img class="playbutton button" onclick="location.href=\'http://media1.hgkz.ch/hgkmedialib-frontend/smil/index.php\'; return false;" src="/fluxcms/themes/3-cols/images/play_inv.png"></img><img class="removebutton button" src="/fluxcms/themes/3-cols/images/remove_inv.png"></img></div><div class="connectors"><img class="nodeicon"  src="/fluxcms/themes/3-cols/images/treeimgs/dark/node_closed.png"/></div><span class="opener">'+ document.getElementById("newPlaylistInput").value+'</span><hr/><ul/></li>' + document.getElementById('playlistTree').innerHTML;
        break;
    }
    
}


function renderPlaylists(data){
	
	/*
	var i;
	var result = "<ul id='playlistTree'>";
	for(i in data){
		result += "<li><span class='buttons'></span><span class='nodeicon'></span><span class='opener'>" +  data[i]['name'] + "</span><ul>";
		for (var x in data[i]['playlist']){
				result += "<li class='playlistItem'><span class='buttons'></span><span class='nodeicon'></span><span class='handle'>" + data[i]['playlist'][x] + "</span></li>";
		}
		result += "</ul>";
	}
	result += "</ul>";
	*/

    if (data.length == 0) return '<ul id="playlistTree"/>';

	var result = "<ul id='playlistTree'>";
	var s = "";
	for(i in data){	
		result += '<li class="playlist" id="playlist_' + i + '"><div class="buttons"><img class="playbutton button" onclick="location.href=\'http://media1.hgkz.ch/hgkmedialib-frontend/smil/index.php\'; return false;" src="/fluxcms/themes/3-cols/images/play_inv.png"></img><img class="removebutton button" onclick="playlist(\'remove\',\''+i+'\',\'\',\'\')" src="/fluxcms/themes/3-cols/images/remove_inv.png"></img></div><div class="connectors"><img class="nodeicon"  src="/fluxcms/themes/3-cols/images/treeimgs/dark/node_closed.png"/></div><span class="opener">' +  data[i]['name'] + '</span><hr /><ul>';
		
		if(data[i]['playlist'].length == 0){
            result +=
                '<li class="playlistItem" style="display: none; visibility: hidden; bottom: 0px;" id="dummy_'+ Math.random() +'">'+
                    '<div class="connectors" style="width: 39px; margin-left: 0px; height: 13px;">'+
                        '<img align="right" src="http://media1.hgkz.ch/fluxcms/themes/3-cols/images/treeimgs/dark/leaf.png" class="icon"/>'+
                    '</div>'+
                    '<span class="handle" style="cursor: move;">dummy</span>'+
                '</li>'+
                '<li id="dropper_'+ Math.random() +'">'+
                    '<div class="connectors" style="width: 39px; margin-left: 0px; height: 13px;">'+
                        '<img align="right" src="http://media1.hgkz.ch/fluxcms/themes/3-cols/images/treeimgs/dark/leaf.png" class="icon"/>'+
                    '</div>'+
                    '<span>drop element here</span>'+
                '</li>';
        }else{
		//	result += '<li id="dummy_'+ Math.random() +'" style="display:none; visibility:hidden;" class="playlistItem">'/*<div class="buttons"><img class="playbutton button" src="/fluxcms/themes/3-cols/images/play.png"></img><img class="removebutton button" src="/fluxcms/themes/3-cols/images/remove.png"></img></div>*/+'<div class="connectors"><img align="right" class="icon"  src="/fluxcms/themes/3-cols/images/treeimgs/dark/leaf.png"/></div><span class="handle">dummy</span></li>';
            result +=
                '<li class="playlistItem" style="display: none; visibility: hidden; bottom: 0px;" id="dummy_'+ Math.random() +'">'+
                    '<div class="connectors" style="width: 39px; margin-left: 0px; height: 13px;">'+
                        '<img align="right" src="http://media1.hgkz.ch/fluxcms/themes/3-cols/images/treeimgs/dark/leaf.png" class="icon"/>'+
                    '</div>'+
                    '<span class="handle" style="cursor: move;">dummy</span>'+
                '</li>'+
                '<li style="display: none; visibility: hidden;" id="dropper_'+ Math.random() +'">'+
                    '<div class="connectors" style="width: 39px; margin-left: 0px; height: 13px;">'+
                        '<img align="right" src="http://media1.hgkz.ch/fluxcms/themes/3-cols/images/treeimgs/dark/leaf.png" class="icon"/>'+
                    '</div>'+
                    '<span>drop element here</span>'+
                '</li>';
			for (var x in data[i]['playlist']){
					s +=data[i]['playlist'][x] + ":\n";
					
					var uniqueid =  x;
					//var uniqueid = "playlistitem_" + x + "_" + Math.random();
					result += '<li class="playlistItem" id="'+ uniqueid +'"><div class="buttons"><img class="playbutton button" src="/fluxcms/themes/3-cols/images/play.png"></img><img class="removebutton button" onclick="playlist(\'removeItem\',\''+ i +'\',\''+ x +'\',\'\')" src="/fluxcms/themes/3-cols/images/remove.png"></img></div><div class="connectors"><img align="right" class="icon"  src="/fluxcms/themes/3-cols/images/treeimgs/dark/leaf.png"/></div><span class="handle">' + data[i]['playlist'][x] + "</span></li>";
			}
		}
		result += "</ul></li>";
		
	}
	result += "</ul>";
	// alert(s);
	
	return result;
	
	
}

function setLogin(type, data){
    switch(type)
    {
        case 'login':
            $('login').innerHTML = '<div class="wrapInput"><span><i18n:text>user</i18n:text></span><input type="text" id="loginFieldUser" onkeypress="trapEnter(event, \'login\');" value="user"/></div><br/><div class="wrapInput"><span><i17n:text>pass</i18n:text></span><input type="password" onkeypress="return trapEnter(event, \'login_pw\');" id="loginFieldPassword" value="password"/></div><br/><div class="wrapInput"><span><i18n:text>domain</i18n:text></span><select onkeypress="trapEnter(event, \'login\');" id="domainSelect"></select> </div><a href="" class="advancedSearchLink" id="loginButton" onclick="auth(\'in\'); return false;"><i18n:text>login</i18n:text></a>'
            remoteAuth.getDomains();
        break;
        case 'domains':
        data.each(function(domain){
                    var option = document.createElement('option');
                    option.setAttribute('value', domain);
                    option.innerHTML = domain;
                    $('domainSelect').appendChild(option);
                    
                });
        break;
        case 'logged':
        $('login').innerHTML = 'you are logged in as<br/>' + data.first + ' ' + data.last + '<a href="" class="advancedSearchLink" id="loginButton" onclick="auth(\'out\'); return false;"><i18n:text>logout</i18n:text></a>';
        break;
    }
}

function renderDraggablePlaylistItem(trigger, id, label){	
	var li = "<ul><li class='addToPlaylist'>" + label+ "</li>";
	var dummy = document.getElementById("dummy");
	dummy.innerHTML = li;
	document.onmousemove = updateMousePos;				
	function updateMousePos (e){
		var tip = document.getElementById("dummy");
		var x = (document.all) ? window.event.x + document.body.scrollLeft : e.pageX;
		var y = (document.all) ? window.event.y + document.body.scrollTop  : e.pageY;
		if (tip != null) {
			tip.style.left = (x) + "px";
			tip.style.top 	= (y) + "px";
		}
		
	}
}



