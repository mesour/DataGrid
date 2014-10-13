(function($) {
	$(document).ready(function() {
        var $form = $('.custom-filter-form form');
        for(var x in mesour.filterValues) {
            if(x.indexOf('[') !== -1) {
                mesour.filterValues[x+']'] = mesour.filterValues[x];
                delete mesour.filterValues[x];
            }
        }
        $form.find('input').filter(':not([type=submit]):not([type=hidden])').each(function() {
            var $this = $(this),
                name = $this.attr('name');
            $this.val(mesour.filterValues[name] ? mesour.filterValues[name] : null);
        });
        var submittedBy = '';
        $form.find('input[type=submit]').on('click', function() {
            submittedBy = $(this).attr('name');
        });
        $form.on('submit', function(e) {
            e.preventDefault();
            var form = $(this);
            var sendValues = {};

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
            $.get(mesour.getUrlWithParam(form.attr("action"), 'filter', 'settings', sendValues));
        });
	});
})(jQuery);