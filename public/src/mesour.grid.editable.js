/**
 * Mesour Grid Selection Component - grid.selection.js
 *
 * @author Matous Nemec (http://mesour.com)
 */
var mesour = !mesour ? {} : mesour;
mesour.gridEditable = !mesour.gridEditable ? {} : mesour.gridEditable;

(function ($) {
    var isInEdit = false,
        isFloat = function (n) {
            return n === +n && n !== (n | 0);
        },
        isInteger = function (n) {
            return n === +n && n === (n | 0);
        };

    var Editable = function (options) {

        var _this = this;

        this.sendData = function (name, data) {
            var result = mesour.core.createLink(name, 'editCell', data, true);
            return $.post(result[0], result[1]).complete(mesour.core.redrawCallback);
        };

        this.create = function () {
            $('.mesour-datagrid').find('[' + options.attributeName + ']').each(function () {
                var $this = $(this),
                    editable_link = $this.attr(options.attributeName),
                    mouse_in = false,
                    closeAllEditables = function () {
                        $('[data-editable]').each(function () {
                            if ($(this).find('input,select').is('*')) {
                                var old_value = $(this).find('input,select').attr('data-old-value');
                                $(this).html(old_value);
                            }
                        });
                        isInEdit = false;
                    },
                    isMouseIn = function () {
                        return mouse_in;
                    };

                var onEdit = function () {
                    if (window.getSelection)
                        window.getSelection().removeAllRanges();
                    else if (document.selection)
                        document.selection.empty();

                    if (isInEdit) return;
                    isInEdit = true;

                    var $_this = $(this),
                        gridName = $(this).closest('[data-mesour-grid]').attr('data-mesour-grid');

                    var column = $(this);
                    column.width(column.width());
                    var line = column.closest('tr');
                    if (!line.is('*')) {
                        line = column.closest('li');
                    }
                    var line_splited = line.attr('id').split('-'),
                        line_id = line_splited[line_splited.length - 1],
                        column_name = column.attr('data-editable'),
                        related = column.attr('data-editable-related'),
                        type = column.attr('data-editable-type'),
                        pickTime = column.attr('data-editable-pickTime') == 1,
                        separator = column.attr('data-separator'),
                        unit = column.attr('data-unit'),
                        date_format = column.attr('data-date-format'),
                        mouse_in = true,
                        old_value = column.html(),
                        keyUpCallback = function (e, fromJs) {
                            if (e.keyCode === 13 || fromJs) {
                                var value = getValidValue();
                                if (type === 'date') {
                                    input.data('DateTimePicker').destroy();
                                }
                                if (value !== false) {
                                    _this.sendData(editable_link, {
                                        'data': {
                                            lineId: line_id,
                                            columnName: column_name,
                                            oldValue: old_value,
                                            dataValue: column.attr('data-value'),
                                            newValue: input.val()
                                        }
                                    }).complete(function () {
                                        column.html(input.is('select') ? input.find('option:selected').text() : input.val());
                                        isInEdit = false;
                                    }).error(function () {
                                        closeAllEditables();
                                    });
                                }
                            } else if (e.keyCode === 27) {
                                closeAllEditables();
                            }
                        };

                    var html = '<input class="editable-input" type="text" data-old-value="' + old_value + '">';
                    if (related) {
                        html = $('<select class="editable-input" data-old-value="' + old_value + '"></select>');
                        html.on('change', function () {
                            keyUpCallback.call(html, {keyCode: 13});
                        });
                        if (mesour.grid.items[gridName] && mesour.grid.items[gridName].relations[related]) {
                            var _related = mesour.grid.items[gridName].relations[related];
                            for (var i in _related) {
                                var option = $('<option value="' + i + '">' + _related[i] + '</option>');
                                html.append(option);
                                if (_related[i] === old_value) {
                                    option.prop('selected', true);
                                }
                            }
                        }
                    }

                    var input = $(html),
                        getValidValue = function () {
                            if (type === 'number') {
                                var num = Number(input.val().replace(',', '.'));
                                if (isInteger(num) || isFloat(num)) {
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
                        'mouseenter': function () {
                            mouse_in = true;
                        },
                        'mouseleave': function () {
                            mouse_in = false;
                        }
                    });

                    $(document).on('mouseenter', '.ui-datepicker', function () {
                        mouse_in = true;
                    });

                    $(document).on('mouseleave', '.ui-datepicker', function () {
                        mouse_in = false;
                    });

                    isMouseIn = function () {
                        return mouse_in;
                    };

                    $(this).html(input);

                    switch (type) {
                        case 'number' :
                            input.val($.trim(old_value.replace(separator, '').replace(unit, '')));
                            break;
                        case 'date' :
                            input.val(!old_value || old_value === '-' ? '' : old_value);
                            input.bootstrapDatetimepicker({
                                format: date_format,
                                useSeconds: true,
                                pickTime: pickTime,
                                focusOnShow: false,
                                hide: function () {
                                    input.focus();
                                },
                                useCurrent: false
                            });
                            break;
                        default :
                            if (input.is('input')) {
                                input.val(old_value);
                            }
                    }
                    input.focus();
                };

                $this.find('>tbody>tr>td[data-editable], >tbody>tr>td>span>span[data-editable]').off('dblclick.grid-editable', onEdit);
                $this.find('>tbody>tr>td[data-editable], >tbody>tr>td>span>span[data-editable]').on('dblclick.grid-editable', onEdit);

                $('html').off('click.grid-editable')
                    .on('click.grid-editable', function () {
                        if (isMouseIn()) {
                            return;
                        } else {
                            closeAllEditables();
                        }
                    });
            });
        };
    };

    mesour.core.createWidget('gridEditable', new Editable({
        attributeName: 'data-mesour-editable'
    }));

    mesour.on.live('mesour-grid-editable', mesour.gridEditable.create);
})(jQuery);