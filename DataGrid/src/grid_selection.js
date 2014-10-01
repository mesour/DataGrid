var native_confirm = true;
var gridConfirm = function(message) {
	return confirm(message);
};
var gridCustomSend = function(url, data) {
	$('#confirm-modal .yes').data('confirm-post-data', data);
};
(function($) {
	var options = {
		mainCheckboxClass: 'main-checkbox',
		checkboxButtonIdent: '#checkbox-button',
		checkboxSelectorIdent: '#checkbox-selector',
		selectCheckboxClass: 'select-checkbox',
		isUnactiveClass: 'is-unactive',
		isActiveClass: 'is-active',
		confirmDataKeyName: 'confirm-post-data',
		dropDownMenuClass: 'dropdown-menu'
	};
	var buttonToNoChecked = function(button) {
		button.removeClass('any-checked checked btn-warning');
		button.addClass('btn-default');
		button.html('&nbsp;&nbsp;&nbsp;&nbsp;');
	};
	var buttonToChecked = function(button) {
		button.addClass('checked btn-warning');
		button.removeClass('any-checked btn-default');
		button.html('<b class=\"glyphicon glyphicon-ok\"></b>');
	};
	var buttonToAnyChecked = function(button) {
		button.addClass('any-checked btn-warning');
		button.removeClass('checked btn-default');
		button.html('<b class=\"glyphicon glyphicon-minus\"></b>');
	};

	$(document).ready(function() {
		var $tbody = $('.' + options.mainCheckboxClass).closest('.data-grid').find('tbody, ul');
		var toggleCheckbox = function($this) {
			var button = $(this).find('.' + options.selectCheckboxClass);
			if ($($this).hasClass('checked')) {
				buttonToNoChecked(button);
			} else {
				buttonToChecked(button);
			}
		};
		var checkAllCheckboxes = function(no_main) {
			var check_all = true;
			var one_checked = false;
			$tbody.find('tr, li').each(function() {
				if (!$(this).find('.' + options.selectCheckboxClass).hasClass('checked'))
					check_all = false;
				else
					one_checked = true;
			});

			if (!no_main) {
				if (check_all) {
					buttonToChecked($('.' + options.mainCheckboxClass));
				} else {
					if(one_checked)
						buttonToAnyChecked($('.' + options.mainCheckboxClass));
					else
						buttonToNoChecked($('.' + options.mainCheckboxClass));
				}
			}
			if (one_checked)
				$(options.checkboxButtonIdent + ' button').removeClass('disabled').closest('div').css('cursor', 'pointer');
			else
				$(options.checkboxButtonIdent + ' button').addClass('disabled').closest('div').css('cursor', 'not-allowed');
		};
		$('#checkbox-selector .dropdown-toggle').find('a.btn').off('click.grid');
		$('.' + options.mainCheckboxClass).off('click.grid');
		$(options.checkboxSelectorIdent + ' ul.' + options.dropDownMenuClass + ' li a').off('click.grid');
		$(options.checkboxButtonIdent + ' ul li a').on('click.grid');
		$('#checkbox-selector .dropdown-toggle').off('click.grid, mouseenter.grid, mouseleave.grid');
		$('#checkbox-selector .dropdown-toggle').find('a.btn').off('click.grid, mouseenter.grid, mouseleave.grid');
		$('.select-checkbox').off('click.grid');
		$(window).off('click.grid');
		$('.data-grid').find('td').off('click.grid');

		$('.' + options.mainCheckboxClass).on('click.grid', function(e) {
			e.stopPropagation();
			var $this = $(this);
			$tbody.find('tr, li').each(toggleCheckbox, $this);
			checkAllCheckboxes(true);
		});
		$(options.checkboxSelectorIdent + ' ul.' + options.dropDownMenuClass + ' li a').on('click.grid', function(e) {
			e.preventDefault();
			switch ($(this).attr('data-select')) {
				case 'active':
					$tbody.find('tr, li').each(function() {
						if ($(this).find('.' + options.isActiveClass).is('*'))
							buttonToChecked($(this).find('.' + options.selectCheckboxClass));
						else
							buttonToNoChecked($(this).find('.' + options.selectCheckboxClass));
					});
					break;
				case 'unactive':
					$tbody.find('tr, li').each(function() {
						if ($(this).find('.' + options.isUnactiveClass).is('*'))
							buttonToChecked($(this).find('.' + options.selectCheckboxClass));
						else
							buttonToNoChecked($(this).find('.' + options.selectCheckboxClass));
					});
					break;
				case 'inverse':
					$tbody.find('tr, li').each(function() {
						var $checkbox = $(this).find('.' + options.selectCheckboxClass);
						if ($checkbox.hasClass('checked'))
							buttonToNoChecked($checkbox);
						else
							buttonToChecked($checkbox);
					});
					break;
			}
			checkAllCheckboxes();
		});
		$(options.checkboxButtonIdent + ' ul li a').on('click.grid', function(e) {
			e.preventDefault();
			var data = [];
			$tbody.find('.' + options.selectCheckboxClass + '.checked').each(function() {
				data.push($(this).attr('data-value'));
			});
			if (data.length > 0) {
				var isConfirm = $(this).attr('data-confirm');
				if(native_confirm && isConfirm) {
					if(gridConfirm(isConfirm)) {
						$.post($(this).attr('href'), {selected: data});
					}
				} else if(isConfirm) {
					gridCustomSend($(this).attr('href'), {selected: data});
				} else {
					$.post($(this).attr('href'), {selected: data});
				}
			}

		});
		var inCheckbox = false, inButton = false;
		$('#checkbox-selector .dropdown-toggle').on({
			'click.grid': function(e) {
				e.preventDefault();
				if(inCheckbox) {
					return;
				}
				var $group = $(this).closest('.btn-group');
				$group.addClass('open');
			},
			'mouseenter.grid': function() {
				inButton = true;
			},
			'mouseleave.grid': function() {
				inButton = false;
			}
		});
		$('#checkbox-selector .dropdown-toggle').find('a.btn').on({
			'click.grid': function(e) {
				e.preventDefault();
				var $this = $(this);
				if ($this.hasClass('checked')) {
					buttonToNoChecked($this);
				} else {
					buttonToChecked($this);
				}
			},
			'mouseenter.grid': function() {
				inCheckbox = true;
			},
			'mouseleave.grid': function() {
				inCheckbox = false;
			}
		});
		var checkButton = function($this) {
			if ($this.hasClass('checked')) {
				buttonToNoChecked($this);
			} else {
				buttonToChecked($this);
			}
			checkAllCheckboxes();
		};
		$('.' + options.selectCheckboxClass).on('click.grid', function(e) {
			e.preventDefault();
			var $this = $(this);
			checkButton($this)
		});
		$(window).on('click.grid', function() {
			if (!inButton)
				$('#checkbox-selector').removeClass('open');
		});
	});
})(jQuery);