function initSafari(){

    var detect = navigator.userAgent.toLowerCase();

    if(checkIt('safari')) {
        alert('do not test with safari!');
//        var width = document.getElementById('domainSelect').style['width'];
        document.getElementById('loginFieldUser').value = Element.getStyle('domainSelect', 'width');
        document.getElementById('domainSelect').style.width = '96px'; 
    }

    function checkIt(string)
    {
        place = detect.indexOf(string) + 1;
        return place;
    }

}
