// create a javascript hash to hold or callback methods
var authCallback = {
	
	getUserData: function(result){
        if (!result) {
            setLogin('login', '');
        } else {
            setLogin('logged', result);
        }
	},
    getDomains: function(result){
        setLogin('domains', result);
    },
    getSession: function(result){
        remoteAuth.getUserData();
    },
    dropSession: function(result){
        remoteAuth.getUserData();
    }
}
