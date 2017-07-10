$(function(){
    $(".for_button button.order").live('click', function(e){
        var sender = $(this), product_id = sender.parent('.for_button').attr('id');
        _gaq.push(['_trackEvent', 'Logo', 'LogoOrder', 'LogoOrderBut']);
        $.extended_post("/catalog/logotype.ajax", {product_id: product_id}, function(data){
            var html = $(data),
            popup = global.popup.append({
                content_element: $("#logotype_popup"),
                opener_element: sender,
                dont_bind: true,
                center: true,
                popup_shade: true
            });

            html.find('ins.popup_close').click(function(e){
                popup.close();
                e.preventDefault();
            });


            html.find('#qty_4')
                .keydown(function (evt)
    			    {
    			    global.checkQtyKey(this, evt);
    			    })
			    .keyup(function()
    			    {
    				if (/^\d/.test($(this).val()))
    				    $(this).val($(this).val().replace(/^0(\d)/, '$1'));

    				test_func();
    				})
			    .blur(function()
    			    {
    				var value = parseInt($(this).val());
    				if (!value)
    				    $(this).val(0);

    				test_func();
    				return true;
    			    });

            html.find("#logotypeForm").validate({
                rules: {
                    name: {required: true},
                    company_name: {required: true},
                    email: {required: true, email: true},
                    phone: {masked_mobile: true},
                    contract_id: {required: true }
                },
                messages: {
                    name: {required: 'Необходимо заполнить поле'},
                    company_name: {required: 'Необходимо заполнить поле'},
                    email: {required: 'Обязательное поле', email: 'Введите правильный email'},
                    phone: {masked_mobile: "Укажите корректный телефон"},
                    contract_id: { required: "Пожалуйста, укажите номер договора" }
                },
                errorElement: 'div',
                submitHandler: function(form) {
                    _gaq.push(['_trackEvent', 'Logo', 'LogoOrder', 'LogoOrderSuccess']);
                    _gaq.push(['_trackPageview', '/LogoOrderPlacedSuccess']);
                    if($(form).valid())
                        setTimeout(function(){
                            form.submit();
                        }, 200);
                }
            });

            html.find("#logo_no_contract").on('click', function(e){
                html.find("#div_logo_contract").toggle();
            });

            html.find("#logo_phone").mask("+7(999)999-99-99");
            global.block_qty_input_field = false
            global.qty_input_field();
            html.show();
            $("#logotype_popup").remove();
            $("#layout").append(html);
            popup.element = $("#logotype_popup");
            popup.element.click(function(e){ e.stopPropagation(); });
            popup.open(sender);
        }, null, null, {dataType: 'html'});
        e.preventDefault();
    });
});