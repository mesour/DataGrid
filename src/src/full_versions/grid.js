var mesour = !mesour ? {dataGrid: {}} : mesour;
mesour.dataGrid.jsVersion = '1.4.2';
$(document).on('ready', function() {
    $('.tree-grid').find('.grid-header').find('.separator:visible:last').hide();
    if(mesour.dataGrid.version) {
        if(mesour.dataGrid.version !== mesour.dataGrid.jsVersion) {
            alert('Please update Mesour DataGrid "grid.js" and "grid.css" from "vendor/mesour/datagrid/DataGrid/src".')
        }
    }
});
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