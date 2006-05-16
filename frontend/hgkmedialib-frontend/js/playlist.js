// create a javascript hash to hold or callback methods
var playlistCallback = {
    getPlaylists: function (result){
        document.getElementById('playlistData').innerHTML = renderPlaylists(result);
        if(0 != document.getElementById('playlistTree').innerHTML.length)
        {
            // activate tree (drag&drop)
            dragDropTree.init('playlistTree', true, true);
            Behavior.apply();
        }
    },
    updatePlaylist: function (result){
        if (!result) alert('error on updatePlaylist() rpc call');        

    },
    createPlaylist: 
    function (result)
    {
        newPlaylist("add", result);
        dragDropTree.init('playlistTree', true, true);
        Behavior.apply();
        document.getElementById('playlistNew').innerHTML = newPlaylist('create', '');
    },
    removePlaylist:
    function (result)
    {
        if(result) document.getElementById('playlistTree').removeChild(document.getElementById('playlist_' + result));
    }
                
    /*
    ,
    
    addEntityToPlaylist: function (result){
        alert ("entity added!");
    },
    
    removeEntityFromPlaylist: function (result){
        alert ("entiity removed!");
    },
    
*/
}
