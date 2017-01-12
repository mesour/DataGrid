/**
 * Mesour Grid Selection Component - grid.selection.js
 *
 * @author Matous Nemec (http://mesour.com)
 */
var mesour = !mesour ? {} : mesour;
mesour.gridEditable = !mesour.gridEditable ? {} : mesour.gridEditable;

(function ($) {

    var Editable = function () {

        var _this = this;

        this.getEditableElement = function($el) {
            if (!$el.is('[data-grid-editable]')) {
                $el = $el.closest('[data-grid-editable]');
            }
            return {
                element: $el,
                name: $el.attr('data-grid-editable'),
                id: $el.attr('data-grid-id')
            };
        };

        this.getEditableValue = function($el) {
            if (!$el.is('[data-grid-value]')) {
                $el = $el.closest('[data-grid-value]');
            }
            return $el.attr('data-grid-value');
        };

        this.create = function () {
            $('.mesour-datagrid').each(function () {
                var $this = $(this).find('[data-mesour-editable]');
                if ($this.is('*')) {
                    var componentName = $this.attr('data-mesour-editable');
                    $(document).on('click', '[data-mesour-editable="' + componentName + '"] [data-grid-is-edit]', function (e) {
                        e.preventDefault();

                        var $current = $(this);
                        var element = _this.getEditableElement($current);
                        var value = _this.getEditableValue($current);

                        mesour.editable.getComponent(componentName).edit(element.name, element.element, element.id, value);
                    });
                    $(document).on('click', '[data-mesour-editable="' + componentName + '"] [data-grid-is-add]', function (e) {
                        e.preventDefault();

                        var element = _this.getEditableElement($(this));

                        mesour.editable.getComponent(componentName).newEntry(element.name, element.element, element.id);
                    });
                    $(document).on('click', '[data-mesour-editable="' + componentName + '"] [data-grid-is-remove]', function (e) {
                        e.preventDefault();

                        var $current = $(this);

                        var element = _this.getEditableElement($current);
                        var value = _this.getEditableValue($current);

                        var confirmText = $current.attr('data-confirm');
                        if (!confirmText || (confirmText && confirm(confirmText))) {
                            mesour.editable.getComponent(componentName).remove(element.name, element.element, element.id, value);
                        }
                    });
                }
            });
        };

        this.live = function () {
            $('.mesour-datagrid').each(function () {
                var $this = $(this).find('[data-mesour-editable]');
                if ($this.is('*')) {
                    var $edit = $this.find('[data-grid-edit]');
                    if (!$edit.find('[data-grid-is-edit]').is('*')) {
                        $edit.append('&nbsp;');
                        $edit.append('<a data-grid-is-edit="true" role="button"><span class="fa fa-pencil"></span></a>');
                    }
                }
            });
        };
    };

    mesour.core.createWidget('gridEditable', new Editable());

    mesour.on.ready('mesour-grid-editable', mesour.gridEditable.create);
    mesour.on.live('mesour-grid-editable', mesour.gridEditable.live);
})(jQuery);