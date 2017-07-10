$(document).ready(function(){

	$(".btn_slide_admin_panel").click(function(){
		$("#slide_admin_panel").slideToggle("slow");
		$(this).toggleClass("active"); return false;
	});
	
	 
});
