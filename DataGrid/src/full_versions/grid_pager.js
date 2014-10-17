(function($) {
	$(document).ready(function() {
		$('form.form-pager').on('submit', function(e) {
            e.preventDefault();
            var $this = $(this);
            var number = $this.find('.number').val();
            if(!number) return;
            $.get(mesour.getUrlWithParam($this.attr('action'), 'pager', 'number', number));
        });
	});
})(jQuery);