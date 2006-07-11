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
            if (! /\d\d/.test(entry)){
                entry = '0' + entry.substr(0,1);
            }
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
            if (!entry.match(/\d\d\d/)){
                if(entry.match(/^\d\d/)){
                    instance.element.value = '19' + entry.substr(0,2);
                    entry = '19' + entry.substr(0,2);
                } else {
                    ret = false;
                    entry = '';
                }
            }
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
            if (! /\d\d\d\d/.test(entry)){
                    ret = false;
                    entry = '';
            }else{
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
        }
        if (entry.length == 5){
            if (entry.substr(4) == "'" || entry.substr(4) == "s"){
                entry = entry.substr(0,4) + "'s";
                instance.element.value = entry;
                return;
            }
            if (entry.match(/\d\d\d\d\d/)){
                entry = entry.substr(0,4) + "-" + entry.substr(4,1);
                instance.element.value = entry;
            }else{
            entry = entry.substr(0,4) + "-";
            instance.element.value = entry;
            ret.push("<li><strong>" + entry + "</strong>01</li>");
            ret.push("<li><strong>" + entry + "</strong>01-31</li>");
            return "<ul>" + ret.join('') + "</ul>";
            }
        }
        if (entry.length == 6){
            if(! entry.match(/^\d\d\d\d.\d/)) 
            {
                ret = false;
                entry = '';
            }else{
                if (entry.substr(4,1) != '-'){
                    entry = entry.substr(0,4) + "-" + entry.substr(6);
                    instance.element.value = entry;
                }
                switch(entry.substr(5)){
                    case '0':
                        for (var i = 1; i < 10; i++){
                            ret.push("<li><strong>" + entry.substr(0,4) + "-" + entry.substr(5) + "</strong>" + i + "</li>");
                        }
                }
            }
        }
        if (entry.length > 10){
            ret = false;
            entry = '';
        }

        if (ret === false){
        ret = [];
        ret.push("<strong>Please enter a date like:</strong>");
        ret.push("<li>1979-04-19</li>");
        ret.push("<li>1980's</li>");
        ret.push("<li>1966</li>");
        }
        return "<ul>" + ret.join('') + "</ul>";
      }
    }, options || {});
  }
});

ajaxRefCounter = 0;
ajaxRefTable = [];

Autocompleter.PearAjax = Class.create();
Autocompleter.PearAjax.prototype = Object.extend(Object.extend(Autocompleter.Base.prototype), {
  initialize: function(element, update, pearajax, mode, options) {
    this.baseInitialize(element, update, options);
    this.mode = mode;
    this.pearajax = pearajax;
  },

  getUpdatedChoices: function() {
    entry = encodeURIComponent(this.mode) + '=' +
      encodeURIComponent(this.getToken());

    var RefCounter = ajaxRefCounter++;
    ajaxRefTable[RefCounter] = this;

    eval(this.pearajax + "('"+ RefCounter +"','"+ entry +"');");
  },

  onComplete: function(list) {
    this.updateChoices(list);
  }

});
