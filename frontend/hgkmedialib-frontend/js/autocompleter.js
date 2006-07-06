Autocompleter.Date = Class.create();
Autocompleter.Date.prototype = Object.extend(new Autocompleter.Base(), {
  initialize: function(element, update, options) {
    this.baseInitialize(element, update, options);
  },

  getUpdatedChoices: function() {
    this.updateChoices(this.options.selector(this));
  },
  setOptions: function(options) {
    this.options = Object.extend({
      choices: 10,
      partialSearch: true,
      partialChars: 2,
      ignoreCase: true,
      fullSearch: false,
      selector: function(instance) {
        var ret       = []; // Beginning matches
        var entry     = instance.getToken();
       
        if(! entry.match(/^\d/)) { 
            ret = false;
            entry = '';
        } 
        if (entry.length == 1) {
            switch (entry)
            {
                case '0':
                    instance.element.value = '200';
                    entry = '200';
                    break;
                case '1':
                    instance.element.value = "19";
                    entry = '19';
                    break;
                case '2':
                    instance.element.value = '200';
                    entry = '200';
                    break;
                default:
                    instance.element.value = '19' + entry;
                    entry = '19' + entry;
            }
        } 
        if (entry.length == 2) {
            if (entry == "19" || entry == "20"){
                for (var i = 0; i < 10; i++){
                    ret.push("<li><strong>" + entry + "</strong>" + i + "0</li>");
                }
                return "<ul>" + ret.join('') + "</ul>";
            }else{
                    instance.element.value = '19' + entry;
                    entry = '19' + entry;
            }
        }
        if (entry.length == 3){
            switch (entry)
            {
                default:
                    ret.push("<li><strong>" + entry + "</strong>0's</li>");
                    for (var i = 0; i < 10; i++){
                        ret.push("<li><strong>" + entry + "</strong>" + i + "</li>");
                    }
                    return "<ul>" + ret.join('') + "</ul>";
            }
        }
        if (entry.length == 4){
            if (entry.substr(3) == '0')
                ret.push("<li><strong>" + entry + "</strong>'s</li>");
            switch (entry)
            {
                default:
                    ret.push("<li><strong>" + entry + "</strong></li>");
                    ret.push("<li><strong>" + entry + "</strong>-01</li>");
                    ret.push("<li><strong>" + entry + "</strong>-01-31</li>");
                    return "<ul>" + ret.join('') + "</ul>";
            }
        }
        if (entry.length == 5){
            if (entry.substr(4) == "'" || entry.substr(4) == "s"){
                entry = entry.substr(0,4) + "'s";
                instance.element.value = entry;
                return;
            }
            if (entry.match(/\d\d\d\d\d/)){
                entry = entry.substr(0,4) + "-" + entry.substr(4);
                instance.element.value = entry;
            }
            entry = entry.substr(0,4) + "-";
            instance.element.value = entry;
            ret.push("<li><strong>" + entry + "</strong>01</li>");
            ret.push("<li><strong>" + entry + "</strong>01-31</li>");
            return "<ul>" + ret.join('') + "</ul>";
        }
        if (entry.length == 6){
            if(! entry.match(/^\d\d\d\d.\d/)) ret = false;
            else{
                switch(entry.substr(5)){
                    case '0':
                        for (var i = 1; i < 10; i++){
                            ret.push("<li><strong>" + entry.substr(0,4) + "-" + entry.substr(5) + "</strong>" + i + "</li>");
                        }
                }
            }
        }

        if (ret === false){
        ret = [];
        ret.push("<li><strong>Please enter a date:</strong></li>");
        ret.push("<li>like:</li>");
        ret.push("<li>1979-04-19</li>");
        ret.push("<li>1980's</li>");
        ret.push("<li>1966</li>");
        }
        return "<ul>" + ret.join('') + "</ul>";
      }
    }, options || {});
  }
});

