// create a javascript hash to hold or callback methods
var readerCallback = {
	
	getActors: function(result){
        eval(result["Ref"] + ".onComplete("+result[list]+")");           
    },
	getByDate: function(result){
        document.body.style.backgroundImage = 'url(/fluxcms/themes/3-cols/images/bgNews.png)';
        document.getElementById('newsView').style.display = "block";
        document.getElementById('advancedSearchView').style.display = "none";
        document.getElementById('informationView').style.display = "none";
        document.getElementById('informationImages').style.display = "none";
        document.getElementById('playlistView').style.display = "none";
		document.getElementById("newsList").innerHTML = renderSearch('news', result);
        Behavior.apply();
        $('newsSearch').style.height = (50 + document.getElementById('newsList').offsetHeight) + 'px';
	},
	getByCollection: function(result){
        document.body.style.backgroundImage = 'url(/fluxcms/themes/3-cols/images/bgNews.png)';
        document.getElementById('newsView').style.display = "block";
        document.getElementById('advancedSearchView').style.display = "none";
        document.getElementById('informationView').style.display = "none";
        document.getElementById('informationImages').style.display = "none";
        document.getElementById('playlistView').style.display = "none";
		document.getElementById("collectionsList").innerHTML = renderSearch('collections', result);
        Behavior.apply();
        $('collectionsSearch').style.height = (50 + document.getElementById('collectionsList').offsetHeight) + 'px';
	},
	getByTitle: function(result){
        document.body.style.backgroundImage = 'url(/fluxcms/themes/3-cols/images/bgNews.png)';
        document.getElementById('newsView').style.display = "block";
        document.getElementById('advancedSearchView').style.display = "none";
        document.getElementById('informationView').style.display = "none";
        document.getElementById('informationImages').style.display = "none";
        document.getElementById('playlistView').style.display = "none";
		document.getElementById('titleList').innerHTML = renderSearch('title', result);
        Behavior.apply();
        $('titleSearch').style.height = (50 + document.getElementById('titleList').offsetHeight) + 'px';
	},
    getInformation: function(result){
        document.body.style.backgroundImage = 'url(/fluxcms/themes/3-cols/images/bgInfo.png)';
        document.getElementById('newsView').style.display = "none";
        document.getElementById('advancedSearchView').style.display = "none";
        document.getElementById('informationView').style.display = "block";
        document.getElementById('informationImages').style.display = "block";
		document.getElementById('playlistView').style.display != "block";
		if(document.getElementById('playlistData').innerHTML == ''){
			get('playlists', "");	 
		}
        document.getElementById('playlistView').style.display = "block";
        
        document.getElementById('playlistBrowser').innerHTML   = 'Playlist'; 
        document.getElementById('informationBrowse').innerHTML = renderInformation('browse', result); 
        document.getElementById('informationFiles').innerHTML  = renderInformation('files', result);

        document.getElementById('informationImages').innerHTML   = renderInformation('images', result['VBM']);
        document.getElementById('informationPlay').innerHTML     = renderInformation('play', "");
        document.getElementById('informationPicture').innerHTML  = renderInformation('picture', result['COV']);
        document.getElementById('informationPath').innerHTML     = renderInformation('path', result['path']);
        document.getElementById('informationData').innerHTML     = renderInformation('data', result['data']);	
		document.getElementById('informationPlaylist').innerHTML = renderInformation('playlist', result);
		Behavior.apply();
    },
    getThumbs: 
    function(result)
    {
        document.body.style.backgroundImage = 'url(/fluxcms/themes/3-cols/images/bgNews.png)';
        document.getElementById('newsView').style.display = "block";
        document.getElementById('advancedSearchView').style.display = "none";
        document.getElementById('informationView').style.display = "none";
        document.getElementById('informationImages').style.display = "none";
        document.getElementById('playlistView').style.display = "none";
        document.getElementById('newsImages').innerHTML = renderSearch('thumbs', result);
    },
    getFiles:
    function(result)
    {
        var div = document.getElementById('informationFilesContainer');
        div.style.display = 'none';
        div.innerHTML = renderInformation('fileTree', result);
		Behavior.apply();
        div.style.display = 'block';

    },
	
	getSubTree: function(result){
		var cont = document.getElementById('informationBrowseContainer');
		cont.style.display = "none";
		cont.innerHTML = renderInformation('subtree', result); 		
		dragDropTree.init('browseTree', false, true);
//		var eff = new Effect.toggle('informationBrowseContainer', 'slide');
		Behavior.apply();
		cont.style.display = "block";
		 
	},
    search: 
    function(result)
    {
        document.body.style.backgroundImage = 'url(/fluxcms/themes/3-cols/images/bgAdvanced.png)';
        document.getElementById('newsView').style.display = "none";
        document.getElementById('informationView').style.display = "none";
        document.getElementById('advancedSearchView').style.display = "block";
        document.getElementById('advancedSearchBrowser').style.display = "block";
        if(document.getElementById('playlistView').style.display != "block"){
            get('playlists', null);
        }
        document.getElementById('playlistView').style.display = "block";
//        document.getElementById('advancedSearchData').innerHTML = toString(result, "", 1);
        document.getElementById('advancedSearchBrowser').innerHTML = renderSearch('advancedsearchbrowser', result['paging']);
        document.getElementById('advancedSearchData').innerHTML = renderSearch('advancedsearch', result['result']);
    }
	
	
}
