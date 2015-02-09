var mesour = !mesour ? {dataGrid: {}} : mesour;
mesour.dataGrid.jsVersion = '2.0.3';
mesour.dataGrid.live = function() {
    var grid = $('.data-grid');
    grid.find('.button-component > div').each(function(){
        var count = 0, i = 0;
        $(this).find('> a, > .dropdown').each(function(){
            count += $(this).outerWidth()+5;
            i++;
        });
        $(this).width(count+(12));
    });
    grid.find('.button-component').each(function(){
        var $this = $(this),
            td = $this.closest('td');
        if(td.is('*')) {
            td.width($this.width()+($this.find('a').length*4));
        }
    });
    grid.find('td.button-component').each(function(){
        var $this = $(this);
        $this.width($this.find('> div').width()+($this.find('a').length*4));
    });
};
$(document).on('ready', function() {
    $('.tree-grid').find('.grid-header').find('.separator:visible:last').hide();
    if(mesour.dataGrid.version) {
        if(mesour.dataGrid.version !== mesour.dataGrid.jsVersion) {
            alert('Please update Mesour DataGrid "grid.js" and "grid.css" from "vendor/mesour/datagrid/DataGrid/src". Require version '+mesour.dataGrid.version+'.')
        }
    }
    mesour.dataGrid.live();
});
$(window).ajaxComplete(mesour.dataGrid.live);
mesour.createGetUrl = function(name, component, parameterName, value) {
    var parameter = name + (component !== null ? ('-' + component) : '') + '-' + parameterName;
    var output = '';
    var addToOutput = function(val, key) {
        output += (output === '' ? '' : '&')+parameter+(!key?'':key)+'='+encodeURIComponent(val);
    };
    var walkRecursive = function(_val, _key) {
        for(var y in _val) {
            if(typeof _val[y] === 'object') {
                var _iterations = 0;
                for(var b in _val[y]) {
                    _iterations++;
                }
                if(_iterations === 0) {
                    addToOutput('', _key + '['+y+']');
                } else {
                    walkRecursive(_val[y], _key + '['+y+']');
                }

            } else {
                addToOutput(_val[y], _key + '['+y+']');
            }
        }
    };

    if(typeof value === 'object') {
        for(var x in value) {
            if(typeof value[x] === 'object') {
                var iterations = 0;
                for(var a in value[x]) {
                    iterations++;
                }
                if(iterations === 0) {
                    addToOutput('', '['+x+']');
                } else {
                    walkRecursive(value[x], '['+x+']');
                }
            } else {
                addToOutput(value[x], '['+x+']');
            }
        }
    } else {
        addToOutput(value);
    }
    return output;
};
mesour.getUrlWithParam = function(name, url, component, parameterName, value) {
    var character = '&';
    if(url.indexOf('?') === -1) {
        character = '?';
    }
    return url + character + this.createGetUrl(name, component, parameterName, value);
};
$(document).off('click.mesour-ajax');
$(document).on('click.mesour-ajax', '.mesour-ajax:not(form)', function(e) {
    e.preventDefault();
    $.get($(this).attr('href'));
});