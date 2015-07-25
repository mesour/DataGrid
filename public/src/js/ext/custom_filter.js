/**
 * Mesour DataGrid - ext/custom_filter.js
 *
 * @author Matous Nemec (mesour.com)
 */
(function($) {
    $(document).ready(function() {
        $('.custom-filter-form form').each(function() {
            var $form = $(this);
            var gridName = $form.closest('[data-mesour-grid]').attr('data-mesour-grid');
            var filterValues = mesour.dataGrid.list[gridName].filterValues;
            for(var x in filterValues) {
                if(x.indexOf('[') !== -1) {
                    filterValues[x+']'] = filterValues[x];
                    delete filterValues[x];
                }
            }
            $form.find('input[type=checkbox]').each(function() {
                $(this).closest('label').css({
                    width: 'auto',
                    height: 'auto',
                    paddingLeft: '5px',
                    position: 'relative',
                    lineHeight: '13px',
                    margin: 0
                }).before(this)
            });
            $form.find('input, textarea').filter(':not([type=submit]):not([type=hidden])').each(function() {
                var $this = $(this),
                    name = $this.attr('name');
                if($this.attr('type') === 'radio') {
                    if($this.val() === filterValues[name]) {
                        $this.prop('checked', true);
                    } else {
                        $this.prop('checked', false);
                    }
                } else if($this.attr('type') === 'checkbox') {
                    if(filterValues[name]) {
                        $this.prop('checked', true);
                    } else {
                        $this.prop('checked', false);
                    }
                } else {
                    $this.val(filterValues[name] ? filterValues[name] : null);
                }

            });
            $form.find('select').each(function() {
                var $this = $(this),
                    name = $this.attr('name');
                if(filterValues[name]) {
                    $this.find('option[value="'+filterValues[name]+'"]').prop('selected', true);
                } else {
                    $this.find('option[value=""]').prop('selected', false);
                }
            });
            var submittedBy = '';
            $form.find('input[type=submit]').on('click', function() {
                submittedBy = $(this).attr('name');
            });
            $form.on('submit', function(e) {
                e.preventDefault();
                var form = $(this);
                var sendValues = {};

                var gridName = form.closest('[data-mesour-grid]').attr('data-mesour-grid');

                sendValues['submittedBy'] = submittedBy;

                // get values
                var values = form.serializeArray();
                values = values.concat(
                    $form.find('input[type=checkbox]:not(:checked)').map(function() {
                        return $(this).is(':checked') ? {"name": this.name, "value": this.value} : null
                    }).get()
                );

                for (var i = 0; i < values.length; i++) {
                    var name = values[i].name;

                    // multi
                    if (name in sendValues) {
                        var val = sendValues[name];

                        if (!(val instanceof Array)) {
                            val = [val];
                        }

                        val.push(values[i].value);
                        sendValues[name] = val;
                    } else {
                        sendValues[name] = values[i].value;
                    }
                }
                delete sendValues.do;
                $.get(mesour.getUrlWithParam(gridName, form.attr("action"), 'filter', 'settings', sendValues))
                    .complete(mesour.snippets.callback);
            });
        });
    });
})(jQuery);