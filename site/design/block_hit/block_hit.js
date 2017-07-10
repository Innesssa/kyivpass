$(function () {
	
	$('#block_hit').scrollbox({
	    direction: 'h',
	    distance: 134
	});
	$('#block_hit-backward').click(function () {
	    $('#block_hit').trigger('backward');
	});
	$('#block_hit-forward').click(function () {
	    $('#block_hit').trigger('forward');
	});
	
});
