jQuery(function($) {

	$('.browser-title a').on('click', function(){
		$(this).blur().parent().next('.browser-block').fadeToggle('fast');
		return false;
	});
	
	$('.history table td, .history table th').on({
		mouseenter: function () {
			var $td = $(this)
			var n = $td.index() + 1;
			$td.parents('table').find('td:nth-child(' + n + '), th:nth-child(' + n + ')').addClass('highlight');
		},
		mouseleave: function () {
			var $td = $(this)
			var n = $td.index() + 1;
			$td.parents('table').find('td:nth-child(' + n + '), th:nth-child(' + n + ')').removeClass('highlight');
		}
	});
    
});
