$(function () {
	//script for feedback_popups
	$('a#link_feedback').click(function () {
		$('div.form_feedback').fadeIn(500);
		$("body").append("<div id='overlay'></div>");
		$('#overlay').show().css({'filter' : 'alpha(opacity=80)'});
		return false;				
	});	
	$('a.feedback_close').click(function () {
		$(this).parent().fadeOut(100);
		$('#overlay').remove('#overlay');
		return false;
	});


$("#feedback_form").submit(function(event) {

/* отключение стандартной отправки формы */
  event.preventDefault();

  /* собираем данные с элементов страницы: */
  var $form = $( this ),
      feedback_name = $form.find( 'input[name="feedback_name"]' ).val(),
      feedback_phone = $form.find( 'input[name="feedback_phone"]' ).val(),
      feedback_email = $form.find( 'input[name="feedback_email"]' ).val(),
      feedback_mess = $form.find( '#feedback_mess' ).val(),
          
      url = $form.attr( 'action' )+'?display=ajax';
  
  /* отправляем данные методом POST */
  var posting = $.post( url, { fn: feedback_name, fp: feedback_phone, fe:  feedback_email, fm: feedback_mess} );

  /* результат помещаем в div */
  posting.done(function( data ) {
    $( "#feedback_form" ).html(data);
  });

});

});
