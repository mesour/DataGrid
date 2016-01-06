/**
 * Mesour Grid Selection Component - grid.selection.js
 *
 * @author Matous Nemec (http://mesour.com)
 */
var mesour = !mesour ? {} : mesour;
if (!mesour.selection) {
    throw new Error('Widget mesour.selection is not created. First create mesour.selection widget.');
}
mesour.gridSelection = !mesour.gridSelection ? {} : mesour.gridSelection;

(function ($) {
    var GridSelection = function (options) {
        var _this = this;

        this.sendData = function (name, linkName, ajax) {
            var data = {
                items: mesour.selection.getValues(name),
                name: linkName
            };
            if (ajax) {
                var result = mesour.core.createLink(name, 'onSelect', data, true);
                $.post(result[0], result[1]).complete(mesour.core.redrawCallback);
            } else {
                window.location.href = mesour.core.createLink(name, 'onSelect', data);
            }
        };

        this.create = function () {
            var names = {};
            $('[' + options.attributeName + ']')
                .each(function () {
                    var $this = $(this),
                        name = $this.attr(options.attributeName);
                    names[name] = name;
                })
                .click(function (e) {
                    e.preventDefault();

                    var $this = $(this),
                        name = $this.attr(options.attributeName),
                        linkName = $this.attr('data-name'),
                        isAjax = $this.is('[' + options.ajaxAttribute + '=ajax]'),
                        _confirm = $this.attr('data-confirm');

                    if (_confirm) {
                        if (!confirm(_confirm)) {
                            return;
                        }
                    }
                    _this.sendData(name, linkName, isAjax);
                });

            var CallbackManager = function () {
                var currentName, items;

                this.setName = function (newName) {
                    currentName = newName;
                };

                this.setItems = function (newItems) {
                    items = newItems;
                };

                this.callback = function () {
                    var values = mesour.selection.getValues(currentName),
                        matchCount = 0;
                    for (var j in values) {
                        if (values[j]) {
                            matchCount++;
                        }
                    }
                    var button = $('[' + options.dropDownAttr + '="' + currentName + '"]').children('button'),
                        counter = button.find('[data-selection-counter]');
                    if (!counter.is('*')) {
                        counter = $('<span data-selection-counter="1"></span>');
                        button.find('.caret').before(counter);
                    }
                    counter.text('(' + matchCount + ') ');
                    if (matchCount > 0) {
                        button.removeClass('disabled');
                    } else {
                        button.addClass('disabled');
                    }

                    items.each(function () {
                        var $_this = $(this),
                            checked = $_this.hasClass('btn-warning');
                        if (checked) {
                            $_this.closest('td').closest('tr').addClass('checked');
                        } else {
                            $_this.closest('td').closest('tr').removeClass('checked');
                        }
                    });
                };
            };

            for (var i in names) {
                var items = mesour.selection.getItems(names[i]),
                    currentName = names[i];

                var instance = new CallbackManager();
                instance.setItems(items);
                instance.setName(currentName);

                items.on('change.selection', instance.callback);
                mesour.selection.getMainCheckbox(currentName).on('change.selection', instance.callback);
            }
        };
    };
    mesour.core.createWidget('gridSelection', new GridSelection({
        attributeName: 'data-mesour-gridselection',
        ajaxAttribute: 'data-mesour-selection',
        dropDownAttr: 'data-mesour-selectiondropdown'
    }));

    mesour.on.live('grid-selection-links', mesour.gridSelection.create);
})(jQuery);