/**
 * Mesour Grid Selection Component - grid.selection.js
 *
 * @author Matous Nemec (http://mesour.com)
 */
var mesour = !mesour ? {} : mesour;
if (!mesour.selection) {
    throw new Error('Widget mesour.selection is not created. First create mesour.selection widget.');
}
mesour.gridSortable = !mesour.gridSortable ? {} : mesour.gridSortable;

(function ($) {
    var Sortable = function (options) {

        var _this = this;

        this.sendData = function (name, data) {
            var result = mesour.core.createLink(name, 'sortData', data, true);
            $.post(result[0], result[1]).complete(mesour.core.redrawCallback);
        };

        this.create = function () {
            var tbody = $('[' + options.attributeName + ']').children('tbody');
            if(!tbody.is('*') || typeof tbody.sortable !== 'function') {
                return;
            }
            tbody.sortable({
                disableNesting: 'no-child',
                forcePlaceholderSize: true,
                autoScroll: true,
                handle: 'a.handler',
                listType: 'tbody',
                helper: 'clone',
                items: '> tr:not(.no-sort)',
                maxLevels: $('a#sort_href').attr("title"),
                opacity: .6,
                placeholder: 'placeholder',
                revert: 250,
                tabSize: 25,
                tolerance: 'pointer',
                toleranceElement: '> td',
                stop: function (a, b) {
                    var item_id = b.item.attr('id').split('-');
                    var $this = $(this);
                    var data = {
                        data: $this.sortable('serialize'),
                        item: item_id[item_id.length - 1]
                    };
                    _this.sendData($this.closest('[' + options.attributeName + ']').attr(options.attributeName), data)
                },
                start: function (a, b) {
                    var sorted;
                    $(this).find('.no-sort').remove();
                    if (b.placeholder.is('tr') && sorted == undefined) {
                        var x = 0;
                        $('thead tr th', $(a.currentTarget).closest('table')).each(function () {
                            x++;
                            $('td:nth-child(' + x + ')', b.helper).css('width', $(this).width());
                        });

                        $(b.placeholder).find('td').css('height', $(b.placeholder).next('tr').outerHeight());

                        sorted = true;
                    } else if (sorted == true) {
                        sorted = false;
                    }
                }
            });
        };
    };

    mesour.core.createWidget('gridSortable', new Sortable({
        attributeName: 'data-mesour-sortable'
    }));

    mesour.on.live('grid-sortable-links', mesour.gridSortable.create);
})(jQuery);