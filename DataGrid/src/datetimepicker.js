/*
 //! version : 3.1.3
 =========================================================
 bootstrap-datetimepicker.js
 https://github.com/Eonasdan/bootstrap-datetimepicker
 =========================================================
 The MIT License (MIT)

 Copyright (c) 2014 Jonathan Peterson
 */
;(function(a,b){'use strict';if(typeof define==='function'&&define.amd){define(['jquery','moment'],b)}else if(typeof exports==='object'){b(require('jquery'),require('moment'))}else{if(!jQuery){throw new Error('bootstrap-datetimepicker requires jQuery to be loaded first');}if(!moment){throw new Error('bootstrap-datetimepicker requires moment.js to be loaded first');}b(a.jQuery,moment)}}(this,function($,k){'use strict';if(typeof k==='undefined'){throw new Error('momentjs is required');}var l=0,DateTimePicker=function(f,g){var h=$.fn.bootstrapDatetimepicker.defaults,icons={time:'glyphicon glyphicon-time',date:'glyphicon glyphicon-calendar',up:'glyphicon glyphicon-chevron-up',down:'glyphicon glyphicon-chevron-down'},picker=this,errored=false,dDate,init=function(){var a=false,localeData,rInterval;picker.options=$.extend({},h,g);picker.options.icons=$.extend({},icons,picker.options.icons);picker.element=$(f);dataToOptions();if(!(picker.options.pickTime||picker.options.pickDate)){throw new Error('Must choose at least one picker');}picker.id=l++;k.locale(picker.options.language);picker.date=k();picker.unset=false;picker.isInput=picker.element.is('input');picker.component=false;if(picker.element.hasClass('input-group')){if(picker.element.find('.datepickerbutton').size()===0){picker.component=picker.element.find('[class^="input-group-"]')}else{picker.component=picker.element.find('.datepickerbutton')}}picker.format=picker.options.format;localeData=k().localeData();if(!picker.format){picker.format=(picker.options.pickDate?localeData.longDateFormat('L'):'');if(picker.options.pickDate&&picker.options.pickTime){picker.format+=' '}picker.format+=(picker.options.pickTime?localeData.longDateFormat('LT'):'');if(picker.options.useSeconds){if(localeData.longDateFormat('LT').indexOf(' A')!==-1){picker.format=picker.format.split(' A')[0]+':ss A'}else{picker.format+=':ss'}}}picker.use24hours=(picker.format.toLowerCase().indexOf('a')<0&&picker.format.indexOf('h')<0);if(picker.component){a=picker.component.find('span')}if(picker.options.pickTime){if(a){a.addClass(picker.options.icons.time)}}if(picker.options.pickDate){if(a){a.removeClass(picker.options.icons.time);a.addClass(picker.options.icons.date)}}picker.options.widgetParent=typeof picker.options.widgetParent==='string'&&picker.options.widgetParent||picker.element.parents().filter(function(){return'scroll'===$(this).css('overflow-y')}).get(0)||'body';picker.widget=$(getTemplate()).appendTo(picker.options.widgetParent);picker.minViewMode=picker.options.minViewMode||0;if(typeof picker.minViewMode==='string'){switch(picker.minViewMode){case'months':picker.minViewMode=1;break;case'years':picker.minViewMode=2;break;default:picker.minViewMode=0;break}}picker.viewMode=picker.options.viewMode||0;if(typeof picker.viewMode==='string'){switch(picker.viewMode){case'months':picker.viewMode=1;break;case'years':picker.viewMode=2;break;default:picker.viewMode=0;break}}picker.viewMode=Math.max(picker.viewMode,picker.minViewMode);picker.options.disabledDates=indexGivenDates(picker.options.disabledDates);picker.options.enabledDates=indexGivenDates(picker.options.enabledDates);picker.startViewMode=picker.viewMode;picker.setMinDate(picker.options.minDate);picker.setMaxDate(picker.options.maxDate);fillDow();fillMonths();fillHours();fillMinutes();fillSeconds();update();showMode();if(!getPickerInput().prop('disabled')){attachDatePickerEvents()}if(picker.options.defaultDate!==''&&getPickerInput().val()===''){picker.setValue(picker.options.defaultDate)}if(picker.options.minuteStepping!==1){rInterval=picker.options.minuteStepping;picker.date.minutes((Math.round(picker.date.minutes()/rInterval)*rInterval)%60).seconds(0)}},getPickerInput=function(){var a;if(picker.isInput){return picker.element}a=picker.element.find('.datepickerinput');if(a.size()===0){a=picker.element.find('input')}else if(!a.is('input')){throw new Error('CSS class "datepickerinput" cannot be applied to non input element');}return a},dataToOptions=function(){var a;if(picker.element.is('input')){a=picker.element.data()}else{a=picker.element.find('input').data()}if(a.dateFormat!==undefined){picker.options.format=a.dateFormat}if(a.datePickdate!==undefined){picker.options.pickDate=a.datePickdate}if(a.datePicktime!==undefined){picker.options.pickTime=a.datePicktime}if(a.dateUseminutes!==undefined){picker.options.useMinutes=a.dateUseminutes}if(a.dateUseseconds!==undefined){picker.options.useSeconds=a.dateUseseconds}if(a.dateUsecurrent!==undefined){picker.options.useCurrent=a.dateUsecurrent}if(a.calendarWeeks!==undefined){picker.options.calendarWeeks=a.calendarWeeks}if(a.dateMinutestepping!==undefined){picker.options.minuteStepping=a.dateMinutestepping}if(a.dateMindate!==undefined){picker.options.minDate=a.dateMindate}if(a.dateMaxdate!==undefined){picker.options.maxDate=a.dateMaxdate}if(a.dateShowtoday!==undefined){picker.options.showToday=a.dateShowtoday}if(a.dateCollapse!==undefined){picker.options.collapse=a.dateCollapse}if(a.dateLanguage!==undefined){picker.options.language=a.dateLanguage}if(a.dateDefaultdate!==undefined){picker.options.defaultDate=a.dateDefaultdate}if(a.dateDisableddates!==undefined){picker.options.disabledDates=a.dateDisableddates}if(a.dateEnableddates!==undefined){picker.options.enabledDates=a.dateEnableddates}if(a.dateIcons!==undefined){picker.options.icons=a.dateIcons}if(a.dateUsestrict!==undefined){picker.options.useStrict=a.dateUsestrict}if(a.dateDirection!==undefined){picker.options.direction=a.dateDirection}if(a.dateSidebyside!==undefined){picker.options.sideBySide=a.dateSidebyside}if(a.dateDaysofweekdisabled!==undefined){picker.options.daysOfWeekDisabled=a.dateDaysofweekdisabled}},place=function(){var a='absolute',offset=picker.component?picker.component.offset():picker.element.offset(),$window=$(window),placePosition;picker.width=picker.component?picker.component.outerWidth():picker.element.outerWidth();offset.top=offset.top+picker.element.outerHeight();if(picker.options.direction==='up'){placePosition='top'}else if(picker.options.direction==='bottom'){placePosition='bottom'}else if(picker.options.direction==='auto'){if(offset.top+picker.widget.height()>$window.height()+$window.scrollTop()&&picker.widget.height()+picker.element.outerHeight()<offset.top){placePosition='top'}else{placePosition='bottom'}}if(placePosition==='top'){offset.bottom=$window.height()-offset.top+picker.element.outerHeight()+3;picker.widget.addClass('top').removeClass('bottom')}else{offset.top+=1;picker.widget.addClass('bottom').removeClass('top')}if(picker.options.width!==undefined){picker.widget.width(picker.options.width)}if(picker.options.orientation==='left'){picker.widget.addClass('left-oriented');offset.left=offset.left-picker.widget.width()+20}if(isInFixed()){a='fixed';offset.top-=$window.scrollTop();offset.left-=$window.scrollLeft()}if($window.width()<offset.left+picker.widget.outerWidth()){offset.right=$window.width()-offset.left-picker.width;offset.left='auto';picker.widget.addClass('pull-right')}else{offset.right='auto';picker.widget.removeClass('pull-right')}if(placePosition==='top'){picker.widget.css({position:a,bottom:offset.bottom,top:'auto',left:offset.left,right:offset.right})}else{picker.widget.css({position:a,top:offset.top,bottom:'auto',left:offset.left,right:offset.right})}},notifyChange=function(a,b){if(k(picker.date).isSame(k(a))&&!errored){return}errored=false;picker.element.trigger({type:'dp.change',date:k(picker.date),oldDate:k(a)});if(b!=='change'){picker.element.change()}},notifyError=function(a){errored=true;picker.element.trigger({type:'dp.error',date:k(a,picker.format,picker.options.useStrict)})},update=function(a){k.locale(picker.options.language);var b=a;if(!b){b=getPickerInput().val();if(b){picker.date=k(b,picker.format,picker.options.useStrict)}if(!picker.date){picker.date=k()}}picker.viewDate=k(picker.date).startOf('month');fillDate();fillTime()},fillDow=function(){k.locale(picker.options.language);var a=$('<tr>'),weekdaysMin=k.weekdaysMin(),i;if(picker.options.calendarWeeks===true){a.append('<th class="cw">#</th>')}if(k().localeData()._week.dow===0){for(i=0;i<7;i++){a.append('<th class="dow">'+weekdaysMin[i]+'</th>')}}else{for(i=1;i<8;i++){if(i===7){a.append('<th class="dow">'+weekdaysMin[0]+'</th>')}else{a.append('<th class="dow">'+weekdaysMin[i]+'</th>')}}}picker.widget.find('.datepicker-days thead').append(a)},fillMonths=function(){k.locale(picker.options.language);var a='',i,monthsShort=k.monthsShort();for(i=0;i<12;i++){a+='<span class="month">'+monthsShort[i]+'</span>'}picker.widget.find('.datepicker-months td').append(a)},fillDate=function(){if(!picker.options.pickDate){return}k.locale(picker.options.language);var a=picker.viewDate.year(),month=picker.viewDate.month(),startYear=picker.options.minDate.year(),startMonth=picker.options.minDate.month(),endYear=picker.options.maxDate.year(),endMonth=picker.options.maxDate.month(),currentDate,prevMonth,nextMonth,html=[],row,clsName,i,days,yearCont,currentYear,months=k.months();picker.widget.find('.datepicker-days').find('.disabled').removeClass('disabled');picker.widget.find('.datepicker-months').find('.disabled').removeClass('disabled');picker.widget.find('.datepicker-years').find('.disabled').removeClass('disabled');picker.widget.find('.datepicker-days th:eq(1)').text(months[month]+' '+a);prevMonth=k(picker.viewDate,picker.format,picker.options.useStrict).subtract(1,'months');days=prevMonth.daysInMonth();prevMonth.date(days).startOf('week');if((a===startYear&&month<=startMonth)||a<startYear){picker.widget.find('.datepicker-days th:eq(0)').addClass('disabled')}if((a===endYear&&month>=endMonth)||a>endYear){picker.widget.find('.datepicker-days th:eq(2)').addClass('disabled')}nextMonth=k(prevMonth).add(42,'d');while(prevMonth.isBefore(nextMonth)){if(prevMonth.weekday()===k().startOf('week').weekday()){row=$('<tr>');html.push(row);if(picker.options.calendarWeeks===true){row.append('<td class="cw">'+prevMonth.week()+'</td>')}}clsName='';if(prevMonth.year()<a||(prevMonth.year()===a&&prevMonth.month()<month)){clsName+=' old'}else if(prevMonth.year()>a||(prevMonth.year()===a&&prevMonth.month()>month)){clsName+=' new'}if(prevMonth.isSame(k({y:picker.date.year(),M:picker.date.month(),d:picker.date.date()}))){clsName+=' active'}if(isInDisableDates(prevMonth,'day')||!isInEnableDates(prevMonth)){clsName+=' disabled'}if(picker.options.showToday===true){if(prevMonth.isSame(k(),'day')){clsName+=' today'}}if(picker.options.daysOfWeekDisabled){for(i=0;i<picker.options.daysOfWeekDisabled.length;i++){if(prevMonth.day()===picker.options.daysOfWeekDisabled[i]){clsName+=' disabled';break}}}row.append('<td class="day'+clsName+'">'+prevMonth.date()+'</td>');currentDate=prevMonth.date();prevMonth.add(1,'d');if(currentDate===prevMonth.date()){prevMonth.add(1,'d')}}picker.widget.find('.datepicker-days tbody').empty().append(html);currentYear=picker.date.year();months=picker.widget.find('.datepicker-months').find('th:eq(1)').text(a).end().find('span').removeClass('active');if(currentYear===a){months.eq(picker.date.month()).addClass('active')}if(a-1<startYear){picker.widget.find('.datepicker-months th:eq(0)').addClass('disabled')}if(a+1>endYear){picker.widget.find('.datepicker-months th:eq(2)').addClass('disabled')}for(i=0;i<12;i++){if((a===startYear&&startMonth>i)||(a<startYear)){$(months[i]).addClass('disabled')}else if((a===endYear&&endMonth<i)||(a>endYear)){$(months[i]).addClass('disabled')}}html='';a=parseInt(a/10,10)*10;yearCont=picker.widget.find('.datepicker-years').find('th:eq(1)').text(a+'-'+(a+9)).parents('table').find('td');picker.widget.find('.datepicker-years').find('th').removeClass('disabled');if(startYear>a){picker.widget.find('.datepicker-years').find('th:eq(0)').addClass('disabled')}if(endYear<a+9){picker.widget.find('.datepicker-years').find('th:eq(2)').addClass('disabled')}a-=1;for(i=-1;i<11;i++){html+='<span class="year'+(i===-1||i===10?' old':'')+(currentYear===a?' active':'')+((a<startYear||a>endYear)?' disabled':'')+'">'+a+'</span>';a+=1}yearCont.html(html)},fillHours=function(){k.locale(picker.options.language);var a=picker.widget.find('.timepicker .timepicker-hours table'),html='',current,i,j;a.parent().hide();if(picker.use24hours){current=0;for(i=0;i<6;i+=1){html+='<tr>';for(j=0;j<4;j+=1){html+='<td class="hour">'+padLeft(current.toString())+'</td>';current++}html+='</tr>'}}else{current=1;for(i=0;i<3;i+=1){html+='<tr>';for(j=0;j<4;j+=1){html+='<td class="hour">'+padLeft(current.toString())+'</td>';current++}html+='</tr>'}}a.html(html)},fillMinutes=function(){var a=picker.widget.find('.timepicker .timepicker-minutes table'),html='',current=0,i,j,step=picker.options.minuteStepping;a.parent().hide();if(step===1){step=5}for(i=0;i<Math.ceil(60/step/4);i++){html+='<tr>';for(j=0;j<4;j+=1){if(current<60){html+='<td class="minute">'+padLeft(current.toString())+'</td>';current+=step}else{html+='<td></td>'}}html+='</tr>'}a.html(html)},fillSeconds=function(){var a=picker.widget.find('.timepicker .timepicker-seconds table'),html='',current=0,i,j;a.parent().hide();for(i=0;i<3;i++){html+='<tr>';for(j=0;j<4;j+=1){html+='<td class="second">'+padLeft(current.toString())+'</td>';current+=5}html+='</tr>'}a.html(html)},fillTime=function(){if(!picker.date){return}var a=picker.widget.find('.timepicker span[data-time-component]'),hour=picker.date.hours(),period=picker.date.format('A');if(!picker.use24hours){if(hour===0){hour=12}else if(hour!==12){hour=hour%12}picker.widget.find('.timepicker [data-action=togglePeriod]').text(period)}a.filter('[data-time-component=hours]').text(padLeft(hour));a.filter('[data-time-component=minutes]').text(padLeft(picker.date.minutes()));a.filter('[data-time-component=seconds]').text(padLeft(picker.date.second()))},click=function(e){e.stopPropagation();e.preventDefault();picker.unset=false;var a=$(e.target).closest('span, td, th'),month,year,step,day,oldDate=k(picker.date);if(a.length===1){if(!a.is('.disabled')){switch(a[0].nodeName.toLowerCase()){case'th':switch(a[0].className){case'picker-switch':showMode(1);break;case'prev':case'next':step=dpGlobal.modes[picker.viewMode].navStep;if(a[0].className==='prev'){step=step*-1}picker.viewDate.add(step,dpGlobal.modes[picker.viewMode].navFnc);fillDate();break}break;case'span':if(a.is('.month')){month=a.parent().find('span').index(a);picker.viewDate.month(month)}else{year=parseInt(a.text(),10)||0;picker.viewDate.year(year)}if(picker.viewMode===picker.minViewMode){picker.date=k({y:picker.viewDate.year(),M:picker.viewDate.month(),d:picker.viewDate.date(),h:picker.date.hours(),m:picker.date.minutes(),s:picker.date.seconds()});set();notifyChange(oldDate,e.type)}showMode(-1);fillDate();break;case'td':if(a.is('.day')){day=parseInt(a.text(),10)||1;month=picker.viewDate.month();year=picker.viewDate.year();if(a.is('.old')){if(month===0){month=11;year-=1}else{month-=1}}else if(a.is('.new')){if(month===11){month=0;year+=1}else{month+=1}}picker.date=k({y:year,M:month,d:day,h:picker.date.hours(),m:picker.date.minutes(),s:picker.date.seconds()});picker.viewDate=k({y:year,M:month,d:Math.min(28,day)});fillDate();set();notifyChange(oldDate,e.type)}break}}}},actions={incrementHours:function(){checkDate('add','hours',1)},incrementMinutes:function(){checkDate('add','minutes',picker.options.minuteStepping)},incrementSeconds:function(){checkDate('add','seconds',1)},decrementHours:function(){checkDate('subtract','hours',1)},decrementMinutes:function(){checkDate('subtract','minutes',picker.options.minuteStepping)},decrementSeconds:function(){checkDate('subtract','seconds',1)},togglePeriod:function(){var a=picker.date.hours();if(a>=12){a-=12}else{a+=12}picker.date.hours(a)},showPicker:function(){picker.widget.find('.timepicker > div:not(.timepicker-picker)').hide();picker.widget.find('.timepicker .timepicker-picker').show()},showHours:function(){picker.widget.find('.timepicker .timepicker-picker').hide();picker.widget.find('.timepicker .timepicker-hours').show()},showMinutes:function(){picker.widget.find('.timepicker .timepicker-picker').hide();picker.widget.find('.timepicker .timepicker-minutes').show()},showSeconds:function(){picker.widget.find('.timepicker .timepicker-picker').hide();picker.widget.find('.timepicker .timepicker-seconds').show()},selectHour:function(e){var a=parseInt($(e.target).text(),10);if(!picker.use24hours){if(picker.date.hours()>=12){if(a!==12){a+=12}}else{if(a===12){a=0}}}picker.date.hours(a);actions.showPicker.call(picker)},selectMinute:function(e){picker.date.minutes(parseInt($(e.target).text(),10));actions.showPicker.call(picker)},selectSecond:function(e){picker.date.seconds(parseInt($(e.target).text(),10));actions.showPicker.call(picker)}},doAction=function(e){var a=k(picker.date),action=$(e.currentTarget).data('action'),rv=actions[action].apply(picker,arguments);stopEvent(e);if(!picker.date){picker.date=k({y:1970})}set();fillTime();notifyChange(a,e.type);return rv},stopEvent=function(e){e.stopPropagation();e.preventDefault()},keydown=function(e){if(e.keyCode===27){picker.hide()}},change=function(e){k.locale(picker.options.language);var a=$(e.target),oldDate=k(picker.date),newDate=k(a.val(),picker.format,picker.options.useStrict);if(newDate.isValid()&&!isInDisableDates(newDate)&&isInEnableDates(newDate)){update();picker.setValue(newDate);notifyChange(oldDate,e.type);set()}else{picker.viewDate=oldDate;picker.unset=true;notifyChange(oldDate,e.type);notifyError(newDate)}},showMode=function(a){if(a){picker.viewMode=Math.max(picker.minViewMode,Math.min(2,picker.viewMode+a))}picker.widget.find('.datepicker > div').hide().filter('.datepicker-'+dpGlobal.modes[picker.viewMode].clsName).show()},attachDatePickerEvents=function(){var a,$parent,expanded,closed,collapseData;picker.widget.on('click','.datepicker *',$.proxy(click,this));picker.widget.on('click','[data-action]',$.proxy(doAction,this));picker.widget.on('mousedown',$.proxy(stopEvent,this));picker.element.on('keydown',$.proxy(keydown,this));if(picker.options.pickDate&&picker.options.pickTime){picker.widget.on('click.togglePicker','.accordion-toggle',function(e){e.stopPropagation();a=$(this);$parent=a.closest('ul');expanded=$parent.find('.in');closed=$parent.find('.collapse:not(.in)');if(expanded&&expanded.length){collapseData=expanded.data('collapse');if(collapseData&&collapseData.transitioning){return}expanded.collapse('hide');closed.collapse('show');a.find('span').toggleClass(picker.options.icons.time+' '+picker.options.icons.date);if(picker.component){picker.component.find('span').toggleClass(picker.options.icons.time+' '+picker.options.icons.date)}}})}if(picker.isInput){picker.element.on({'click':$.proxy(picker.show,this),'focus':$.proxy(picker.show,this),'change':$.proxy(change,this),'blur':$.proxy(picker.hide,this)})}else{picker.element.on({'change':$.proxy(change,this)},'input');if(picker.component){picker.component.on('click',$.proxy(picker.show,this));picker.component.on('mousedown',$.proxy(stopEvent,this))}else{picker.element.on('click',$.proxy(picker.show,this))}}},attachDatePickerGlobalEvents=function(){$(window).on('resize.datetimepicker'+picker.id,$.proxy(place,this));if(!picker.isInput){$(document).on('mousedown.datetimepicker'+picker.id,$.proxy(picker.hide,this))}},detachDatePickerEvents=function(){picker.widget.off('click','.datepicker *',picker.click);picker.widget.off('click','[data-action]');picker.widget.off('mousedown',picker.stopEvent);if(picker.options.pickDate&&picker.options.pickTime){picker.widget.off('click.togglePicker')}if(picker.isInput){picker.element.off({'focus':picker.show,'change':change,'click':picker.show,'blur':picker.hide})}else{picker.element.off({'change':change},'input');if(picker.component){picker.component.off('click',picker.show);picker.component.off('mousedown',picker.stopEvent)}else{picker.element.off('click',picker.show)}}},detachDatePickerGlobalEvents=function(){$(window).off('resize.datetimepicker'+picker.id);if(!picker.isInput){$(document).off('mousedown.datetimepicker'+picker.id)}},isInFixed=function(){if(picker.element){var a=picker.element.parents(),inFixed=false,i;for(i=0;i<a.length;i++){if($(a[i]).css('position')==='fixed'){inFixed=true;break}}return inFixed}else{return false}},set=function(){k.locale(picker.options.language);var a='';if(!picker.unset){a=k(picker.date).format(picker.format)}getPickerInput().val(a);picker.element.data('date',a);if(!picker.options.pickTime){picker.hide()}},checkDate=function(a,b,c){k.locale(picker.options.language);var d;if(a==='add'){d=k(picker.date);if(d.hours()===23){d.add(c,b)}d.add(c,b)}else{d=k(picker.date).subtract(c,b)}if(isInDisableDates(k(d.subtract(c,b)))||isInDisableDates(d)){notifyError(d.format(picker.format));return}if(a==='add'){picker.date.add(c,b)}else{picker.date.subtract(c,b)}picker.unset=false},isInDisableDates=function(a,b){k.locale(picker.options.language);var c=k(picker.options.maxDate,picker.format,picker.options.useStrict),minDate=k(picker.options.minDate,picker.format,picker.options.useStrict);if(b){c=c.endOf(b);minDate=minDate.startOf(b)}if(a.isAfter(c)||a.isBefore(minDate)){return true}if(picker.options.disabledDates===false){return false}return picker.options.disabledDates[a.format('YYYY-MM-DD')]===true},isInEnableDates=function(a){k.locale(picker.options.language);if(picker.options.enabledDates===false){return true}return picker.options.enabledDates[a.format('YYYY-MM-DD')]===true},indexGivenDates=function(a){var b={},givenDatesCount=0,i;for(i=0;i<a.length;i++){if(k.isMoment(a[i])||a[i]instanceof Date){dDate=k(a[i])}else{dDate=k(a[i],picker.format,picker.options.useStrict)}if(dDate.isValid()){b[dDate.format('YYYY-MM-DD')]=true;givenDatesCount++}}if(givenDatesCount>0){return b}return false},padLeft=function(a){a=a.toString();if(a.length>=2){return a}return'0'+a},getTemplate=function(){var a='<thead>'+'<tr>'+'<th class="prev">&lsaquo;</th><th colspan="'+(picker.options.calendarWeeks?'6':'5')+'" class="picker-switch"></th><th class="next">&rsaquo;</th>'+'</tr>'+'</thead>',contTemplate='<tbody><tr><td colspan="'+(picker.options.calendarWeeks?'8':'7')+'"></td></tr></tbody>',template='<div class="datepicker-days">'+'<table class="table-condensed">'+a+'<tbody></tbody></table>'+'</div>'+'<div class="datepicker-months">'+'<table class="table-condensed">'+a+contTemplate+'</table>'+'</div>'+'<div class="datepicker-years">'+'<table class="table-condensed">'+a+contTemplate+'</table>'+'</div>',ret='';if(picker.options.pickDate&&picker.options.pickTime){ret='<div class="bootstrap-datetimepicker-widget'+(picker.options.sideBySide?' timepicker-sbs':'')+(picker.use24hours?' usetwentyfour':'')+' dropdown-menu" style="z-index:9999 !important;">';if(picker.options.sideBySide){ret+='<div class="row">'+'<div class="col-sm-6 datepicker">'+template+'</div>'+'<div class="col-sm-6 timepicker">'+tpGlobal.getTemplate()+'</div>'+'</div>'}else{ret+='<ul class="list-unstyled">'+'<li'+(picker.options.collapse?' class="collapse in"':'')+'>'+'<div class="datepicker">'+template+'</div>'+'</li>'+'<li class="picker-switch accordion-toggle"><a class="btn" style="width:100%"><span class="'+picker.options.icons.time+'"></span></a></li>'+'<li'+(picker.options.collapse?' class="collapse"':'')+'>'+'<div class="timepicker">'+tpGlobal.getTemplate()+'</div>'+'</li>'+'</ul>'}ret+='</div>';return ret}if(picker.options.pickTime){return('<div class="bootstrap-datetimepicker-widget dropdown-menu">'+'<div class="timepicker">'+tpGlobal.getTemplate()+'</div>'+'</div>')}return('<div class="bootstrap-datetimepicker-widget dropdown-menu">'+'<div class="datepicker">'+template+'</div>'+'</div>')},dpGlobal={modes:[{clsName:'days',navFnc:'month',navStep:1},{clsName:'months',navFnc:'year',navStep:1},{clsName:'years',navFnc:'year',navStep:10}]},tpGlobal={hourTemplate:'<span data-action="showHours"   data-time-component="hours"   class="timepicker-hour"></span>',minuteTemplate:'<span data-action="showMinutes" data-time-component="minutes" class="timepicker-minute"></span>',secondTemplate:'<span data-action="showSeconds"  data-time-component="seconds" class="timepicker-second"></span>'};tpGlobal.getTemplate=function(){return('<div class="timepicker-picker">'+'<table class="table-condensed">'+'<tr>'+'<td><a href="#" class="btn" data-action="incrementHours"><span class="'+picker.options.icons.up+'"></span></a></td>'+'<td class="separator"></td>'+'<td>'+(picker.options.useMinutes?'<a href="#" class="btn" data-action="incrementMinutes"><span class="'+picker.options.icons.up+'"></span></a>':'')+'</td>'+(picker.options.useSeconds?'<td class="separator"></td><td><a href="#" class="btn" data-action="incrementSeconds"><span class="'+picker.options.icons.up+'"></span></a></td>':'')+(picker.use24hours?'':'<td class="separator"></td>')+'</tr>'+'<tr>'+'<td>'+tpGlobal.hourTemplate+'</td> '+'<td class="separator">:</td>'+'<td>'+(picker.options.useMinutes?tpGlobal.minuteTemplate:'<span class="timepicker-minute">00</span>')+'</td> '+(picker.options.useSeconds?'<td class="separator">:</td><td>'+tpGlobal.secondTemplate+'</td>':'')+(picker.use24hours?'':'<td class="separator"></td>'+'<td><button type="button" class="btn btn-primary" data-action="togglePeriod"></button></td>')+'</tr>'+'<tr>'+'<td><a href="#" class="btn" data-action="decrementHours"><span class="'+picker.options.icons.down+'"></span></a></td>'+'<td class="separator"></td>'+'<td>'+(picker.options.useMinutes?'<a href="#" class="btn" data-action="decrementMinutes"><span class="'+picker.options.icons.down+'"></span></a>':'')+'</td>'+(picker.options.useSeconds?'<td class="separator"></td><td><a href="#" class="btn" data-action="decrementSeconds"><span class="'+picker.options.icons.down+'"></span></a></td>':'')+(picker.use24hours?'':'<td class="separator"></td>')+'</tr>'+'</table>'+'</div>'+'<div class="timepicker-hours" data-action="selectHour">'+'<table class="table-condensed"></table>'+'</div>'+'<div class="timepicker-minutes" data-action="selectMinute">'+'<table class="table-condensed"></table>'+'</div>'+(picker.options.useSeconds?'<div class="timepicker-seconds" data-action="selectSecond"><table class="table-condensed"></table></div>':''))};picker.destroy=function(){detachDatePickerEvents();detachDatePickerGlobalEvents();picker.widget.remove();picker.element.removeData('DateTimePicker');if(picker.component){picker.component.removeData('DateTimePicker')}};picker.show=function(e){if(getPickerInput().prop('disabled')){return}if(picker.options.useCurrent){if(getPickerInput().val()===''){if(picker.options.minuteStepping!==1){var a=k(),rInterval=picker.options.minuteStepping;a.minutes((Math.round(a.minutes()/rInterval)*rInterval)%60).seconds(0);picker.setValue(a.format(picker.format))}else{picker.setValue(k().format(picker.format))}notifyChange('',e.type)}}if(e&&e.type==='click'&&picker.isInput&&picker.widget.hasClass('picker-open')){return}if(picker.widget.hasClass('picker-open')){picker.widget.hide();picker.widget.removeClass('picker-open')}else{picker.widget.show();picker.widget.addClass('picker-open')}picker.height=picker.component?picker.component.outerHeight():picker.element.outerHeight();place();picker.element.trigger({type:'dp.show',date:k(picker.date)});attachDatePickerGlobalEvents();if(e){stopEvent(e)}};picker.disable=function(){var a=getPickerInput();if(a.prop('disabled')){return}a.prop('disabled',true);detachDatePickerEvents()};picker.enable=function(){var a=getPickerInput();if(!a.prop('disabled')){return}a.prop('disabled',false);attachDatePickerEvents()};picker.hide=function(){var a=picker.widget.find('.collapse'),i,collapseData;for(i=0;i<a.length;i++){collapseData=a.eq(i).data('collapse');if(collapseData&&collapseData.transitioning){return}}picker.widget.hide();picker.widget.removeClass('picker-open');picker.viewMode=picker.startViewMode;showMode();picker.element.trigger({type:'dp.hide',date:k(picker.date)});detachDatePickerGlobalEvents()};picker.setValue=function(a){k.locale(picker.options.language);if(!a){picker.unset=true;set()}else{picker.unset=false}if(!k.isMoment(a)){a=(a instanceof Date)?k(a):k(a,picker.format,picker.options.useStrict)}else{a=a.locale(picker.options.language)}if(a.isValid()){picker.date=a;set();picker.viewDate=k({y:picker.date.year(),M:picker.date.month()});fillDate();fillTime()}else{notifyError(a)}};picker.getDate=function(){if(picker.unset){return null}return k(picker.date)};picker.setDate=function(a){var b=k(picker.date);if(!a){picker.setValue(null)}else{picker.setValue(a)}notifyChange(b,'function')};picker.setDisabledDates=function(a){picker.options.disabledDates=indexGivenDates(a);if(picker.viewDate){update()}};picker.setEnabledDates=function(a){picker.options.enabledDates=indexGivenDates(a);if(picker.viewDate){update()}};picker.setMaxDate=function(a){if(a===undefined){return}if(k.isMoment(a)||a instanceof Date){picker.options.maxDate=k(a)}else{picker.options.maxDate=k(a,picker.format,picker.options.useStrict)}if(picker.viewDate){update()}};picker.setMinDate=function(a){if(a===undefined){return}if(k.isMoment(a)||a instanceof Date){picker.options.minDate=k(a)}else{picker.options.minDate=k(a,picker.format,picker.options.useStrict)}if(picker.viewDate){update()}};init()};$.fn.bootstrapDatetimepicker=function(b){return this.each(function(){var a=$(this),data=a.data('DateTimePicker');if(!data){a.data('DateTimePicker',new DateTimePicker(this,b))}})};$.fn.bootstrapDatetimepicker.defaults={format:false,pickDate:true,pickTime:true,useMinutes:true,useSeconds:false,useCurrent:true,calendarWeeks:false,minuteStepping:1,minDate:k({y:1900}),maxDate:k().add(100,'y'),showToday:true,collapse:true,language:k.locale(),defaultDate:'',disabledDates:false,enabledDates:false,icons:{},useStrict:false,direction:'auto',sideBySide:false,daysOfWeekDisabled:[],widgetParent:false}}));