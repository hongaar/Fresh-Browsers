jQuery(function($) {
	$('.browser-title a').on("click", function(){
		$(this).parent().siblings().fadeToggle().parent().toggleClass('browser-block-active');
		return false;
	});
});