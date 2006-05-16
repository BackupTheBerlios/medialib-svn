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
            remoteReader.getByDate(val);    
        break;
        
        case 'collections':
            remoteReader.getByCollection(val);  
        break;
        
        case 'title':
            remoteReader.getByTitle(val);   
        break;

        case 'overallSearchField':
            remoteReader.search(val, 1);
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
			remotePlaylist.removeEntityFromPlaylist(id01, id02);
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

function addToPlaylist(e)
{
    var targ;
    if (!e) var e = window.event;
    if (document.createEvent) {
        var newEvt = document.createEvent("MouseEvents");
        newEvt.initMouseEvent("mousedown",true,true, e.view, e.detail, e.screenX, e.screenY, e.clientX, e.clientY, e.ctrlKey, e.shiftKey, e.altKey, e.metaKey, e.button, e.relatedTarget);
        $('addSpan').dispatchEvent(newEvt);
        $('informationData').innerHTML = toString(newEvt, "event", 1);
    }

}

function init (){
	// get('playlists', null);	
    remoteReader.getByTitle();
    remoteReader.getByCollection();
    remoteReader.getByDate();
    remoteReader.getThumbs();
    remoteAuth.getUserData();
    initSafari();
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
        if (type == 'news' || type == 'collections' || type == 'title' || type == 'overallSearchField') search(type);    
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
