var mesour = !mesour ? {} : mesour;
mesour.createGetUrl = function(component, parameterName, value) {
    var parameter = gridName + (component !== null ? ('-' + component) : '') + '-' + parameterName;
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
mesour.getUrlWithParam = function(url, component, parameterName, value) {
    var character = '&';
    if(url.indexOf('?') === -1) {
        character = '?';
    }
    return url + character + this.createGetUrl(component, parameterName, value);
};