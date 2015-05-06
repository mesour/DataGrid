/**
 * Mesour DataGrid - core.js
 *
 * @author Matous Nemec (mesour.com)
 */
var mesour = !mesour ? {dataGrid: {}} : mesour;
mesour.dataGrid = !mesour.dataGrid ? {} : mesour.dataGrid;
mesour.dataGrid.jsVersion = '2.1.0';

mesour.on = mesour.on ? mesour.on : {
    live: function(name, callback) {
        if(typeof name !== 'string') {
            throw Error('First argument must be a string.');
        }
        if(typeof callback !== 'function') {
            throw Error('Second argument must be callbable.');
        }
        this.live.arr[name] = callback;
    },
    ready: function(name, callback) {
        if(typeof name !== 'string') {
            throw Error('First argument must be a string.');
        }
        if(typeof callback !== 'function') {
            throw Error('Second argument must be callbable.');
        }
        this.ready.arr[name] = callback;
    }
};
mesour.on._apply = mesour.on._apply ? mesour.on._apply : function(obj) {
    obj.arr = obj.arr ? obj.arr : {};
    obj.fn = obj.fn ? obj.fn : function() {
        for(var i in obj.arr) {
            if(typeof obj.arr[i] === 'function') {
                obj.arr[i].call(window);
            }
        }
    };
};
mesour.on._apply(mesour.on.live);
mesour.on._apply(mesour.on.ready);

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

(function($) {
    $(document).off('ready.mesour-ready');
    $(document).off('ready.mesour-ajax');
    $(window).off('ajaxComplete.mesour-ajax');
    $(document).on('ready.mesour-ready', mesour.on.ready.fn);
    $(document).on('ready.mesour-ajax', mesour.on.live.fn);
    $(window).on('ajaxComplete.mesour-ajax', mesour.on.live.fn);

    $(document).off('click.mesour-ajax');
    $(document).on('click.mesour-ajax', '.mesour-ajax:not(form)', function(e) {
        e.preventDefault();
        $.get($(this).attr('href'));
    });
})(jQuery);

mesour.on.ready('grid-version', function() {
    if(mesour.dataGrid.version) {
        if(mesour.dataGrid.version !== mesour.dataGrid.jsVersion) {
            alert('Please update  "mesourGrid.js" and "mesourGrid.css" from "vendor/mesour/datagrid/DataGrid/public". Required version '+mesour.dataGrid.version+'.')
        }
    }
});