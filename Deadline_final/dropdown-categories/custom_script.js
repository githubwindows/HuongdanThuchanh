jQuery(document).ready(function($) {

	$("ul.ct_dropdown > li > a").click(function(e){
	e.preventDefault();
	if($(this).parent().children('.children').is(':visible')){
		$(this).parent().children('.children').fadeOut();
	}
	else{
		$(this).parent().children('.children').fadeIn();
	}
	});

});