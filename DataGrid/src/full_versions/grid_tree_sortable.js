(function($) {
	$(document).ready(function() {
		$('ul.sortable').nestedSortable({
			disableNesting: 'no-nest',
			forcePlaceholderSize: true,
			autoScroll: true,
			handle: 'a.handler',
			listType: 'ul',
			helper: 'clone',
			items: 'li',
			maxLevels: 10,
			opacity: .6,
			placeholder: 'placeholder',
			revert: 250,
			tabSize: 25,
			tolerance: 'pointer',
			toleranceElement: '> div',
			stop: function() {
                var $this = $(this);
                var gridName = $this.closest('[data-mesour-grid]').attr('data-mesour-grid');
				$.get(mesour.getUrlWithParam(gridName, $this.attr("data-sort-href"), 'sortable', 'sortable_data', $this.nestedSortable('serialize')), function(data) {
					
				});
			},
			start: function(a, b) {
				$(b.placeholder).css('height', $(a.toElement).closest('li').outerHeight());
			}
		});

		$('ul.sortable-simple').sortable({
			forcePlaceholderSize: true,
			autoScroll: true,
			handle: 'a.handler',
			listType: 'ul',
			helper: 'clone',
			items: 'li',
			opacity: .6,
			placeholder: 'placeholder',
			revert: 250,
			tolerance: 'pointer',
			toleranceElement: '> div',
			stop: function() {
                var $this = $(this);
                var gridName = $this.closest('[data-mesour-grid]').attr('data-mesour-grid');
				$.get(mesour.getUrlWithParam(gridName, $this.attr("data-sort-href"), 'sortable', 'sortable_data', $this.nestedSortable('serialize')), function(data) {
					
				});
			},
			start: function(a, b) {
				$(b.placeholder).css('height', $(a.toElement).closest('li').outerHeight());
			}
		});
	});
})(jQuery);