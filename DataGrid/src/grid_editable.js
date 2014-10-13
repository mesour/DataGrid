(function($) {
    var isInEdit = false,
        isFloat = function (n) {
            return n === +n && n !== (n|0);
        },
        isInteger = function(n) {
            return n === +n && n === (n|0);
        };
    $(document).on('ready', function() {
        var editable_link = $('[data-editable]:first').closest('.data-grid').attr('data-editable-link'),
            mouse_in = false,
            closeAllEditables = function() {
                $('[data-editable]').each(function(){
                    if($(this).find('input').is('*')) {
                        var old_value = $(this).find('input').attr('data-old-value');
                        $(this).html(old_value);
                    }
                });
                isInEdit = false;
            },
            isMouseIn = function() {
                return mouse_in;
            };

        $(document).on('dblclick', '[data-editable]', function(){
            if(isInEdit) return;
            isInEdit = true;

            var column = $(this);
            column.width(column.width());
            var line = column.closest('tr');
            if(!line.is('*')) {
                line = column.closest('li');
            }
            var line_splited = line.attr('id').split('-'),
                line_id = line_splited[line_splited.length-1],
                column_name = column.attr('data-editable'),
                type = column.attr('data-editable-type'),
                separator = column.attr('data-separator'),
                date_format = column.attr('data-date-format'),
                mouse_in = true,
                old_value = column.html();

            var input = $('<input class="editable-input" type="text" data-old-value="'+old_value+'">'),
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
                'keyup': function(e, fromJs) {
                    if(e.keyCode === 13 || fromJs) {
                        var value = getValidValue();
                        if(type === 'date') {
                            input.data('DateTimePicker').destroy();
                        }
                        if(value !== false) {
                            $.get(mesour.getUrlWithParam(editable_link, 'editable', 'editable_data', {
                                'data': {
                                    lineId: line_id,
                                    columnName: column_name,
                                    oldValue: old_value,
                                    newValue: input.val()
                                }
                            })).complete(function(){
                                column.html(input.val());
                                isInEdit = false;
                            }).error(function(){
                                closeAllEditables();
                            });
                        }
                    } else if(e.keyCode === 27) {
                        closeAllEditables();
                    }
                },
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
                    input.val(old_value.replace(separator, ''));
                    break;
                case 'date' :
                    input.val(old_value);
                    input.bootstrapDatetimepicker({
                        format: date_format,
                        useSeconds: true,
                        hide: function() {
                            input.focus();
                        }
                    });
                    break;
                default :
                    input.val(old_value);
            }
            input.focus();
        });

        $('html').on('click', function() {
            if(isMouseIn()) {
                return;
            } else {
                closeAllEditables();
            }
        });
    });
})(jQuery);