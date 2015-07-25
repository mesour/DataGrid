/**
 * Mesour DataGrid - ext/selection.js
 *
 * @author Matous Nemec (mesour.com)
 */
(function($) {
    var native_confirm = true;
    var gridConfirm = function(message) {
        return confirm(message);
    };
    var gridCustomSend = function(url, data) {
        $('#confirm-modal .yes').data('confirm-post-data', data);
    };
    var options = {
        mainCheckboxClass: 'main-checkbox',
        checkboxButtonIdent: '.checkbox-button',
        checkboxSelectorIdent: '.checkbox-selector',
        selectCheckboxClass: 'select-checkbox',
        isUnactiveClass: 'is-unactive',
        isActiveClass: 'is-active',
        confirmDataKeyName: 'confirm-post-data',
        dropDownMenuClass: 'dropdown-menu'
    };
    var buttonToNoChecked = function(button) {
        button.removeClass('any-checked checked btn-warning');
        button.addClass('btn-default');
        button.html('&nbsp;&nbsp;&nbsp;&nbsp;');
    };
    var buttonToChecked = function(button) {
        button.addClass('checked btn-warning');
        button.removeClass('any-checked btn-default');
        button.html('<b class=\"glyphicon glyphicon-ok\"></b>');
    };
    var buttonToAnyChecked = function(button) {
        button.addClass('any-checked btn-warning');
        button.removeClass('checked btn-default');
        button.html('<b class=\"glyphicon glyphicon-minus\"></b>');
    };

    mesour.on.live('grid-ready-selection', function() {
        $('.checkbox-selector .dropdown-toggle').find('a.btn').off('click.grid');

        $(options.checkboxSelectorIdent + ' ul.' + options.dropDownMenuClass + ' li a').off('click.grid');
        $(options.checkboxButtonIdent + ' ul li a').on('click.grid');
        $('.checkbox-selector .dropdown-toggle').off('click.grid, mouseenter.grid, mouseleave.grid');
        $('.checkbox-selector .dropdown-toggle').find('a.btn').off('click.grid, mouseenter.grid, mouseleave.grid');
        $('.select-checkbox').off('click.grid');
        $(window).off('click.grid');
        $('.data-grid').find('td').off('click.grid');

        var toggleCheckbox = function($this) {
            var button = $(this).find('.' + options.selectCheckboxClass);
            if ($($this).hasClass('checked')) {
                buttonToNoChecked(button);
            } else {
                buttonToChecked(button);
            }
        };

        var main_checkboxes = $('.main-checkbox');
        main_checkboxes.off('click.grid');

        main_checkboxes.each(function() {
            var mainCheckboxButton = $(this),
                $tbody = mainCheckboxButton.closest('.data-grid').find('tbody:first, ul.grid-ul');

            var checkAllCheckboxes = function(no_main) {
                var check_all = true;
                var one_checked = false;
                $tbody.find('>tr, >li').each(function() {
                    if (!$(this).find('.' + options.selectCheckboxClass).hasClass('checked'))
                        check_all = false;
                    else
                        one_checked = true;
                });

                if (!no_main) {
                    if (check_all) {
                        buttonToChecked(mainCheckboxButton);
                    } else {
                        if(one_checked)
                            buttonToAnyChecked(mainCheckboxButton);
                        else
                            buttonToNoChecked(mainCheckboxButton);
                    }
                }
                if (one_checked)
                    $tbody.closest('.data-grid').next('.datagrid-bottom').find(options.checkboxButtonIdent + ' button').removeClass('disabled').closest('div').css('cursor', 'pointer');
                else
                    $tbody.closest('.data-grid').next('.datagrid-bottom').find(options.checkboxButtonIdent + ' button').addClass('disabled').closest('div').css('cursor', 'not-allowed');
            };

            mainCheckboxButton.on('click.grid', function(e) {
                e.stopPropagation();
                var $this = $(this);
                $tbody.find('>tr:not(.no-sort), >li:not(.no-sort)').each(toggleCheckbox, $this);
                checkAllCheckboxes(true);
            });

            $tbody.closest('.data-grid').next('.datagrid-bottom').find(options.checkboxButtonIdent + ' ul li a').on('click.grid', function(e) {
                e.preventDefault();
                var data = [],
                    $this = $(this),
                    gridName = $(this).closest('[data-mesour-grid]').attr('data-mesour-grid');
                if($this.closest('[data-mesour-grid]').parent().closest('[data-mesour-grid]').is('*')) {
                    gridName = $this.closest('[data-mesour-grid]').parent().closest('[data-mesour-grid]').attr('data-mesour-grid')+'-'+gridName;
                }
                $tbody.find('.' + options.selectCheckboxClass + '.checked').each(function() {
                    data.push($(this).attr('data-value'));
                });
                if (data.length > 0) {
                    var isConfirm = $(this).attr('data-confirm'),
                        isAjax = $(this).hasClass('is-ajax');
                    var href = mesour.getUrlWithParam(gridName, $(this).attr('href'), 'selection', 'selected', {items: data});
                    if(native_confirm && isConfirm) {
                        if(gridConfirm(isConfirm)) {
                            if(isAjax) {
                                $.get(href).complete(mesour.snippets.callback);
                            } else {
                                location.href = href;
                            }

                        }
                    } else if(isConfirm) {
                        gridCustomSend(href, {selected: data});
                    } else {
                        if(isAjax) {
                            $.get(href).complete(mesour.snippets.callback);
                        } else {
                            location.href = href;
                        }
                    }
                }

            });

            mainCheckboxButton.closest('.checkbox-selector').find('ul.' + options.dropDownMenuClass + ' li a').on('click.grid', function(e) {
                e.preventDefault();
                var $this = $(this);
                switch ($this.attr('data-select')) {
                    case 'inverse':
                        $tbody.find('tr, li').each(function() {
                            var $checkbox = $(this).find('.' + options.selectCheckboxClass);
                            if ($checkbox.hasClass('checked'))
                                buttonToNoChecked($checkbox);
                            else
                                buttonToChecked($checkbox);
                        });
                        break;
                    default:
                        $tbody.find('tr, li').each(function() {
                            if ($(this).find('.is-' + $this.attr('data-select')).is('*'))
                                buttonToChecked($(this).find('.' + options.selectCheckboxClass));
                            else
                                buttonToNoChecked($(this).find('.' + options.selectCheckboxClass));
                        });
                }
                checkAllCheckboxes();
            });

            var checkButton = function($this) {
                if ($this.hasClass('checked')) {
                    buttonToNoChecked($this);
                } else {
                    buttonToChecked($this);
                }
                checkAllCheckboxes();
            };
            $tbody.find('>tr>td.with-checkbox, >li>div>span.with-checkbox').find('.' + options.selectCheckboxClass).on('click.grid', function(e) {
                e.preventDefault();
                var $this = $(this);
                checkButton($this);
            });

            var inCheckbox = false, inButton = false;
            mainCheckboxButton.closest('.checkbox-selector').find('.dropdown-toggle').on({
                'click.grid': function(e) {
                    e.preventDefault();
                    if(inCheckbox) {
                        return;
                    }
                    var $group = $(this).closest('.btn-group');
                    $group.addClass('open');
                },
                'mouseenter.grid': function() {
                    inButton = true;
                },
                'mouseleave.grid': function() {
                    inButton = false;
                }
            });
            mainCheckboxButton.closest('.checkbox-selector').find('.dropdown-toggle').find('a.btn').on({
                'click.grid': function(e) {
                    e.preventDefault();
                    var $this = $(this);
                    if ($this.hasClass('checked')) {
                        buttonToNoChecked($this);
                    } else {
                        buttonToChecked($this);
                    }
                },
                'mouseenter.grid': function() {
                    inCheckbox = true;
                },
                'mouseleave.grid': function() {
                    inCheckbox = false;
                }
            });
            $(window).on('click.grid', function() {
                if (!inButton)
                    mainCheckboxButton.closest('.checkbox-selector').removeClass('open');
            });
        });
    });
})(jQuery);