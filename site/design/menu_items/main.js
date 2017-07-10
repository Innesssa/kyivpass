var sliderTimeout;
var sliderObj;

var posCart = null;

function sliderAutoplay(){
    sliderObj.trigger( 'nextSlide' );
    setTimeout( sliderAutoplay, 3000 );
}
// pause slider function
function sliderPause(){
    clearTimeout( sliderTimeout );
}


$(document).ready(function(){
    $('.menu > li').hover(
        function(){
            $('.menu').find('ul').hide();
            $(this).find('ul').show();
            $(this).addClass('active');
            $(this).find('a').first().addClass('active');
        },
        function(){
            $('.menu').find('ul').hide();
            $(this).removeClass('active');
            $(this).find('a').first().removeClass('active');
        }
    )
    
    
    // initialize slider
    sliderObj = $( '.slider' ).lemmonSlider({
        //'slideToLast' : true,
        infinite: true
    });
    setTimeout( sliderAutoplay , 3000 );
    
    var wereItems 	= $('form.wareItem').cartItemFn();
    var cartWidget 	= $('.user-menu .cart').cartWidgetFn();
    
    if(posCart === null){
    	var options = {
    		'cartWidgetFn' 	: cartWidget,
    		'wareItems' 	: wereItems
    	}
    	posCart = new cart(options);
    }
    
    //
    
    
    //sliderObj.trigger( 'nextSlide' );
    //setTimeout( function(){}, 3000 );
    //sliderObj.trigger( 'nextSlide' );
    // run
    //sliderAutoplay();

})