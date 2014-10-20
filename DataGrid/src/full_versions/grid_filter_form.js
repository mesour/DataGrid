(function($) {
	$(document).ready(function() {
        $('.custom-filter-form form').each(function() {
            var $form = $(this);
            var gridName = $form.closest('[data-mesour-grid]').attr('data-mesour-grid');
            var filterValues = mesour.dataGrid[gridName].filterValues;
            for(var x in filterValues) {
                if(x.indexOf('[') !== -1) {
                    filterValues[x+']'] = filterValues[x];
                    delete filterValues[x];
                }
            }
            $form.find('input').filter(':not([type=submit]):not([type=hidden])').each(function() {
                var $this = $(this),
                    name = $this.attr('name');
                $this.val(filterValues[name] ? filterValues[name] : null);
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
                $.get(mesour.getUrlWithParam(gridName, form.attr("action"), 'filter', 'settings', sendValues));
            });
        });
	});
})(jQuery);