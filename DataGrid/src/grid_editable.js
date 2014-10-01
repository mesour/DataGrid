(function($) {
    var isInEdit = false;
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

        $('[data-editable]').on('dblclick', function(){
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
                mouse_in = true,
                old_value = column.html();

            var input = $('<input class="editable-input" type="text" data-old-value="'+old_value+'">');
            input.val(old_value);
            input.width(column.innerWidth());
            input.on({
                'keyup': function(e) {
                    if(e.keyCode === 13) {
                        $.post(editable_link, {
                            'data': {
                                lineId: line_id,
                                columnName: column_name,
                                oldValue: old_value,
                                newValue: input.val()
                            }
                        }).complete(function(){
                            column.html(input.val());
                            isInEdit = false;
                        }).error(function(){
                            closeAllEditables();
                        });
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

            isMouseIn = function() {
                return mouse_in;
            };

            $(this).html(input);

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