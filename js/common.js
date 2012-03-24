jQuery(function($) {
	$('.browser-title a').on("click", function(){
		$(this).blur().parent().siblings('.browser-version').fadeToggle().parent().toggleClass('browser-block-active');
		return false;
	});
});