/**
 * Mesour Grid Selection Component - grid.selection.js
 *
 * @author Matous Nemec (http://mesour.com)
 */
var mesour = !mesour ? {} : mesour;
mesour.grid = !mesour.grid ? {} : mesour.grid;

(function ($) {
    var live = function () {
        var grids = $('.mesour-datagrid');

        grids.find('.only-buttons').each(function () {
            $(this).closest('td').addClass('actions-column');
        });

        grids.find('[data-mesour-enabled-filter="1"]').find('.selection-dropdown').each(function () {
            $(this).before('<span class="fake-header">&nbsp;</span>');
        });

        $('[data-mesour-toggle="tooltip"]').tooltip();
    };

    live();
    $(document).on('ready', live);
    $(window).on('ajaxComplete', live);
    $(window).on('ajaxError', live);

    var Grid = function (options) {

        var _this = this;

        this.items = {};

        this.setRelation = function (gridName, tableName, data) {
            if (!_this.items[gridName]) {
                _this.items[gridName] = {
                    relations: {}
                };
            }
            if (!_this.items[gridName].relations[tableName]) {
                _this.items[gridName].relations[tableName] = {};
            }
            _this.items[gridName].relations[tableName] = data;
        };

    };

    mesour.core.createWidget('grid', new Grid());
})(jQuery);