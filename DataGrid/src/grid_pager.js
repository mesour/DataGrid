(function($) {
	$(document).ready(function() {
		$('form.form-pager').on('submit', function(e) {
            e.preventDefault();
            var $this = $(this);
            var number = $this.find('.number').val();
            if(!number) return;
            $.post($this.attr('action'), {
                number: number,
                'to_page': $this.find('.to-page').val()
            });
        });
	});
})(jQuery);