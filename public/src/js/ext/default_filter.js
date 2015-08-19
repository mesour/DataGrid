/**
 * Mesour DataGrid - ext/default_filter.js
 *
 * @author Matous Nemec (mesour.com)
 */
(function($){
    if (!Array.prototype.indexOf) {
        Array.prototype.indexOf = function(obj, start) {
            for (var i = (start || 0), j = this.length; i < j; i++) {
                if (this[i] === obj) { return i; }
            }
            return -1;
        };
    }
    var removeDiacritics = function (str) {

        var defaultDiacriticsRemovalMap = [
            {'base':'A', 'letters':/[\u0041\u24B6\uFF21\u00C0\u00C1\u00C2\u1EA6\u1EA4\u1EAA\u1EA8\u00C3\u0100\u0102\u1EB0\u1EAE\u1EB4\u1EB2\u0226\u01E0\u00C4\u01DE\u1EA2\u00C5\u01FA\u01CD\u0200\u0202\u1EA0\u1EAC\u1EB6\u1E00\u0104\u023A\u2C6F]/g},
            {'base':'AA','letters':/[\uA732]/g},
            {'base':'AE','letters':/[\u00C6\u01FC\u01E2]/g},
            {'base':'AO','letters':/[\uA734]/g},
            {'base':'AU','letters':/[\uA736]/g},
            {'base':'AV','letters':/[\uA738\uA73A]/g},
            {'base':'AY','letters':/[\uA73C]/g},
            {'base':'B', 'letters':/[\u0042\u24B7\uFF22\u1E02\u1E04\u1E06\u0243\u0182\u0181]/g},
            {'base':'C', 'letters':/[\u0043\u24B8\uFF23\u0106\u0108\u010A\u010C\u00C7\u1E08\u0187\u023B\uA73E]/g},
            {'base':'D', 'letters':/[\u0044\u24B9\uFF24\u1E0A\u010E\u1E0C\u1E10\u1E12\u1E0E\u0110\u018B\u018A\u0189\uA779]/g},
            {'base':'DZ','letters':/[\u01F1\u01C4]/g},
            {'base':'Dz','letters':/[\u01F2\u01C5]/g},
            {'base':'E', 'letters':/[\u0045\u24BA\uFF25\u00C8\u00C9\u00CA\u1EC0\u1EBE\u1EC4\u1EC2\u1EBC\u0112\u1E14\u1E16\u0114\u0116\u00CB\u1EBA\u011A\u0204\u0206\u1EB8\u1EC6\u0228\u1E1C\u0118\u1E18\u1E1A\u0190\u018E]/g},
            {'base':'F', 'letters':/[\u0046\u24BB\uFF26\u1E1E\u0191\uA77B]/g},
            {'base':'G', 'letters':/[\u0047\u24BC\uFF27\u01F4\u011C\u1E20\u011E\u0120\u01E6\u0122\u01E4\u0193\uA7A0\uA77D\uA77E]/g},
            {'base':'H', 'letters':/[\u0048\u24BD\uFF28\u0124\u1E22\u1E26\u021E\u1E24\u1E28\u1E2A\u0126\u2C67\u2C75\uA78D]/g},
            {'base':'I', 'letters':/[\u0049\u24BE\uFF29\u00CC\u00CD\u00CE\u0128\u012A\u012C\u0130\u00CF\u1E2E\u1EC8\u01CF\u0208\u020A\u1ECA\u012E\u1E2C\u0197]/g},
            {'base':'J', 'letters':/[\u004A\u24BF\uFF2A\u0134\u0248]/g},
            {'base':'K', 'letters':/[\u004B\u24C0\uFF2B\u1E30\u01E8\u1E32\u0136\u1E34\u0198\u2C69\uA740\uA742\uA744\uA7A2]/g},
            {'base':'L', 'letters':/[\u004C\u24C1\uFF2C\u013F\u0139\u013D\u1E36\u1E38\u013B\u1E3C\u1E3A\u0141\u023D\u2C62\u2C60\uA748\uA746\uA780]/g},
            {'base':'LJ','letters':/[\u01C7]/g},
            {'base':'Lj','letters':/[\u01C8]/g},
            {'base':'M', 'letters':/[\u004D\u24C2\uFF2D\u1E3E\u1E40\u1E42\u2C6E\u019C]/g},
            {'base':'N', 'letters':/[\u004E\u24C3\uFF2E\u01F8\u0143\u00D1\u1E44\u0147\u1E46\u0145\u1E4A\u1E48\u0220\u019D\uA790\uA7A4]/g},
            {'base':'NJ','letters':/[\u01CA]/g},
            {'base':'Nj','letters':/[\u01CB]/g},
            {'base':'O', 'letters':/[\u004F\u24C4\uFF2F\u00D2\u00D3\u00D4\u1ED2\u1ED0\u1ED6\u1ED4\u00D5\u1E4C\u022C\u1E4E\u014C\u1E50\u1E52\u014E\u022E\u0230\u00D6\u022A\u1ECE\u0150\u01D1\u020C\u020E\u01A0\u1EDC\u1EDA\u1EE0\u1EDE\u1EE2\u1ECC\u1ED8\u01EA\u01EC\u00D8\u01FE\u0186\u019F\uA74A\uA74C]/g},
            {'base':'OI','letters':/[\u01A2]/g},
            {'base':'OO','letters':/[\uA74E]/g},
            {'base':'OU','letters':/[\u0222]/g},
            {'base':'P', 'letters':/[\u0050\u24C5\uFF30\u1E54\u1E56\u01A4\u2C63\uA750\uA752\uA754]/g},
            {'base':'Q', 'letters':/[\u0051\u24C6\uFF31\uA756\uA758\u024A]/g},
            {'base':'R', 'letters':/[\u0052\u24C7\uFF32\u0154\u1E58\u0158\u0210\u0212\u1E5A\u1E5C\u0156\u1E5E\u024C\u2C64\uA75A\uA7A6\uA782]/g},
            {'base':'S', 'letters':/[\u0053\u24C8\uFF33\u1E9E\u015A\u1E64\u015C\u1E60\u0160\u1E66\u1E62\u1E68\u0218\u015E\u2C7E\uA7A8\uA784]/g},
            {'base':'T', 'letters':/[\u0054\u24C9\uFF34\u1E6A\u0164\u1E6C\u021A\u0162\u1E70\u1E6E\u0166\u01AC\u01AE\u023E\uA786]/g},
            {'base':'TZ','letters':/[\uA728]/g},
            {'base':'U', 'letters':/[\u0055\u24CA\uFF35\u00D9\u00DA\u00DB\u0168\u1E78\u016A\u1E7A\u016C\u00DC\u01DB\u01D7\u01D5\u01D9\u1EE6\u016E\u0170\u01D3\u0214\u0216\u01AF\u1EEA\u1EE8\u1EEE\u1EEC\u1EF0\u1EE4\u1E72\u0172\u1E76\u1E74\u0244]/g},
            {'base':'V', 'letters':/[\u0056\u24CB\uFF36\u1E7C\u1E7E\u01B2\uA75E\u0245]/g},
            {'base':'VY','letters':/[\uA760]/g},
            {'base':'W', 'letters':/[\u0057\u24CC\uFF37\u1E80\u1E82\u0174\u1E86\u1E84\u1E88\u2C72]/g},
            {'base':'X', 'letters':/[\u0058\u24CD\uFF38\u1E8A\u1E8C]/g},
            {'base':'Y', 'letters':/[\u0059\u24CE\uFF39\u1EF2\u00DD\u0176\u1EF8\u0232\u1E8E\u0178\u1EF6\u1EF4\u01B3\u024E\u1EFE]/g},
            {'base':'Z', 'letters':/[\u005A\u24CF\uFF3A\u0179\u1E90\u017B\u017D\u1E92\u1E94\u01B5\u0224\u2C7F\u2C6B\uA762]/g},
            {'base':'a', 'letters':/[\u0061\u24D0\uFF41\u1E9A\u00E0\u00E1\u00E2\u1EA7\u1EA5\u1EAB\u1EA9\u00E3\u0101\u0103\u1EB1\u1EAF\u1EB5\u1EB3\u0227\u01E1\u00E4\u01DF\u1EA3\u00E5\u01FB\u01CE\u0201\u0203\u1EA1\u1EAD\u1EB7\u1E01\u0105\u2C65\u0250]/g},
            {'base':'aa','letters':/[\uA733]/g},
            {'base':'ae','letters':/[\u00E6\u01FD\u01E3]/g},
            {'base':'ao','letters':/[\uA735]/g},
            {'base':'au','letters':/[\uA737]/g},
            {'base':'av','letters':/[\uA739\uA73B]/g},
            {'base':'ay','letters':/[\uA73D]/g},
            {'base':'b', 'letters':/[\u0062\u24D1\uFF42\u1E03\u1E05\u1E07\u0180\u0183\u0253]/g},
            {'base':'c', 'letters':/[\u0063\u24D2\uFF43\u0107\u0109\u010B\u010D\u00E7\u1E09\u0188\u023C\uA73F\u2184]/g},
            {'base':'d', 'letters':/[\u0064\u24D3\uFF44\u1E0B\u010F\u1E0D\u1E11\u1E13\u1E0F\u0111\u018C\u0256\u0257\uA77A]/g},
            {'base':'dz','letters':/[\u01F3\u01C6]/g},
            {'base':'e', 'letters':/[\u0065\u24D4\uFF45\u00E8\u00E9\u00EA\u1EC1\u1EBF\u1EC5\u1EC3\u1EBD\u0113\u1E15\u1E17\u0115\u0117\u00EB\u1EBB\u011B\u0205\u0207\u1EB9\u1EC7\u0229\u1E1D\u0119\u1E19\u1E1B\u0247\u025B\u01DD]/g},
            {'base':'f', 'letters':/[\u0066\u24D5\uFF46\u1E1F\u0192\uA77C]/g},
            {'base':'g', 'letters':/[\u0067\u24D6\uFF47\u01F5\u011D\u1E21\u011F\u0121\u01E7\u0123\u01E5\u0260\uA7A1\u1D79\uA77F]/g},
            {'base':'h', 'letters':/[\u0068\u24D7\uFF48\u0125\u1E23\u1E27\u021F\u1E25\u1E29\u1E2B\u1E96\u0127\u2C68\u2C76\u0265]/g},
            {'base':'hv','letters':/[\u0195]/g},
            {'base':'i', 'letters':/[\u0069\u24D8\uFF49\u00EC\u00ED\u00EE\u0129\u012B\u012D\u00EF\u1E2F\u1EC9\u01D0\u0209\u020B\u1ECB\u012F\u1E2D\u0268\u0131]/g},
            {'base':'j', 'letters':/[\u006A\u24D9\uFF4A\u0135\u01F0\u0249]/g},
            {'base':'k', 'letters':/[\u006B\u24DA\uFF4B\u1E31\u01E9\u1E33\u0137\u1E35\u0199\u2C6A\uA741\uA743\uA745\uA7A3]/g},
            {'base':'l', 'letters':/[\u006C\u24DB\uFF4C\u0140\u013A\u013E\u1E37\u1E39\u013C\u1E3D\u1E3B\u017F\u0142\u019A\u026B\u2C61\uA749\uA781\uA747]/g},
            {'base':'lj','letters':/[\u01C9]/g},
            {'base':'m', 'letters':/[\u006D\u24DC\uFF4D\u1E3F\u1E41\u1E43\u0271\u026F]/g},
            {'base':'n', 'letters':/[\u006E\u24DD\uFF4E\u01F9\u0144\u00F1\u1E45\u0148\u1E47\u0146\u1E4B\u1E49\u019E\u0272\u0149\uA791\uA7A5]/g},
            {'base':'nj','letters':/[\u01CC]/g},
            {'base':'o', 'letters':/[\u006F\u24DE\uFF4F\u00F2\u00F3\u00F4\u1ED3\u1ED1\u1ED7\u1ED5\u00F5\u1E4D\u022D\u1E4F\u014D\u1E51\u1E53\u014F\u022F\u0231\u00F6\u022B\u1ECF\u0151\u01D2\u020D\u020F\u01A1\u1EDD\u1EDB\u1EE1\u1EDF\u1EE3\u1ECD\u1ED9\u01EB\u01ED\u00F8\u01FF\u0254\uA74B\uA74D\u0275]/g},
            {'base':'oi','letters':/[\u01A3]/g},
            {'base':'ou','letters':/[\u0223]/g},
            {'base':'oo','letters':/[\uA74F]/g},
            {'base':'p','letters':/[\u0070\u24DF\uFF50\u1E55\u1E57\u01A5\u1D7D\uA751\uA753\uA755]/g},
            {'base':'q','letters':/[\u0071\u24E0\uFF51\u024B\uA757\uA759]/g},
            {'base':'r','letters':/[\u0072\u24E1\uFF52\u0155\u1E59\u0159\u0211\u0213\u1E5B\u1E5D\u0157\u1E5F\u024D\u027D\uA75B\uA7A7\uA783]/g},
            {'base':'s','letters':/[\u0073\u24E2\uFF53\u00DF\u015B\u1E65\u015D\u1E61\u0161\u1E67\u1E63\u1E69\u0219\u015F\u023F\uA7A9\uA785\u1E9B]/g},
            {'base':'t','letters':/[\u0074\u24E3\uFF54\u1E6B\u1E97\u0165\u1E6D\u021B\u0163\u1E71\u1E6F\u0167\u01AD\u0288\u2C66\uA787]/g},
            {'base':'tz','letters':/[\uA729]/g},
            {'base':'u','letters':/[\u0075\u24E4\uFF55\u00F9\u00FA\u00FB\u0169\u1E79\u016B\u1E7B\u016D\u00FC\u01DC\u01D8\u01D6\u01DA\u1EE7\u016F\u0171\u01D4\u0215\u0217\u01B0\u1EEB\u1EE9\u1EEF\u1EED\u1EF1\u1EE5\u1E73\u0173\u1E77\u1E75\u0289]/g},
            {'base':'v','letters':/[\u0076\u24E5\uFF56\u1E7D\u1E7F\u028B\uA75F\u028C]/g},
            {'base':'vy','letters':/[\uA761]/g},
            {'base':'w','letters':/[\u0077\u24E6\uFF57\u1E81\u1E83\u0175\u1E87\u1E85\u1E98\u1E89\u2C73]/g},
            {'base':'x','letters':/[\u0078\u24E7\uFF58\u1E8B\u1E8D]/g},
            {'base':'y','letters':/[\u0079\u24E8\uFF59\u1EF3\u00FD\u0177\u1EF9\u0233\u1E8F\u00FF\u1EF7\u1E99\u1EF5\u01B4\u024F\u1EFF]/g},
            {'base':'z','letters':/[\u007A\u24E9\uFF5A\u017A\u1E91\u017C\u017E\u1E93\u1E95\u01B6\u0225\u0240\u2C6C\uA763]/g}
        ];

        for(var i=0; i<defaultDiacriticsRemovalMap.length; i++) {
            str = str.replace(defaultDiacriticsRemovalMap[i].letters, defaultDiacriticsRemovalMap[i].base);
        }

        return str;

    };
    var phpDate = function(e,g){var h=this;var k,f;var l=['Sun','Mon','Tues','Wednes','Thurs','Fri','Satur','January','February','March','April','May','June','July','August','September','October','November','December'];var m=/\\?(.?)/gi;var o=function(t,s){return f[t]?f[t]():s};var p=function(n,c){n=String(n);while(n.length<c){n='0'+n}return n};f={d:function(){return p(f.j(),2)},D:function(){return f.l().slice(0,3)},j:function(){return k.getDate()},l:function(){return l[f.w()]+'day'},N:function(){return f.w()||7},S:function(){var j=f.j();var i=j%10;if(i<=3&&parseInt((j%100)/10,10)==1){i=0}return['st','nd','rd'][i-1]||'th'},w:function(){return k.getDay()},z:function(){var a=new Date(f.Y(),f.n()-1,f.j());var b=new Date(f.Y(),0,1);return Math.round((a-b)/864e5)},W:function(){var a=new Date(f.Y(),f.n()-1,f.j()-f.N()+3);var b=new Date(a.getFullYear(),0,4);return p(1+Math.round((a-b)/864e5/7),2)},F:function(){return l[6+f.n()]},m:function(){return p(f.n(),2)},M:function(){return f.F().slice(0,3)},n:function(){return k.getMonth()+1},t:function(){return(new Date(f.Y(),f.n(),0)).getDate()},L:function(){var j=f.Y();return j%4===0&j%100!==0|j%400===0},o:function(){var n=f.n();var W=f.W();var Y=f.Y();return Y+(n===12&&W<9?1:n===1&&W>9?-1:0)},Y:function(){return k.getFullYear()},y:function(){return f.Y().toString().slice(-2)},a:function(){return k.getHours()>11?'pm':'am'},A:function(){return f.a().toUpperCase()},B:function(){var H=k.getUTCHours()*36e2;var i=k.getUTCMinutes()*60;var s=k.getUTCSeconds();return p(Math.floor((H+i+s+36e2)/86.4)%1e3,3)},g:function(){return f.G()%12||12},G:function(){return k.getHours()},h:function(){return p(f.g(),2)},H:function(){return p(f.G(),2)},i:function(){return p(k.getMinutes(),2)},s:function(){return p(k.getSeconds(),2)},u:function(){return p(k.getMilliseconds()*1000,6)},e:function(){throw'Not supported (see source code of date() for timezone on how to add support)';},I:function(){var a=new Date(f.Y(),0);var c=Date.UTC(f.Y(),0);var b=new Date(f.Y(),6);var d=Date.UTC(f.Y(),6);return((a-c)!==(b-d))?1:0},O:function(){var b=k.getTimezoneOffset();var a=Math.abs(b);return(b>0?'-':'+')+p(Math.floor(a/60)*100+a%60,4)},P:function(){var O=f.O();return(O.substr(0,3)+':'+O.substr(3,2))},T:function(){return'UTC'},Z:function(){return-k.getTimezoneOffset()*60},c:function(){return'Y-m-d\\TH:i:sP'.replace(m,o)},r:function(){return'D, d M Y H:i:s O'.replace(m,o)},U:function(){return k/1000|0}};this.date=function(a,b){h=this;k=(b===undefined?new Date():(b instanceof Date)?new Date(b):new Date(b*1000));return a.replace(m,o)};return this.date(e,g)};
    var strtotime = function(e,f){var g,match,today,year,date,days,ranges,len,times,regex,i,fail=false;if(!e){return fail}e=e.replace(/^\s+|\s+$/g,'').replace(/\s{2,}/g,' ').replace(/[\t\r\n]/g,'').toLowerCase();match=e.match(/^(\d{1,4})([\-\.\/\:])(\d{1,2})([\-\.\/\:])(\d{1,4})(?:\s(\d{1,2}):(\d{2})?:?(\d{2})?)?(?:\s([A-Z]+)?)?$/);if(match&&match[2]===match[4]){if(match[1]>1901){switch(match[2]){case'-':{if(match[3]>12||match[5]>31){return fail}return new Date(match[1],parseInt(match[3],10)-1,match[5],match[6]||0,match[7]||0,match[8]||0,match[9]||0)/1000}case'.':{return fail}case'/':{if(match[3]>12||match[5]>31){return fail}return new Date(match[1],parseInt(match[3],10)-1,match[5],match[6]||0,match[7]||0,match[8]||0,match[9]||0)/1000}}}else if(match[5]>1901){switch(match[2]){case'-':{if(match[3]>12||match[1]>31){return fail}return new Date(match[5],parseInt(match[3],10)-1,match[1],match[6]||0,match[7]||0,match[8]||0,match[9]||0)/1000}case'.':{if(match[3]>12||match[1]>31){return fail}return new Date(match[5],parseInt(match[3],10)-1,match[1],match[6]||0,match[7]||0,match[8]||0,match[9]||0)/1000}case'/':{if(match[1]>12||match[3]>31){return fail}return new Date(match[5],parseInt(match[1],10)-1,match[3],match[6]||0,match[7]||0,match[8]||0,match[9]||0)/1000}}}else{switch(match[2]){case'-':{if(match[3]>12||match[5]>31||(match[1]<70&&match[1]>38)){return fail}year=match[1]>=0&&match[1]<=38?+match[1]+2000:match[1];return new Date(year,parseInt(match[3],10)-1,match[5],match[6]||0,match[7]||0,match[8]||0,match[9]||0)/1000}case'.':{if(match[5]>=70){if(match[3]>12||match[1]>31){return fail}return new Date(match[5],parseInt(match[3],10)-1,match[1],match[6]||0,match[7]||0,match[8]||0,match[9]||0)/1000}if(match[5]<60&&!match[6]){if(match[1]>23||match[3]>59){return fail}today=new Date();return new Date(today.getFullYear(),today.getMonth(),today.getDate(),match[1]||0,match[3]||0,match[5]||0,match[9]||0)/1000}return fail}case'/':{if(match[1]>12||match[3]>31||(match[5]<70&&match[5]>38)){return fail}year=match[5]>=0&&match[5]<=38?+match[5]+2000:match[5];return new Date(year,parseInt(match[1],10)-1,match[3],match[6]||0,match[7]||0,match[8]||0,match[9]||0)/1000}case':':{if(match[1]>23||match[3]>59||match[5]>59){return fail}today=new Date();return new Date(today.getFullYear(),today.getMonth(),today.getDate(),match[1]||0,match[3]||0,match[5]||0)/1000}}}}if(e==='now'){return f===null||isNaN(f)?new Date().getTime()/1000|0:f|0}if(!isNaN(g=Date.parse(e))){return g/1000|0}date=f?new Date(f*1000):new Date();days={'sun':0,'mon':1,'tue':2,'wed':3,'thu':4,'fri':5,'sat':6};ranges={'yea':'FullYear','mon':'Month','day':'Date','hou':'Hours','min':'Minutes','sec':'Seconds'};function lastNext(a,b,c){var d,day=days[b];if(typeof day!=='undefined'){d=day-date.getDay();if(d===0){d=7*c}else if(d>0&&a==='last'){d-=7}else if(d<0&&a==='next'){d+=7}date.setDate(date.getDate()+d)}}function process(a){var b=a.split(' '),type=b[0],range=b[1].substring(0,3),typeIsNumber=/\d+/.test(type),ago=b[2]==='ago',num=(type==='last'?-1:1)*(ago?-1:1);if(typeIsNumber){num*=parseInt(type,10)}if(ranges.hasOwnProperty(range)&&!b[1].match(/^mon(day|\.)?$/i)){return date['set'+ranges[range]](date['get'+ranges[range]]()+num)}if(range==='wee'){return date.setDate(date.getDate()+(num*7))}if(type==='next'||type==='last'){lastNext(type,range,num)}else if(!typeIsNumber){return false}return true}times='(years?|months?|weeks?|days?|hours?|minutes?|min|seconds?|sec'+'|sunday|sun\\.?|monday|mon\\.?|tuesday|tue\\.?|wednesday|wed\\.?'+'|thursday|thu\\.?|friday|fri\\.?|saturday|sat\\.?)';regex='([+-]?\\d+\\s'+times+'|'+'(last|next)\\s'+times+')(\\sago)?';match=e.match(new RegExp(regex,'gi'));if(!match){return fail}for(i=0,len=match.length;i<len;i++){if(!process(match[i])){return fail}}return(date.getTime()/1000)};
    var phpTime = function(){return Math.floor(new Date().getTime()/1000)};
    var filters = {};
    var CustomFilter = function (dropdown) {
        var $filter_modal = dropdown.getFilter().getFilterModal();
        var dateQuarter = function() {
            var thisMonth = Number(phpDate('n'));
            if (thisMonth <= 3) return 1;
            if (thisMonth <= 6) return 2;
            if (thisMonth <= 9) return 3;
            return 4;
        };
        var getStartTimestampForQuarter = function(quarter, year){
            year = !year ? phpDate('Y') : year;
            switch(quarter) {
                case 1:
                    return strtotime(year+'-01-01');
                case 2:
                    return strtotime(year+'-04-01');
                case 3:
                    return strtotime(year+'-07-01');
                default:
                    return strtotime(year+'-10-01');
            }
        };
        var getEndTimestampForQuarter = function(quarter, year){
            year = !year ? phpDate('Y') : year;
            switch(quarter) {
                case 1:
                    return strtotime(year+'-03-31');
                case 2:
                    return strtotime(year+'-06-30');
                case 3:
                    return strtotime(year+'-09-30');
                default:
                    return strtotime(year+'-12-31');
            }
        };
        var fixValue = function(value) {
            if(dropdown.getType() !== 'date') return value;
            var oneDay = 60 * 60 * 24;
            var phpDateFormat = dropdown.getFilter().getPhpDateFormat();
            switch(value) {
                case 'yesterday':
                    return [phpDate(phpDateFormat, phpTime() - oneDay)];
                case 'today':
                    return [phpDate(phpDateFormat)];
                case 'tommorow':
                    return [phpDate(phpDateFormat, phpTime() + oneDay)];
                case 'last_week_start':
                    return [phpDate(phpDateFormat, strtotime('Last Monday', strtotime('last week')) - oneDay)];
                case 'last_week_end':
                    return [phpDate(phpDateFormat, strtotime('Last Monday', strtotime('last week')) + 7 * oneDay)];
                case 'this_week_start':
                    var staticstart = phpTime();
                    if(phpDate('D')!='Mon')
                        staticstart = strtotime('last Monday');
                    return [phpDate(phpDateFormat, staticstart - oneDay)];
                case 'this_week_end':
                    var staticstart = phpTime();
                    if(phpDate('D')!='Mon')
                        staticstart = strtotime('last Monday');
                    return [phpDate(phpDateFormat, staticstart + 7 * oneDay)];
                case 'next_week_start':
                    return [phpDate(phpDateFormat, strtotime('Last Monday', strtotime('next week')) - oneDay)];
                case 'next_week_end':
                    return [phpDate(phpDateFormat, strtotime('Last Monday', strtotime('next week')) + 7 * oneDay)];
                case 'last_month_start':
                    return [phpDate(phpDateFormat, strtotime(phpDate('1-n-Y', strtotime('last month'))) - oneDay)];
                case 'last_month_end':
                    return [phpDate(phpDateFormat, strtotime(phpDate('t-n-Y', strtotime('last month'))) + oneDay)];
                case 'this_month_start':
                    return [phpDate(phpDateFormat, strtotime(phpDate('1-n-Y')) - oneDay)];
                case 'this_month_end':
                    return [phpDate(phpDateFormat, strtotime(phpDate('t-n-Y')) + oneDay)];
                case 'next_month_start':
                    return [phpDate(phpDateFormat, strtotime(phpDate('1-n-Y', strtotime('next month'))) - oneDay)];
                case 'next_month_end':
                    return [phpDate(phpDateFormat, strtotime(phpDate('t-n-Y', strtotime('next month'))) + oneDay)];
                case 'last_quarter_start':
                    var quarter = dateQuarter();
                    return [phpDate(phpDateFormat, getStartTimestampForQuarter(quarter - 1 < 1 ? 4 : quarter-1, quarter - 1 < 1 ? phpDate('Y', strtotime('last year')) : phpDate('Y')) - oneDay)];
                case 'last_quarter_end':
                    var quarter = dateQuarter();
                    return [phpDate(phpDateFormat, getEndTimestampForQuarter(quarter - 1 < 1 ? 4 : quarter-1, quarter - 1 < 1 ? phpDate('Y', strtotime('last year')) : phpDate('Y')) + oneDay)];
                case 'this_quarter_start':
                    return [phpDate(phpDateFormat, getStartTimestampForQuarter(dateQuarter()) - oneDay)];
                case 'this_quarter_end':
                    return [phpDate(phpDateFormat, getEndTimestampForQuarter(dateQuarter()) + oneDay)];
                case 'next_quarter_start':
                    var quarter = dateQuarter();
                    return [phpDate(phpDateFormat, getStartTimestampForQuarter(quarter + 1 > 4 ? 1 : quarter+1, quarter + 1 > 4 ? phpDate('Y', strtotime('next year')) : phpDate('Y')) - oneDay)];
                case 'next_quarter_end':
                    var quarter = dateQuarter();
                    return [phpDate(phpDateFormat, getEndTimestampForQuarter(quarter + 1 > 4 ? 1 : quarter+1, quarter + 1 > 4 ? phpDate('Y', strtotime('next year')) : phpDate('Y')) + oneDay)];
                case 'last_year_start':
                    return [phpDate(phpDateFormat, strtotime(phpDate('1-1-Y', strtotime('last year'))) - oneDay)];
                case 'last_year_end':
                    return [phpDate(phpDateFormat, strtotime(phpDate('31-12-Y', strtotime('last year'))) + oneDay)];
                case 'this_year_start':
                    return [phpDate(phpDateFormat, strtotime(phpDate('1-1-Y')) - oneDay)];
                case 'this_year_end':
                    return [phpDate(phpDateFormat, strtotime(phpDate('31-12-Y')) + oneDay)];
                case 'next_year_start':
                    return [phpDate(phpDateFormat, strtotime(phpDate('1-1-Y', strtotime('next year'))) - oneDay)];
                case 'next_year_end':
                    return [phpDate(phpDateFormat, strtotime(phpDate('31-12-Y', strtotime('next year'))) + oneDay)];
                default:
                    return value;
            }
        };
        dropdown.getElement().find('.open-modal').on('click', function(e) {
            e.preventDefault();
            var $this = $(this),
                values = dropdown.getValues('custom'),
                type1 = $this.attr('data-type-first'),
                type2 = $this.attr('data-type-second'),
                firstValue = $this.attr('data-first-value'),
                secondValue = $this.attr('data-second-value'),
                operator = $this.attr('data-operator');

            if($this.hasClass('edit-filter') && values) {
                type1 = values.how1;
                type2 = values.how2;
                operator = values.operator;
                firstValue = values.val1;
                secondValue = values.val2;
            }

            $filter_modal.find('[data-name]').val(dropdown.getName());

            if(firstValue) {
                var _val = fixValue(firstValue);
                if(typeof _val === 'string' && _val.split('-').length !== 3) {
                    $filter_modal.find('#grid-value-1').val(_val);
                    $filter_modal.find('#grid-value-1').removeAttr('data-date-defaultDate');
                } else {
                    if(typeof _val === 'string' && _val.split('-').length === 3) {
                        _val = [_val];
                    }
                    $filter_modal.find('#grid-value-1').val(_val[0]);
                    $filter_modal.find('#grid-value-1').attr('data-date-defaultDate', _val[0]);
                }
            } else {
                $filter_modal.find('#grid-value-1').val(null);
            }
            if(secondValue) {
                var _val = fixValue(secondValue);
                if(typeof _val === 'string') {
                    $filter_modal.find('#grid-value-2').val(_val);
                    $filter_modal.find('#grid-value-2').removeAttr('data-date-defaultDate');
                } else {
                    $filter_modal.find('#grid-value-2').val(_val[0]);
                    $filter_modal.find('#grid-value-2').attr('data-date-defaultDate', _val[0]);
                }
            } else {
                $filter_modal.find('#grid-value-2').val(null);
            }
            if(type1) {
                $filter_modal.find('#grid-how-1').val(type1);
            } else {
                $filter_modal.find('#grid-how-1').val(null);
            }
            if(type2) {
                $filter_modal.find('#grid-how-2').val(type2);
            } else {
                $filter_modal.find('#grid-how-2').val(null);
            }
            if(operator === 'or') {
                $filter_modal.find('input[name="operator"][value=or]').prop('checked', true);
            } else {
                $filter_modal.find('input[name="operator"][value=and]').prop('checked', true);
            }

            if(dropdown.getType() === 'date') {
                $filter_modal.find('.input-group-addon').show();
                $('#grid-datepicker1, #grid-datepicker2').data('DateTimePicker').destroy();
                $('#grid-datepicker1, #grid-datepicker2').bootstrapDatetimepicker({
                    pickTime: false
                });
                $filter_modal.find('#grid-value-1, #grid-value-2').on('keydown.data-grid', function(e){
                    e.preventDefault();
                    if(e.keyCode === 46 || e.keyCode === 8) {
                        $(this).val(null);
                    }
                });
            } else {
                $filter_modal.find('.input-group-addon').hide();
                $filter_modal.find('#grid-value-1, #grid-value-2').off('keydown.data-grid');
            }

            $('.grid-filter').fadeIn(function(){
                $filter_modal.find('#grid-value-1').focus();
            });
        });
    };
    var Checkers = function (dropdown) {
        var allCheckedCheckbox = dropdown.getElement().find('.select-all'),
            allSearchedCheckedCheckbox = dropdown.getElement().find('.select-all-searched'),
            checkers = dropdown.getElement().find('.inline-box ul .checker'),
            searchInput = dropdown.getElement().find('.search-input'),
            checkChecked = function (all_checkers, master_checker) {
                var allChecked = true;
                all_checkers.each(function () {
                    if (!$(this).is(':checked')) {
                        allChecked = false;
                    }
                });
                if (allChecked) {
                    master_checker.prop('checked', true)
                        .closest('li').addClass('li-checked');
                } else {
                    master_checker.prop('checked', false)
                        .closest('li').removeClass('li-checked');
                }
            },
            checkAllChecked = function (triggered) {
                if (allSearchedCheckedCheckbox.is(':visible')) {
                    checkChecked(checkers.filter(':visible'), allSearchedCheckedCheckbox);
                }
                checkChecked(checkers, allCheckedCheckbox);
                if(!triggered) {
                    dropdown.save();
                    dropdown.getFilter().apply();
                }
            },
            allCheckboxCallback = function (e) {
                var $this = $(this);
                var visible = !$this.hasClass('select-all-searched') ? '' : ':visible';
                if ($this.is(':checked')) {
                    $this.closest('li').addClass('li-checked')
                        .closest('ul').find('.checker' + visible).prop('checked', true)
                        .trigger('change', true);
                } else {
                    $this.closest('li').removeClass('li-checked')
                        .closest('ul').find('.checker' + visible).prop('checked', false)
                        .trigger('change', true);
                }
                dropdown.save();
                dropdown.getFilter().apply();
            },
            checkAllSubChecked = function ($checker) {
                var sub_ul = $checker.closest('.toggled-sub-ul');
                if (!sub_ul.is('*')) return;
                checkChecked(sub_ul.children('li').children('.checker'), sub_ul.closest('li').children('.checker'));
                var sub_sub_ul = sub_ul.closest('li').parent('ul').closest('li');
                if (!sub_sub_ul.is('*')) return;
                checkChecked(sub_sub_ul.children('ul').children('li').children('.checker'), sub_sub_ul.children('.checker'));
            };

        allCheckedCheckbox.off('change.data-grid');
        allCheckedCheckbox.on('change.data-grid', allCheckboxCallback);

        allSearchedCheckedCheckbox.off('change.data-grid');
        allSearchedCheckedCheckbox.on('change.data-grid', allCheckboxCallback);
        checkers.on('change', function (e, triggered) {
            var $this = $(this),
                li = $this.closest('li'),
                sub_ul = li.find('.toggled-sub-ul');

            if ($this.is(':checked')) {
                li.addClass('li-checked');
                if (sub_ul.is('*')) {
                    sub_ul.find('.checker').prop('checked', true)
                        .closest('li').addClass('li-checked');
                }
            } else {
                li.removeClass('li-checked');
                if (sub_ul.is('*')) {
                    sub_ul.find('.checker').prop('checked', false)
                        .closest('li').removeClass('li-checked');
                }
            }
            checkAllSubChecked($this);
            checkAllChecked(triggered);
        });
        checkers.next('label').each(function () {
            var $this = $(this);
            if ($this.text().length > 40) {
                $this.text($this.text().substr(0, 37) + '...');
            }
        });
        dropdown.getElement().find('.close-all a').on('click', function(e) {
            e.preventDefault();
            var $this = $(this);
            $this.closest('li').children('ul').find('ul').each(function() {
                var sub = $(this);
                sub.slideUp();
                sub.closest('li').find('.toggle-sub-ul').removeClass('glyphicon-minus').addClass('glyphicon-plus');
            });
        });
        dropdown.getElement().find('.toggle-sub-ul').on('click', function(e) {
            e.preventDefault();
            var $this = $(this),
                subselect = $this.closest('li').children('ul'),
                closeAll = $this.closest('li').children('.close-all');
            if(subselect.is(':visible')) {
                subselect.slideUp();
                closeAll.hide();
                $this.removeClass('glyphicon-minus').addClass('glyphicon-plus');
            } else {
                subselect.slideDown();
                closeAll.show();
                $this.removeClass('glyphicon-plus').addClass('glyphicon-minus');
            }
        });
        searchInput.on('keyup', function () {
            var $this = $(this),
                value = removeDiacritics($this.val().toLowerCase()),
                checkers = $this.closest('.search').next('.box-inner').find('.checker'),
                one_hide = false;

            allSearchedCheckedCheckbox.closest('li').hide();
            checkers.closest('li').show();
            checkers.closest('li').each(function () {
                var $li = $(this);
                if (removeDiacritics($li.text().toLowerCase()).indexOf(value) === -1) {
                    $li.hide();
                    one_hide = true;
                }
            });
            if (one_hide) {
                allSearchedCheckedCheckbox.closest('li').show();
            }
            checkAllChecked(true);
        });

        this.getChecked = function() {
            var values = [];
            checkers.filter('[data-value]').each(function(){
                var $this = $(this);
                if($this.is(':checked')) {
                    values.push($this.attr('data-value'))
                }
            });
            return values;
        };

        this.check = function(val) {
            checkers.filter('[data-value="'+val+'"]').prop('checked', true)
                .trigger('change', true);
        };
    };
    var applyDropDown = function(gridName, href, filterData) {
        if(filterData !== '') {
            var name = filterData.id;
            var opened = filterData.opened;
            mesour.cookie(gridName+'-'+name, opened);
        }
    };
    var Dropdown = function (element, name, filter) {
        var _this = this;

        var type = element.attr('data-type');

        var customFilter,
            checkers,
            mouseIn = false;

        var create = function(){};

        var destroy = function() {
            var ul = element.find('.box-inner').find('ul');
            ul.find('li:not(.all-select-li):not(.all-select-searched-li)').remove();
        };

        this.destroy = function() {
            destroy();
        };

        this.create = function(gridData, isAgain) {
            create(gridData, isAgain);
        };

        var apply = function(open) {
            applyDropDown(filter.getName(), filter.getDropDownLink(), open);
        };

        create = function(gridData, isAgain) {
            gridData = !gridData ? filter.getData() : gridData;
            if(!gridData) return;
            var values = {};
            for(var x = 0;x<gridData.length;x++) {
                if(!values[gridData[x][name]]) {
                    values[gridData[x][name]] = {
                        val: gridData[x][name],
                        keys: [x]
                    };
                } else {
                    values[gridData[x][name]].keys.push(x);
                }
            }

            if(!type) {
                var ul = element.find('.box-inner').find('ul');
                for(var y in values) {
                    if(!values[y].val && Number(values[y].val) !== 0) continue;

                    var li = $('<li>'),
                        id = name+(typeof values[y].val.replace === 'function' ? values[y].val.replace(' ', '') : values[y].val);
                    li.append('<input type="checkbox" class="checker" data-value="'+values[y].val+'" id="'+id+'">');
                    li.append('&nbsp;');
                    li.append('<label for="'+id+'">'+values[y].val+'</label>');
                    ul.append(li);
                }
            } else if(type === 'date') {
                var years = [],
                    months = {};
                for(var y in values) {
                    if(!values[y].val) continue;

                    var isTimestamp = isNaN(values[y].val);

                    var timestamp = isTimestamp ? strtotime(values[y].val) : values[y].val;
                    var year = phpDate('Y', timestamp);
                    var month = phpDate('n', timestamp);
                    var day = phpDate('j', timestamp);
                    if(years.indexOf(year) === -1) {
                        years.push(year)
                    }
                    if(!months[year]) {
                        months[year] = {};
                        months[year]['months'] = [];
                        months[year]['days'] = {};
                    }
                    if(months[year]['months'].indexOf(month) === -1) {
                        months[year]['months'].push(month);
                    }
                    if(!months[year]['days'][month]) {
                        months[year]['days'][month] = [];
                    }
                    if(months[year]['days'][month].indexOf(day) === -1) {
                        months[year]['days'][month].push(day);
                    }
                }
                years.sort(function(a, b){return b-a});
                var ul = element.find('.box-inner').find('ul');
                for(var a in years) {
                    var year_li = $('<li>');
                    year_li.append('<span class="glyphicon glyphicon-plus toggle-sub-ul"></span>');
                    year_li.append('&nbsp;');
                    year_li.append('<input type="checkbox" class="checker">');
                    year_li.append('&nbsp;');
                    year_li.append('<label>'+years[a]+'</label>');
                    year_li.append('<span class="close-all">(<a href="#">'+mesour.dataGrid.translates.closeAll+'</a>)</span>');
                    var month_ul = $('<ul class="toggled-sub-ul">');
                    year_li.append(month_ul);

                    months[years[a]].months.sort(function(a, b){return a-b});
                    var month = months[years[a]].months;
                    for(var b in month) {
                        var month_li = $('<li>');
                        month_li.append('<span class="glyphicon glyphicon-plus toggle-sub-ul"></span>');
                        month_li.append('&nbsp;');
                        month_li.append('<input type="checkbox" class="checker">');
                        month_li.append('&nbsp;');
                        month_li.append('<label>'+mesour.dataGrid.translates.months[month[b]]+'</label>');
                        month_ul.append(month_li);
                        var days_ul = $('<ul class="toggled-sub-ul">');
                        month_li.append(days_ul);

                        months[years[a]].days[month[b]].sort(function(a, b){return a-b});
                        var days = months[years[a]].days[month[b]];
                        for(var c in days) {
                            var this_time = strtotime(years[a]+'-'+month[b]+'-'+days[c]);
                            var date_text = isTimestamp ? phpDate(filter.getPhpDateFormat(), this_time) : this_time;
                            var day_li = $('<li>');
                            day_li.append('<span class="glyphicon">&nbsp;</span>');
                            day_li.append('<input type="checkbox" class="checker" data-value="'+date_text+'">');
                            day_li.append('&nbsp;');
                            day_li.append('<label>'+days[c]+'</label>');
                            days_ul.append(day_li);
                        }
                    }
                    ul.append(year_li);
                }
            }
            if(isAgain) {
                customFilter = new CustomFilter(_this);
                checkers = new Checkers(_this);
            }
        };

        create();

        this.getName = function () {
            return name;
        };

        this.getType = function () {
            return !type ? 'text' : type;
        };

        this.getElement = function () {
            return element;
        };

        this.getValues = function (valType) {
            var val = filter.getValues(name);
            if(!valType) {
                return val;
            } else {
                if(!val[valType]) {
                    return {};
                } else {
                    return val[valType];
                }
            }
        };

        this.setValues = function (newValues, valType) {
            var val = filter.getValues(name);
            val[valType] = newValues;
            filter.setValues(val, name);
        };

        this.unsetValues = function(valType) {
            var val = filter.getValues(name);
            delete val[valType];
            filter.setValues(val, name);
        };

        this.getFilter = function() {
            return filter;
        };

        customFilter = new CustomFilter(this);
        checkers = new Checkers(this);

        this.update = function () {
            var values = _this.getValues(),
                toggle_button = element.find('.dropdown-toggle'),
                menu = element.find('.dropdown-menu'),
                first_submenu = menu.children('.dropdown-submenu');
            toggle_button.find('.glyphicon-ok').hide();
            first_submenu.find('.glyphicon').closest('button').hide();

            if (values) {
                if (values.custom && values.custom.operator) {
                    toggle_button.find('.glyphicon-ok').show();
                    first_submenu.find('.glyphicon').closest('button').show();
                }
                if (values.checkers && values.checkers[0]) {
                    toggle_button.find('.glyphicon-ok').show();
                    for(var x = 0;x < values.checkers.length; x++) {
                        checkers.check(values.checkers[x]);
                    }
                }
            }
        };

        this.toggle = function() {
            if(_this.isOpened()) {
                _this.close();
            } else {
                _this.open();
            }
        };

        this.isOpened = function() {
            return element.hasClass('open');
        };

        this.open = function() {
            filter.closeAll(element);
            element.addClass('open');
            apply({
                id: _this.getName(),
                opened: 1
            });
        };

        this.close = function() {
            _this.update();
            element.removeClass('open');
            apply({
                id: _this.getName(),
                opened: 0
            });
        };

        element.on({
            mouseenter: function() {
                mouseIn = true;
            },
            mouseleave: function() {
                mouseIn = false;
            }
        });

        $('.grid-filter.modal-dialog').on({
            mouseenter: function() {
                mouseIn = true;
            },
            mouseleave: function() {
                mouseIn = false;
            }
        });

        $('html').on('click.filter-el-'+name, function() {
            if(_this.isOpened() && !mouseIn) {
                _this.close();
            }
        });

        element.children('button').on('click', function(e) {
            e.preventDefault();
            filter.closeAll(element);
            _this.toggle(element);
        });

        element.find('.reset-filter').on({
            click: function() {
                _this.unsetValues('custom');
                _this.update();
                _this.save();
                filter.apply();
            },
            mouseenter: function() {
                $(this).removeClass('btn-success').addClass('btn-danger');
            },
            mouseleave: function() {
                $(this).removeClass('btn-danger').addClass('btn-success');
            }
        });

        element.find('.close-filter').on('click', function(e) {
            e.preventDefault();
            _this.update();
            _this.close();
        });

        this.save = function() {
            var checked = checkers.getChecked();
            if(checked.length > 0) {
                _this.setValues(_this.getFilter().generateNextPriority(), 'priority');
                _this.setValues(checked, 'checkers');
                _this.setValues(type !== 'date' ? 'text' : 'date', 'type');
            } else {
                _this.unsetValues('priority');
                _this.unsetValues('checkers');
            }
            //_this.getFilter().filterCheckers();
            //_this.close();
        };

        this.update();
    };
    var applyFilter = function(gridName, href, filterData) {
        if(filterData !== '') {
            $.get(mesour.getUrlWithParam(gridName, href, 'filter', 'settings', $.parseJSON(filterData)))
                .complete(mesour.snippets.callback);
        }
    };
    var Filter = function (gridName, element) {
        var _this = this;

        var gridName = element.closest('.panel').attr('data-grid');
        var dropdowns = {};
        var valuesInput = element.find('[data-filter-values]');
        var modal = element.find('.grid-filter');
        var applyButton = element.find('.apply-filter');
        var dropDownLink = element.attr('data-dropdown-link');
        var resetButton = element.find('.full-reset');

        this.apply = function() {
            applyFilter(gridName, applyButton.attr("data-href"), valuesInput.val());
        };

        this.getDropdowns = function() {
            return dropdowns;
        };

        this.getName = function () {
            return gridName;
        };

        this.getDropDownLink = function() {
            return dropDownLink;
        };

        this.getFilterModal = function() {
            return modal;
        };

        this.closeAll = function(notThis) {
            for(var x in dropdowns) {
                dropdowns[x].update();
            }
            element.find('.dropdown').each(function() {
                if(!notThis || $(this)[0] !== notThis[0]) {
                    $(this).removeClass('open');
                    mesour.cookie(gridName+'-'+$(this).attr('data-filter'), 0);
                }
            });
        };

        this.getData = function () {
            return mesour.dataGrid.list[gridName].gridValues;
        };

        this.getPhpDateFormat = function () {
            return mesour.dataGrid.list[gridName].phpFilterDate;
        };

        this.getValues = function (name) {
            var val = valuesInput.val();
            val = val.length === 0 ? {} : $.parseJSON(val);
            if (!name) {
                return val;
            } else {
                if (!val[name]) {
                    return {};
                } else {
                    return val[name];
                }
            }
        };

        this.setValues = function(newValues, name) {
            var oldValues = valuesInput.val().length > 0 ? $.parseJSON(valuesInput.val()) : {};
            oldValues[name] = newValues;
            valuesInput.val(JSON.stringify(oldValues));
        };

        this.refreshPriorities = function() {
            var _currentValues = _this.getValues();
            var _usedPriorities = {};
            for(var x in _currentValues) {
                _usedPriorities[_currentValues[x].priority] = x;
            }
            var keys = [];

            for (var k in _usedPriorities) {
                if (_usedPriorities.hasOwnProperty(k)) {
                    keys.push(k);
                }
            }
            keys.sort();
            var priority = 1;
            for (var i = 0; i < keys.length; i++) {
                k = keys[i];
                if(_currentValues[_usedPriorities[k]].priority) {
                    _currentValues[_usedPriorities[k]].priority = priority;
                    priority++
                }
            }
            valuesInput.val(JSON.stringify(_currentValues));
        };

        this.generateNextPriority = function() {
            _this.refreshPriorities();
            var currentValues = _this.getValues();
            var usedPriorities = [];
            for(var x in currentValues) {
                usedPriorities.push(currentValues[x].priority);
            }
            if(usedPriorities.length > 0) {
                var nextPriority = 1;
                for(var y = 0; y < usedPriorities.length;y++) {
                    if(usedPriorities[y] > nextPriority) {
                        nextPriority = usedPriorities[y]+1;
                    } else if(usedPriorities[y] === nextPriority) {
                        nextPriority++;
                    }
                }
                return nextPriority;
            } else {
                return 1;
            }
        };

        this.filterData = function(key, valuesArr) {
            var data = _this.getData(),
                output = [];
            for(var x in data) {
                if(valuesArr.indexOf(data[x][key]) !== -1) {
                    output.push(data[x]);
                }
            }
            return output;
        };

        this.filterCheckers = function() {
            var currentValues = _this.getValues(),
                usedPriorities = {};

            for(var x in currentValues) {
                usedPriorities[currentValues[x].priority] = x;
            }
            var keys = [];
            for (var k in usedPriorities) {
                if (usedPriorities.hasOwnProperty(k)) {
                    keys.push(k);
                }
            }
            keys.sort();
            var usedDropdowns = {},
                newData = _this.getData();
            for (var i = 0; i < keys.length; i++) {
                k = keys[i];
                usedDropdowns[usedPriorities[k]] = true;
                dropdowns[usedPriorities[k]].destroy();
                dropdowns[usedPriorities[k]].create(newData, true);
                dropdowns[usedPriorities[k]].update();
                if(currentValues[usedPriorities[k]].checkers && currentValues[usedPriorities[k]].checkers.length > 0)
                    newData = _this.filterData(usedPriorities[k], currentValues[usedPriorities[k]].checkers);
            }
            for(var x in dropdowns) {
                var dropdown = dropdowns[x];
                if(usedDropdowns[dropdown.getName()]) continue;
                dropdowns[x].destroy();
                dropdowns[x].create(newData, true);
                dropdowns[x].update();
            }
        };

        resetButton.on('click', function(e) {
            e.preventDefault();
            $.each(_this.getDropdowns(), function(key, dropdown) {
                dropdown.unsetValues('custom');
                dropdown.unsetValues('priority');
                dropdown.unsetValues('checkers');
                dropdown.update();
                dropdown.getFilter().filterCheckers();
            });
            _this.apply();
        });

        element.find('[data-filter]').each(function () {
            var $this = $(this),
                name = $this.attr('data-filter');
            dropdowns[name] = new Dropdown($this, name, _this);
            $this.data('grid-filter-dropdown', dropdowns[name]);
        });

        _this.filterCheckers();

        modal.find('[aria-hidden="true"], [data-dismiss="modal"]').on('click.custom-filter', function(e) {
            e.preventDefault();
            $(this).closest('.modal-dialog').fadeOut();
        });

        $('#grid-datepicker1, #grid-datepicker2').bootstrapDatetimepicker({
            pickTime: false
        });

        modal.find('.save-custom-filter').on('click', function() {
            var name = modal.find('[data-name]').val(),
                internalValues = {
                    how1: modal.find('#grid-how-1').val(),
                    how2: modal.find('#grid-how-2').val(),
                    val1: modal.find('#grid-value-1').val(),
                    val2: modal.find('#grid-value-2').val(),
                    operator: modal.find('input[name="operator"]:checked').val()
                };
            if(internalValues.how1.length === 0) {
                alert('Please select some value in first select.');
                modal.find('#grid-how-1').focus();return;
            }
            if(internalValues.val1.length === 0) {
                alert('Please insert some value for first text input.');
                modal.find('#grid-value-1').focus();return;
            }
            if(internalValues.how2.length !== 0 && internalValues.val2.length === 0) {
                alert('Please insert some value for second input.');
                modal.find('#grid-value-2').focus();return;
            }
            dropdowns[name].setValues(internalValues, 'custom');
            dropdowns[name].setValues(dropdowns[name].getType() !== 'date' ? 'text' : 'date', 'type');
            $(this).closest('.modal-dialog').fadeOut();
            dropdowns[name].update();
            dropdowns[name].save();
            dropdowns[name].getFilter().apply();
        });
    };
    mesour.on.live('grid-filter-default', function() {
        $('.data-grid-filter').each(function () {
            var $this = $(this),
                name = $this.closest('.panel').attr('data-grid');
            var filter = $this.data('grid-filter');
            if(!filter) {
                filters[name] = filter = new Filter(name, $this);
                $this.data('grid-filter', filter);
            }
            $.each(filter.getDropdowns(), function(key,dropdown) {
                dropdown.destroy();
                dropdown.create();
                dropdown.update();
                dropdown.getFilter().filterCheckers();
                if(mesour.cookie(name+'-'+dropdown.getName()) === '1') {
                    dropdown.open();
                }
            });
        });
    });
})(jQuery);