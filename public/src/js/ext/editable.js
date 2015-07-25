/**
 * Mesour DataGrid - ext/editable.js
 *
 * @author Matous Nemec (mesour.com)
 */
(function($) {
    var isInEdit = false,
        isFloat = function (n) {
            return n === +n && n !== (n|0);
        },
        isInteger = function(n) {
            return n === +n && n === (n|0);
        };
    $(document).on('ready', function() {
        var change = function() {
            $('.data-grid').find('[data-editable-link]').each(function() {
                var $this = $(this),
                    editable_link = $this.attr('data-editable-link'),
                    mouse_in = false,
                    closeAllEditables = function() {
                        $('[data-editable]').each(function(){
                            if($(this).find('input,select').is('*')) {
                                var old_value = $(this).find('input,select').attr('data-old-value');
                                $(this).html(old_value);
                            }
                        });
                        isInEdit = false;
                    },
                    isMouseIn = function() {
                        return mouse_in;
                    };

                var onEdit = function(){
                    if(isInEdit) return;
                    isInEdit = true;

                    var $_this = $(this),
                        gridName = $(this).closest('[data-mesour-grid]').attr('data-mesour-grid');
                    if($_this.closest('[data-mesour-grid]').parent().closest('[data-mesour-grid]').is('*')) {
                        gridName = $_this.closest('[data-mesour-grid]').parent().closest('[data-mesour-grid]').attr('data-mesour-grid')+'-'+gridName;
                    }

                    var column = $(this);
                    column.width(column.width());
                    var line = column.closest('tr');
                    if(!line.is('*')) {
                        line = column.closest('li');
                    }
                    var line_splited = line.attr('id').split('-'),
                        line_id = line_splited[line_splited.length-1],
                        column_name = column.attr('data-editable'),
                        related = column.attr('data-editable-related'),
                        type = column.attr('data-editable-type'),
                        separator = column.attr('data-separator'),
                        unit = column.attr('data-unit'),
                        date_format = column.attr('data-date-format'),
                        mouse_in = true,
                        old_value = column.html(),
                        keyUpCallback = function(e, fromJs) {
                            if(e.keyCode === 13 || fromJs) {
                                var value = getValidValue();
                                if(type === 'date') {
                                    input.data('DateTimePicker').destroy();
                                }
                                if(value !== false) {
                                    $.get(mesour.getUrlWithParam(gridName, editable_link, 'editable', 'editable_data', {
                                        'data': {
                                            lineId: line_id,
                                            columnName: column_name,
                                            oldValue: old_value,
                                            dataValue: column.attr('data-value'),
                                            newValue: input.val()
                                        }
                                    })).complete(function(){
                                        column.html(input.is('select') ? input.find('option:selected').text() : input.val());
                                        isInEdit = false;
                                    }).error(function(){
                                        closeAllEditables();
                                    });
                                }
                            } else if(e.keyCode === 27) {
                                closeAllEditables();
                            }
                        };

                    var html = '<input class="editable-input" type="text" data-old-value="' + old_value + '">';
                    if (related) {
                        html = '<select class="editable-input" data-old-value="' + old_value + '">';
                        if (mesour.dataGrid.list[gridName] && mesour.dataGrid.list[gridName].relations[related]) {
                            var _related = mesour.dataGrid.list[gridName].relations[related];
                            for (var i in _related) {
                                var option;
                                if (_related[i] === old_value) {
                                    html += '<option value="' + i + '" selected="selected">' + _related[i] + '</option>';
                                } else {
                                    html += '<option value="' + i + '">' + _related[i] + '</option>';
                                }
                            }
                        }
                        html += '</select>';
                        html = $(html);
                        var sel = html.find(':selected');
                        setTimeout(function() {
                            sel.prop('selected', false).prop('selected', true);
                        }, 200);
                        html.on('change', function () {
                            keyUpCallback.call(html, {keyCode: 13});
                        });
                    }

                    var input = $(html),
                        getValidValue = function() {
                            if(type === 'number') {
                                var num = Number(input.val().replace(',', '.'));
                                if(isInteger(num) || isFloat(num)) {
                                    return input.val();
                                } else {
                                    alert('Value of this column must be a number.');
                                    return false;
                                }
                            }
                            return input.val();
                        };
                    input.width(column.innerWidth());
                    input.on({
                        'keyup': keyUpCallback,
                        'mouseenter': function() {
                            mouse_in = true;
                        },
                        'mouseleave': function() {
                            mouse_in = false;
                        }
                    });

                    $(document).on('mouseenter', '.ui-datepicker', function() {
                        mouse_in = true;
                    });

                    $(document).on('mouseleave', '.ui-datepicker', function() {
                        mouse_in = false;
                    });

                    isMouseIn = function() {
                        return mouse_in;
                    };

                    $(this).html(input);

                    switch(type) {
                        case 'number' :
                            input.val($.trim(old_value.replace(separator, '').replace(unit, '')));
                            break;
                        case 'date' :
                            input.val(!old_value || old_value === '-'?'':old_value);
                            input.bootstrapDatetimepicker({
                                format: date_format,
                                useSeconds: true,
                                focusOnShow: false,
                                hide: function() {
                                    input.focus();
                                },
                                useCurrent: false
                            });
                            break;
                        default :
                            input.val(old_value);
                    }
                    input.focus();
                };

                $this.find('>table>tbody>tr>td[data-editable], >table>tbody>tr>td>span>span[data-editable], >.tree-grid>ul li>div>span[data-editable], >.tree-grid>ul li>div>span>span>span[data-editable]').off('dblclick.grid-editable', onEdit);
                $this.find('>table>tbody>tr>td[data-editable], >table>tbody>tr>td>span>span[data-editable], >.tree-grid>ul li>div>span[data-editable], >.tree-grid>ul li>div>span>span>span[data-editable]').on('dblclick.grid-editable', onEdit);

                var _html = $('html');
                _html.off('click.grid-editable');
                _html.on('click.grid-editable', function() {
                    if(isMouseIn()) {
                        return;
                    } else {
                        closeAllEditables();
                    }
                });
            });
        };
        change();
        $(window).ajaxComplete(change);
    });
})(jQuery);