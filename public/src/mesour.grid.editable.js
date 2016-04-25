/**
 * Mesour Grid Selection Component - grid.selection.js
 *
 * @author Matous Nemec (http://mesour.com)
 */
var mesour = !mesour ? {} : mesour;
mesour.gridEditable = !mesour.gridEditable ? {} : mesour.gridEditable;

(function ($) {

    var Editable = function (options) {

        this.create = function () {
            $('.mesour-datagrid').each(function () {
                var $this = $(this).find('[data-mesour-editable]');
                if ($this.is('*')) {
                    var componentName = $this.attr('data-mesour-editable');
                    $(document).on('click', '[data-mesour-editable="' + componentName + '"] [' + options.attributeName + ']', function (e) {
                        var $element = $(this),
                            name = $element.attr('data-grid-editable'),
                            value = $element.attr('data-grid-value'),
                            addNew = $element.attr('data-grid-add'),
                            id = $element.attr('data-grid-id');

                        if (e.ctrlKey || e.metaKey) {
                            if (addNew === 'true') {
                                e.preventDefault();
                                mesour.editable.getComponent(componentName).newEntry(name, $element, id);
                            } else {
                                e.preventDefault();
                                mesour.editable.getComponent(componentName).edit(name, $element, id, value);
                            }
                        }
                    });
                }
            });
        };
    };

    mesour.core.createWidget('gridEditable', new Editable({
        attributeName: 'data-grid-editable',
        attributeValue: 'data-grid-value',
        attributeIdentifier: 'data-grid-id'
    }));

    mesour.on.ready('mesour-grid-editable', mesour.gridEditable.create);
})(jQuery);