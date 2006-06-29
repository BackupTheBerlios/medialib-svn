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
        if(result){
            var eff = new Effect.Fade('playlist_' + result);
            setTimeout("$('playlist_" + result + "').parentNode.removeChild($('playlist_"+ result +"'))",1250);
        }
    },
                
    /*
    
    addEntityToPlaylist: function (result){
        alert ("entity added!");
    },
    
*/
    removeEntityFromPlaylist: function (result){
            var playlist = $(result).parentNode.parentNode;
            var eff = new Effect.Fade(result);
            setTimeout("$('" + result + "').parentNode.removeChild($('"+ result +"'))",1250);
            setTimeout(
            'if($("'+ $(result).parentNode.id +'").childNodes.length == 2) {'
                + '$("'+ $(result).parentNode.id +'").childNodes[1].style.display = "block";'
                + '$("'+ $(result).parentNode.id +'").childNodes[1].style.visibility = "visible";'
            + '}', 1250);
    }
    
}
