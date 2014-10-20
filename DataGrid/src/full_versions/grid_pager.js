(function($) {
	$(document).ready(function() {
		$('form.form-pager').on('submit', function(e) {
            e.preventDefault();
            var $this = $(this);
            var number = $this.find('.number').val();
            var gridName = $this.closest('[data-mesour-grid]').attr('data-mesour-grid');
            if(!number) return;
            $.get(mesour.getUrlWithParam(gridName, $this.attr('action'), 'pager', 'number', number));
        });
	});
})(jQuery);