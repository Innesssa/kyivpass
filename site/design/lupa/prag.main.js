var global = {
	load_data_format: function(data) {
		var url = document.location.href.replace(/^https?:\/\/[^\/]+/, '');
		return data.replace(new RegExp('<a [^>]*href="' + url + '"[^>]*>(.*?)<\\/a>', 'g'), '<b class="selected">$1</b>').replace(new RegExp ('(<a [^>]*href="([^"]+)"[^>]*)>(.*?)<\\/a>', 'g'), function (str, s1, s2, s3) {if (url.match(new RegExp('^' + s2))) return s1 + ' class="selected">' + s3 + '</a>'; else return str;}).replace(/<\/?noindex[^>]*>/gi, '');
	    },

	selectors: function(element)
        {
		var t = this;
		t.items = element.find('.selectors .selector');
		t.items.click(function(){
			if ($(this).is('.selected')) return false;
			t.tab_hide = t.items.filter('.selected').attr('class').replace(/.*selector_(\S+).*/, '$1');
			t.items.filter('.selected').removeClass('selected');
			t.tab_show = $(this).attr('class').replace(/.*selector_(\S+).*/, '$1');
			$(this).addClass('selected');
			$('.tab_'+t.tab_hide).fadeOut(300, function(){
				$('.tab_'+t.tab_show).fadeIn(500);
                });
            });

        var sel;
        if (t.items.filter('.selected').size())
            sel = t.items.filter('.selected');
        else
            {
            sel = t.items.eq(0);
            sel.addClass('selected');
            }
        if(sel.size())
            {
            var tab_show = sel.attr('class').replace(/.*selector_(\S+).*/, '$1');
            $('.tab_' + tab_show).fadeIn(500);
            }
	    },

	checkQtyKey: function(eInput, evt)
        {
		var bWrongChar = true;
		var iKeyCode = evt.keyCode;
		var sType = evt.type;
		var bKeypress = (sType == 'keypress');
		var bCtrlKey = evt.ctrlKey || evt.metaKey;
		var bShiftKey = evt.shiftKey;
		if (iKeyCode == 0
            || iKeyCode == 8 || iKeyCode == 9 || iKeyCode == 12 || iKeyCode == 16
            || iKeyCode == 17 || iKeyCode == 18 || iKeyCode == 33 || iKeyCode == 34 || iKeyCode == 35
            || iKeyCode == 36 || iKeyCode == 37 || iKeyCode == 38 || iKeyCode == 39 || iKeyCode == 40
            || (iKeyCode == 46 && !window.opera) || (iKeyCode > 47 && iKeyCode < 58)
            || (iKeyCode > 95 && iKeyCode < 106 && !bKeypress) || iKeyCode == 91 || iKeyCode == 224)
            bWrongChar = false;
		if ((bCtrlKey && iKeyCode == 65) || (bCtrlKey && iKeyCode == 67) || (bCtrlKey && iKeyCode == 86)
            || (bCtrlKey && iKeyCode == 88))
            bWrongChar = false;
		if ((bShiftKey && (iKeyCode > 47 && iKeyCode < 58)))
            bWrongChar = true;
		if (bWrongChar)
            {
			evt.returnValue = false;
			evt.cancelBubble = true;
			if (evt.preventDefault) evt.preventDefault();
			return false;
		    }
		return true;
	    },

	popup: {
		active: null,
		shade: null,
		append: function (params) { return new this.item(params); },
		item: function (params) { this.init(params); }
	},

	dashboard_setting: {
		init: function(){
			if (!$('.dashboard_setting_link').size()) return;
			$('.dashboard_setting_link').click(function(){
				$('.dashboard_setting_link').hide();
				$('.dashboard').css({left: -10});
				$('.dashboard_setting_popup').show();
			});
			$('.dashboard_setting_popup .popup_close').click(function(){
				$('.dashboard_setting_popup').hide();
				$('.dashboard').css({left: 0});
				$('.dashboard_setting_link').show();
			});

			$('.color_chooser_item').click(function(e){
                e.preventDefault();
				if ($(this).is('.selected')) return false;
				var st = $(this).data('color');
				$('.dashboard_setting_popup').find('.selected').removeClass('selected');
				$(this).addClass('selected');
				var c = document.getElementById("dashboard_style");
				var h = c.parentNode;
				h.removeChild(c);
				c = document.createElement("link");
				c.id = "dashboard_style";
				c.type = "text/css";
				c.rel = "stylesheet";
				c.href = '/public/css/color_' + st + '.css';
				h.appendChild(c);
                $.cookie("color", st, { expires: 365*10 } ); //set for 10 years

                
				return false;
			});
		}
	},

	authorization: {
		init: function(){
			if (!$('.login_link').size()) return;
			window.popup_auth = global.popup.append({
				content_element: $('.authorization_popup'),
				opener_element: $('.login_link'),
				popup_shade: false,
                onOpen: function() {
                    _gaq.push(['_trackEvent', 'Auth', 'Win', 'WinOpen']);
                }
			});
		}
	},

	add_new_items_popup:{
		init: function(){
			if (!$('.add_new_items_link').size()) return;
			global.popup.append({
				content_element: $('.add_new_items_popup'),
				opener_element: $('.add_new_items_link'),
				popup_shade: false
			});
		}
	},

    navigation: {
        init: function() {
            if(!$('#path .directory_link').size())
                return;
            global.popup.append({
                content_element: $('.directory_popup'),
                opener_element: $('.directory_link'),
                popup_shade: true
            });

            var parts = [], selected = null;

            function show (item) {
                var path = item.attr('rel');
                function select() {
                    $('.directory_popup .level2').html(parts[path]);
                    if (selected)
                        selected.removeClass('selected');
                    selected = item;
                    selected.addClass('selected');
                }
                if(parts[path])
                    select();
                else {
                    $.ajax({
                        type: "POST",
                        url: path,
                        data: {random:1}, // megabugfix! TODO: pekopt: refactor to GET request
                        beforeSend: function() {
                            $('.directory_popup .level2').html('<div class="loading"></div>').show();
                        },
                        success: function(data) {
                            parts[path] = global.load_data_format(data);
                            select();
                        },
                        error: function() {
                            $('.directory_popup .level2').hide();
                        }
                    })
                }
            } //show

            $('.directory_popup .level1 .pseudo_link').click(function(e){
                show($(this));
                return false;
            });

            if($('.directory_popup .level1 .selected').size())
                $('.directory_popup .level1 .selected').click();

            } //init
        }, //navigation

	qty_input_field: function()
        {
        if(this.block_qty_input_field)
            return;
        this.block_qty_input_field = true;

        $('.qty_input_field').each(function()
		    {
			var t = $(this);
			var inp = t.find('input.qty_input');

			// блокировка дрыгалок, например на уменьшение, когда колво и так 1
			function test_func()
			    {
				if (inp.val() < 2)
				    t.find('.qty_less').addClass('disabled_less');
				else
				    t.find('.qty_less').removeClass('disabled_less');

				setTimeout(function()
				    {
				    if(typeof(card_calc) == 'undefined')
				        return;
				    card_calc.recalc_sum();
				    }, 50);
				}

			// ограничение на ввод в поле количество тока цифр, ну и испраление
			//  всякие там, тип 01 на 1
			inp
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

            // обработчики дрыгалок, меняющих количество
			t.find('.qty_more').click(function()
			    {
			    inp.val(parseInt(inp.val()) + 1);
			    test_func();
			    });

			t.find('.qty_less').click(function()
			    {
				var qty = parseInt(inp.val());
				if (qty == 1)
				    return;
				else if (qty < 1)
				    {
				    inp.val(0);
				    return false;
				    }
				inp.val(qty-1);

				test_func();
			    });
		    });
	   },

    small_basket_update: function(params, sender)
        {
        if(!params.success)
            return false;

        if(!params.qty)
            {
            $('#mini-basket .l-summary').html("Корзина пуста");
            $('#mini-basket .l-pd').hide();
            }
        else
            {
            $('#mini-basket .l-summary').html(prettyCount(params.qty, ['товар']) + '<br />' + prettyPrice(params.sum));
            $('#mini-basket .l-pd').toggle(!!(params.pd_overlimit > 0));
            if(params.pd_overlimit)
                $('#small_basket_popup .sum').html(prettyPrice(params.pd_overlimit));
            if(params.items)
                basket = params.items;

            if(sender)
                sender.replaceWith("<div><a href='/basket/'>Товар уже добавлен в корзину</a></div>").unbind('click');
            }
        return true;
        }
}; // global ?


global.popup.item.prototype = {
	init: function (params){
		var t = this;
		if (!params)
            params = {};
		if (!params.parent_element)
            params.parent_element = $('#layout');
		t.element = params && params.element ? params.element : t.create (params);
		if (params.popup_shade)
            t.popup_shade = true;
		t.element.hide();
        this.params = params;
		if (params && params.opener_element) {
            //to bind live() event need to pass live object into params
            //  with structure {'parent': __selector__, 'el': __selector__ } where parent && el - Strings
            //  parent needs to be static non-ajax-loaded dom node
            if(params && params.live) {
                $(params.live.parent).on('click', params.live.el, function(e) {
                    if ($.preventDefaultEvent(e)) return;
                    if (t.opened) t.close(); else t.open(this);
                    if(params.onClick && typeof(params.onClick) == 'function') {
                        params.onClick.call(this)
                    }
                    return false;
                });
            } else if(!params.dont_bind) {
                params.opener_element.on('click', function(e) {
                    if($.preventDefaultEvent)
                        if ($.preventDefaultEvent(e))
                            return;
                    if (t.opened)
                        t.close();
                    else
                        t.open(this);

                    if(params.onClick && typeof(params.onClick) == 'function') {
                        params.onClick.call(this)
                    }
                    return false;
                });
            }
		}
		this.element.find('.popup_close').on('click', function(){
            t.close();
        });
		this.element.click(function(){ global.popup.click_on_active = true; });
	},

	create: function(params) {
		var popup_box;
		if (params && params.content_element) popup_box = params.content_element.addClass('popup');
		else popup_box = $('<div class="popup" />');
		if (params.popup_shade && !global.popup.shade) {
			global.popup.shade = $('<div class="popup_shade" />');
			global.popup.shade.appendTo(params.parent_element);
			global.popup.shade.hide();
		}
		popup_box.prepend('<ins class="popup_close" title="Закрыть"></ins>').wrapInner('<div class="popup_reducer"/>').appendTo(params.parent_element);
		return popup_box;
	},

	open: function(opener_element){
        var that = this;
		if (global.popup.active && global.popup.active != this)
            global.popup.active.close();
        if (opener_element)
            {
			this.element.css({top: $(opener_element).offset().top - 1});
			var elW = this.element.width()
                + parseInt(this.element.css("padding-left"),0)
                + parseInt(this.element.css("padding-right"),0)
                + parseInt(this.element.css("margin-left"),0)
                + parseInt(this.element.css("margin-right"),0);
			if (($(opener_element).offset().left-1+elW) > document.body.offsetWidth)
				this.element.css({left : document.body.offsetWidth-elW-1});
			else
				this.element.css({left : $(opener_element).offset().left - 1});
		    }
		
		if(this.popup_shade)
            global.popup.shade.show();

        this.element.fadeIn();

        // centering div onload
        if(this.params && this.params.center) {
            that.element.centerDiv({fixed: this.params.fixed});
            if (!this.params.fixed) {
                $(window).resize(function(){that.element.centerDiv()});
                $(window).scroll(function(){that.element.centerDiv()});
                this.element.resize(function(){that.element.centerDiv();});
                setTimeout(function(){
                    $(window).trigger('resize');
                }, 100);
            }
        }

        // fix position if window out of the window by height&top values
        if(this.params && !this.params.center && !this.params.fixed)
            $(window)
                .resize(function() {
                    var offset = that.element.offset();
                    if($(window).height() < (that.element.outerHeight() + offset.top - $(window).scrollTop()))
                        that.element.stop(true, true)
                            .animate({top: $(window).height()-that.element.outerHeight()+$(window).scrollTop()});
                })
                .trigger('resize');

        if(this.params && this.params.onOpen && typeof(this.params.onOpen) == 'function')
            this.params.onOpen.call(this);

		global.popup.active = this;
		this.opened = true;
	},

	close: function(){
        if (global.popup.shade)
            global.popup.shade.hide();
		if($.browser.msie)
            this.element.hide();
        else
            this.element.fadeOut();
		global.popup.active = null;
		this.opened = false;
        if(this.params && this.params.onClose && typeof(this.params.onClose) == 'function') {
            this.params.onClose.call(this)
        }
	}
};

$(document).click(function() {
    if(global.popup.click_on_active)
        global.popup.click_on_active = false;
    else if(global.popup.active && !$('#loading_block').is(':visible'))
        global.popup.active.close();
});

$(document).keydown (function(e) {
    if(e.keyCode === 27 && global.popup.active)
        global.popup.active.close();
});

$(document).ready(function() {
    // fix label click in ie < 9
    // ето ад, сам признаю
    if ($.browser.msie && parseInt($.browser.version) < 9) {
        window._msie8_labels_click_emulate = {};
        var labels = $('label'), ignore, rel;
        if (labels.size())
            labels.each(function(){
                var sender = $(this);
                rel = sender.attr('for');
                if (rel) {
                    ignore = false;
                    if (rel in _msie8_labels_click_emulate) {
                        if (_msie8_labels_click_emulate[rel][1] < 5){
                            _msie8_labels_click_emulate[rel][0] = Date.now();
                            ++_msie8_labels_click_emulate[rel][1];
                        }
                        else
                            if (Date.now() - _msie8_labels_click_emulate[rel] < 200)
                                ignore = true;
                    }
                    else
                        _msie8_labels_click_emulate[rel] = [+new Date(), 1];

                    if (!ignore)
                        sender.click(function(){
                            $('#' + rel).click();
                            return true;
                        });
                }
            });
    }

    // Иконки в левом верхнем углу товаров каталога
    var product_icons = $('.set-product-icon');
    if((window.location.pathname.match(/^\/catalog\//) || window.location.pathname.match(/^\/search\//) || window.location.pathname == '/')
            && product_icons.size()) {
        function make_prod_icon_img(sender, img, index, size) {
            img = $('<img class="product_icon" src="/public/img/{img}" style="position: absolute;z-index:2;" />'
                .replace(/\{img\}/, img));
            sender.after(img);
            setTimeout(function(){
                var sender_offset = sender.offset();
                img
                    .offset({top:sender_offset.top + (0.75 * size * index), left:sender_offset.left})
                    .css('z-index', 2 + index);
            }, 200);
        }

        product_icons.each(function(){
            var sender = $(this), size, count = -1;

            if (70 < sender.width() && sender.width() < 290)
                size = 55;
            else if(sender.width() > 290)
                size = 105;
            else
                return;

            if (sender.hasClass('set-pre-order-icon'))
                make_prod_icon_img(sender, 'prod_pre_order_icon_' + size + '.png', ++count, size);
            if (sender.hasClass('set-action-icon'))
                make_prod_icon_img(sender, 'prod_action_icon_' + size + '.png', ++count, size);
            if (sender.hasClass('set-eco-icon'))
                make_prod_icon_img(sender, 'prod_eco_icon_' + size + '.png', ++count, size);
            if (sender.hasClass('set-new-icon'))
                make_prod_icon_img(sender, 'prod_new_icon_' + size + '.png', ++count, size);
            if (sender.hasClass('set-sale-icon'))
                make_prod_icon_img(sender, 'prod_sale_icon_' + size + '.png', ++count, size);
        })
    }

    // retail send_stat
    if ($('#retail_block').size()) {
        try { 
            rrApi.recomTrack(
                    $(this).find('#retail_method_id').val(),
                    $(this).find("#retail_item_id").val(),
                    $(this).find("retail_show_id").val());
        } catch(e) {}
    }

    $('button.buy_unauthorized').click(function(){
        console.log('asd');
        var sender = $(this);
        _gaq.push(['_trackEvent', 'But', 'EnterNBuy', 'Everywhere']);
        setTimeout(function(){
            window.popup_auth.open(sender);
        }, 100);
        return false;
    });

    $.validator.addMethod("prag_phone", function(value){
        if(!value.length) 
            return true;
        return ((/^8\d{10}$/).test(value));
        }, "Укажите корректный номер телефона <br />(маска: 8хххххххххх)");
    $.validator.addMethod("prag_mobphone", function(value){
        if(!value.length) 
            return true;
        return ((/^7\d{10}$/).test(value));
        }, "Укажите корректный номер телефона <br />(маска: 7хххххххххх)");
    $.validator.addMethod("prag_mail", function(value){
        if(!value.length) 
            return true;
        return ((/^[a-z0-9._-]+@[a-z0-9.-]+\.[a-z]{2,4}$/i).test(value));
        }, "Укажите корректный адрес электронной почты <br />(маска: name@server.domain)");
    $.validator.addMethod("prag_zip", function(value){
        if(!value.length) 
            return true;
        return ((/^\d{6}$/).test(value));
        }, "Укажите корректный почтовый индекс<br />(пример: 123456)");
    $.validator.addMethod("prag_req", function(value,el){
        if(value == $(el).attr('placeholder'))
            return false;
        return value.length;
        }, "Поле обязательно для заполнения");
    $.validator.addMethod("non_cyrillic", function(value){
        if(!value.length)
            return true;
        return !/[а-я]+/i.test(value);
    }, "Поле не должно содержать кириллические символы");

    $.validator.addMethod("single_tire", function(value){
        if(!value.length)
            return true;
        return value.replace(/[^-]/g, '').length <= 1;
    }, "Поле не должно содержать больше одного тире!");

    $.validator.addMethod("single_word", function(value){
        if(!value.length)
            return true;
        return /^[a-zа-я-]+$/i.test(value);
    }, "Поле не должно содержать пробелы, цифры и спецсимволы");

    $.validator.addMethod("masked_mobile", function(mobile, element) {
        if (mobile == '+_(___)___-__-__')
            return false;
        return this.optional(element) || mobile.match(/\+\d{1}\(\d{3}\)[\d-]{9}/);
        }, "Укажите корректный Номер телефона для оповещения SMS");

    // Authorization
    if($('.dashboard_setting_link').size())
        global.dashboard_setting.init();
    if($('.login_link').size())
        global.authorization.init();
    
    // TODO: ето какой-то пиздец, просто слов нет
    setTimeout(function ()
        {
        if($('.add_new_items_link').size())
            global.add_new_items_popup.init();  
        }, 300);

    if($('#callMeAjax').size())
        {
        $('#callMeAjax').submit(function()
            {
            var name = $("#callme_name").val();
            var phone = $("#callme_phone").val();
            $.post("/export/index.php", {callme_name: name, callme_phone: phone, add: "callMe"});
            global.popup.active.close();
            return false;
            })
        }

    // Path
    if ($('#path').size())
        {
        $('#path .path_item').each(function(i)
            {
            $(this)
                .animate({top:'-1.6em'}, 600+(240*i), function(){$(this).prev().css({visibility: 'visible'});})
                .animate({top:'0'}, 240)
                .animate({top:'-0.3em'}, 120)
                .animate({top:'0'}, 60);
            });
        global.navigation.init();
        }

    // Online Consultant
    if ($('.online_consultant_link').size())
        {
        $('.online_consultant_link').click(function(e){
            if($.preventDefaultEvent(e)) return;
            window.open(this.href, 'RecipeWindow', 'width=560,height=400,toolbar=1,location=1,directories=1,menubar=1,scrollbars=1,resizable=1,status=1');
            return false;
        });
    }

    // Banner Wide
    if($('.banner_wide').size())
        $('.banner_wide .banner').css('background-image', 'url('+$('.banner_wide .banner img').attr('src')+')');
    
    // Items Grid Preview
    $('.items_grid').each(function()
        {
        $(this).find('.item .preview').not('.next .preview').each(function()
            {
            var padding, height ,img;
            if($(this).is(':visible'))
                padding = parseInt(($(this).find('.reducer').height()-$(this).find('img').height())/2);
            else
                {
                if(jCommon.browser.msie)
                    {
                    img = $(this).find('img').clone();
                    $('#layout').append(img);
                    height = img.height();
                    img.remove();
                    }
                else
                    height = parseInt($(this).find('img').attr('height'));
                padding = parseInt((parseInt($(this).find('.reducer').css('height'))-height)/2);
                }
            $(this).find('.reducer div').css('padding-top', padding + 'px');
            });
        });

    // обработчик на кнопу "в корзину" для представления списком
    if($('.items_list').size())
        {
        $('.retail_block a').click(function(){
            try { rrApi.recomMouseDown($(this).parents('tr.sortable').attr('id'),$('#retail_method_id').val()) } catch (e) {}
            console.log($(this).parents('tr.sortable').attr('id'));
        });

        $('.items_list .for_button button.button').click(function(){
            // если юзер незалогинен
            if($('.button.buy_unauthorized').size())
                return true;

            if ($(this).parents('.retail_block').size()) {
                try {rrApi.recomAddToCart($(this).parents('tr.sortable').attr('id'), $('#retail_method_id').val())} catch (e) {};
            }

            var line = $(this).parents('tr.sortable');

            var sender = $(this);
            $.extended_post('/basket/add.json', {
                id: line.attr('id'),
                qty: line.find('.qty_input').val()
                }, function(resp) {
                    if(!resp.success)
                        return;
                    global.small_basket_update(resp, sender);
                    animateBasketAdd(line.find(".preview a img"));
                    try { rrApi.addToBasket(line.attr("id")) } catch(e) {}
                }, function(){
                    sender.removeAttr('disabled');
                });
            sender.attr('disabled', 'disabled');
            return true;
            });
        }

    // Item Popup
    if($('.items_grid').size())
        {
        var item_popup_timer, item, item_popup_el = '<div class="popup item_popup"><div class="popup_container"></div></div>';
        $('.items_grid .item').not('.adver, .next').find('.r').hover(
            function()
                {
                global.item_popup_item = $(this).parent();
                item_popup_timer = setTimeout(function(){global.item_popup()}, 100);
                },
            function()
                {
                if(item_popup_timer)
                    clearTimeout(item_popup_timer);
            });
        
        $('.items_grid .adver').find('.r').hover(
            function()
                {
                global.item_popup_item = $(this).parent();
                item_popup_timer = setTimeout(function(){global.item_popup()}, 100);
                $(this).parent().addClass('hover');
                },
            function()
                {
                $(this).parent().removeClass('hover');
                if(item_popup_timer)
                    clearTimeout(item_popup_timer);
            });
        }

    global.item_popup = function() 
        {
        if(global.block_popup)
            return;
        global.block_popup = true;
        var item = global.item_popup_item;
        item.parent().find('.item_popup').remove();
        item.parent().append(item_popup_el);
        var item_popup = item.parent().find('.item_popup');
        
        if(item.hasClass('adver'))
            item_popup
                .css('border', 'none')
                .css('box-shadow', 'none')
                .css('-moz-box-shadow', 'none')
                .css('-o-box-shadow', 'none')
                .css('-webkit-box-shadow', 'none')
                .find('.popup_container').addClass('adver');
        
        item_popup.find('.popup_container')
            .append(item.find('.r').html())
            .css({width: item.find('.r').width()});
        
        item_popup
            .css({
                top: item.offset().top - item.parent().offset().top - 1, 
                left: item.offset().left - item.parent().offset().left - 1, 
                display: 'block'})
            .mouseleave(function(){$(this).remove();})
            .fadeIn(200, function()
                {
                item_popup.find('.summary').slideDown(100);
                item_popup.find('.code,.price,.for_button').show();
                item_popup.find('.title span').toggle();
                if(item_popup.find('.delimeter_icon').attr('src'))
                    item_popup.find('.delimeter_icon').attr('src', item_popup.find('.delimeter_icon').attr('src').replace('ico_small', 'ico_big'));
                if(item_popup.find('.logotype_info').size()) {
                    item_popup.find('.logotype_info').show();
                }
                if($.inArray(parseInt(item_popup.find('.for_button:first').attr('id')), basket) != -1 && !item_popup.find('.logotype_info').size()) {
                    item_popup.find('.for_button:first').replaceWith("<div><a href='/basket/'>Товар уже добавлен в корзину</a></div>")
                }

                item_popup.find('.r_inner a').click(function() {
                    var item_id = $(this).parents('.r_inner').find('.for_button:first').attr('id');
                    if ($(this).parents('#retail_block').size() && item_id) {
                        try { rrApi.recomMouseDown(item_id, $('#retail_method_id').val()) } catch (e) {}
                    }
                });

                item_popup.find('.for_button button.button').click(function()
                    {
                    var sender = $(this);

                    if (sender.parents('#retail_block').size()){
                        try {rrApi.recomAddToCart(sender.parents('.for_button:first').attr('id'), $('#retail_method_id').val())} catch (e) {};
                    }
                    // если юзер незалогинен
                    if($('button.buy_unauthorized').size())
                        {
                        setTimeout(function(){
                            window.popup_auth.open(sender);
                        }, 100);
                        return false;
                        }

                    $.extended_post('/basket/add.json',
                        {id: sender.parents('.for_button:first').attr('id'), qty: 1 },
                        function(resp)
                            {
                            if(!resp.success)
                                return;
                            global.small_basket_update(resp, sender);
                            animateBasketAdd(item_popup.find(".reducer img"));
                            sender.parent()
                                .css('height', sender.offsetHeight)
                                .html("<div><a href='/basket/'>Товар уже добавлен в корзину</a></div>");
                            try { rrApi.addToBasket(sender.parents('.for_button:first').attr('id')) } catch(e) {}
                            },
                        function()
                            {
                            sender.removeAttr('disabled');
                            }
                        );
                    sender.attr('disabled', 'disabled');
                    return true;
                    });
                });
        global.block_popup = false;
        }; // global.item_popup

    // Adver Pointer
    if ($('.adver .pointer').size())
        $('.adver .pointer').each(function(){
            $(this).addClass('pointer_' + Math.floor((Math.random()*4)+1));
        });

    // Color
    $('.color label').click(function(){
        var color = $(this).parents().filter('.color');

        if (color.hasClass('selected')) {
            color.removeClass('selected');
            color.find('input')[0].checked = false;
        }
        else {
            color.addClass('selected');
            color.find('input')[0].checked = true;
        }

        return false;
    });

    // Search examples
    $('.search_input .example .pseudo_link').click(function(){
        $('#search_text').removeClass('empty').val( $(this).text() );
    });

    $('.search_input .search_in_category span').click(function(e){
        $('.search_input .search_in_category input').click();
    });

    // Qty Input
    global.qty_input_field();

    // Check Order Status
    if ($('.order_status_popup_link').size()) {
        global.popup.append({
            content_element: $('.order_status_popup'),
            opener_element: $('.order_status_popup_link'),
            popup_shade: false,
            onOpen: function() {
                _gaq.push(['_trackEvent', 'CheckOrder', 'Win', 'WinOpen']);
            }
        });
    }

    if ($('#check_order_status').size()) {
        $("#check_order_status").validate({
          rules: {
            order_number: {
              required: true,
              maxlength: 8,
              number: true
            }
          },
          messages: {
              order_number: "Номер веден неверно"
          }
        });        
        $('#check_order_status').submit(function(){
            var check_form = $(this), check_container = $(this).parent(), order_number = $(this).find('#order_number').val(),  order_placeholder = $(this).find('#order_number').attr('placeholder');
            if (order_number == order_placeholder) order_number = '';
            if (order_number) {
                $.ajax({
                    type: "POST",
                    dataType: 'json',
                    url: $(this).attr('action'),
                    data: {
                        order_id: order_number
                    },
                    success: function(data){
                        if('found' in data && data['found']) {
                            _gaq.push(['_trackEvent', 'CheckOrder', 'CheckStatus', 'StatusOk']);
                        } else {
                            _gaq.push(['_trackEvent', 'CheckOrder', 'CheckStatus', 'StatusError']);
                        }
                        check_form.detach();
                        check_container.html("<div class='order_status'><p class='first_child'>"+data['html']+"</p> \
                            <p class='last_child'><span class='pseudo_link check_other_order_link'>Проверить другой заказ</span></p>\
                        </div>");
                        check_container.find('.check_other_order_link').click(function(){
                            check_container.html('');
                            check_container.append(check_form);
                        });
                    }
                });
            }
            return false;
        });
    }
    
    // Шаблоны заказов
    
    $('.orders .sortable_group tr').hover(
        function (){
            $(this).find('.order_hidden').css('visibility', 'visible');
            $(this).find('.delete_hidden').css('visibility', 'visible');
            },
        function (){
            $(this).find('.order_hidden').css('visibility', 'hidden');
            $(this).find('.delete_hidden').css('visibility', 'hidden');
            });

    //удаляем шаблон
    $(".delete_hidden").click(function(){
        var t = $(this);
        var id = t.find("a").attr("id");
        $.post("/personal/template/ajax/",
            {action: "delete_template", id_tmp: id},
            function() {
                t.parents("tr:first").remove();
        });
    });

    var _get = window.location.search.replace('?', '').split('&');
    window._GET = {};
    for(var i in _get)
        {
        if(_get[i].toString().indexOf('=') == -1)
            continue;
        var par = _get[i].toString().split('=');
        window._GET[par[0]] = par[1];
        }

    $('.radio_links label > input[type=radio]').parent().click(function (){
        $(this).parent().find('label > input[type=radio]').removeAttr('checked');
        $(this).children('input').attr('checked','checked');
        });

    $('#set_show').change(function(){
        $.post("/personal/template/ajax/", {
                action: "rename",
                id_template: $('#id_template').val(),
                set_show: $(this).val()
            }
        );
        return false;
    });


    $("#register_physic").on('click', function(e) {
        var t = $(this);
        load_start();
        _gaq.push(['_trackPageview', '/person_want_reg']);
        _gaq.push(['_trackEvent', 'RegForm', 'Fiz', 'WinOpen']);
        setTimeout(function(){
            window.location.href = t.attr('href');
        }, 1000);
        e.preventDefault();
    });

    // popup.register
    $("a[rel=fancy]").fancybox(
    {
        'transitionIn'		: 'none',
        'transitionOut'		: 'none',
        'titlePosition' 	: 'over',
        showNavArrows : false,
        'titleFormat'		: function(currentArray, currentIndex, currentOpts) {}
    });

    // valid register form
    $("#register_form").validate({
        rules: {
            inn: {
                required: true,
                maxlength: 12,
                minlength: 10,
                number: true
            },
            last_name: {
                required: true,
                single_word: true,
                single_tire: true
            },
            first_name: {
                required: true,
                single_word: true,
                single_tire: true
            }
        },
        messages: {
            inn: "Введите правильный ИНН - от 10 до 12 цифр",
            last_name: { required: "Поле обязательное" },
            first_name: { required: "Поле обязательное" }
        }
    });        
    $("#id_mobile").mask("+9(999)999-99-99");

    // ajax.register
    $("#register_form").submit(function() {
        if(!$(this).valid())
            return false;
        var field = {};
        $('#register_fake_button').click();
        $.each($(".register_field"), function(n, elem) {
            var name = $(elem).attr('name');
            if($(elem).attr('type') == 'radio') {
                select = $(elem).filter(':checked').val();
                if(select)
                    field[name] = select;
            }
            else {
                field[name] = $(elem).val();
            }
        });
        $("#button_reg").attr('disabled', 'disabled').addClass('disabled');
        $("#preload_reg").show();
        $.post("/register/company",
            field,
            function(data) {
                $("#preload_reg").hide();
                if(data.err) {
                    $("#button_reg").removeAttr('disabled').removeClass('disabled');
                    for(var name in field) {
                        $("#error_" + name).hide();
                        $("[name=" + name + "]").parent().parent().removeClass("error2");
                    }
                    for(var key in data.err)
                    if (data.err.hasOwnProperty(key)) {
                        $("#error_" + key).show();
                        $("[name=" + key + "]").parent().parent().addClass("error2");
                        $("#error_" + key + " td:last span").html(" " + data.err[key]);
                    }
                }
                else {
                    location.replace('/');
                }
            }
        );
        return false;
    });

    $('#add_new_art').click(function(){
        $.post("/personal/template/ajax/", {
                action: "add_articul",
                art: $('#new_items').val(),
                id: $('#id_template').val()
            },
            function(data){
                if(data.err){
                    if (!$.isArray(data.err))
                        data.err = [data.err];
                    data.err_txt = data.err.join('<br />');
                    $('.err_add').html(data.err_txt).show();
                } else {
                    $('.err_add').hide();
                    location.reload(true);
                }
            }
        );
        return false;
    });

    $('#new_items').keyup(function(event){
            if (event.keyCode == '13') {
                $('#add_new_art').click()
            }
        return false;
        });

    global.popup.append({
        content_element: $('#small_basket_popup'),
        opener_element: $('#mini-basket .l-pd a.pseudo_link'),
        popup_shade: false
    });

    $('.basket .l-pd a.pseudo_link').click(function(){
        setTimeout(function(){
            $('#small_basket_popup').css('left', parseFloat($('#small_basket_popup').css('left'))-64);
        }, 10);
    });
    
    window.d = window.d || {};
    d.utils = d.utils || {};
    
    // показываем хинты
    d.utils.show_hint = function (text, e, w, h)
        {
        window.ev = e;
        var p = {};
        p.l = e.pageX + 10;
        p.t = e.pageY + 15;
        p.w = w || text.length * 5;
        p.h = h || text.length;
        if($(document).width() < p.l + p.w)
            p.l = $(document).width() - p.w - 30;
        $('.hint').remove();
        var obj = $('<div class="hint" style="padding: 8px; display:block;left: '+p.l+'px;top: '+p.t+'px; width: '+p.w+'px;height:'+p.h+'px">'+text+'</div>');
        $('body').append(obj);
        };
    
    d.utils.add_hint = function (obj, flags, w, h)
        {
        $(obj)
            .append($('<sup style="font-weight: bold; color: blue; width: 16px; height: 16px;">?</sup>'))
            .on('mouseover', function(e) {d.utils.show_hint($(obj).find('.hint_msg').text(), e, w, h);})
            //.on('mousemove', function(e) {d.utils.show_hint($(obj).find('.hint_msg').text(), e);})
            .on('mouseout', function(e) { $('.hint').remove();})
        };
    
    if ($.browser.msie && $.browser.version == '6.0')
        {
          $(".table_view tr").hover(function(){
              $(this).addClass("table_row_hover");
              },function() {
              $(this).removeClass("table_row_hover");
              });
        }
    
    if($('.selectors .selected').size()) {
        setInterval(function(){
            $('.selectors .selected css3-container').css('z-index', '4');
            $('.selectors .selected b').css('z-index', '5');
        }, 200);
    }

    if($('#personal_guest').size()) {
        $('#personal_guest').click(function(e) {
            e.preventDefault();
            $('.login_link').click();
            return false;
        });
    }

    if($(".navigation.brands li.inner").length) {
        $(".navigation.brands li.inner a").click(function(e){
//            $(".navigation.brands li").each(function(){
//                $(this).find("input[type='hidden']").remove();
//            });
            $(this).parents('li.inner').first().addClass('selected');
            $(this).parents('li.inner').first().append("<input type='hidden' name='filter_brands[]' value='"+ $(this).attr('rel') +"' />");
            $(this).parents('form.brands').submit();
            e.preventDefault();
        });
        $(".navigation.brands li.inner .cancel").click(function(e){
            $(this).parents('li.inner').first().removeClass('selected');
            $(this).parents('li.inner').first().find("input[type='hidden']").remove();
            $(this).parents('form.brands').submit();
            e.preventDefault();
        });

    }
});


function dfSetSelectOptions(select, options, flags) {
    var f_add_any = (flags & 0x01)?1:0;
    var f_add_empty = (flags & 0x02)?1:0;
    var f_not_clear = (flags & 0x04)?1:0;

    if(!f_not_clear)
        $(select + ' option').remove();

    if((!isset(options))&&(f_add_empty)) {
        $(select).append($('<option value="" selected="selected"></option>'));
        return true;
    }

    if(!isset(options))
        return false;

    if(!is_array(options))
        options = [options];

    if(f_add_any && options.length > 1)
        $(select).append($('<option value="0" selected="selected">Любой</option>'));

    if(f_add_empty && options.length > 1)
        $(select).append($('<option value="" selected="selected"></option>'));

    var opt_html = '';
    for(var i in options)
    if(options.hasOwnProperty(i))
        if(options[i])
            opt_html += '<option value="'+options[i]['id']+'">'+options[i]['name']+'</option>';
    $(select).append(opt_html);

    return true;
}


/**
 * @author Brilliantov A.
 * @param elem img to animate
 *
 * Function to animate img ( Flying to basket )
 */
function animateBasketAdd(elem) {
    // Типа эта функция у нас везде вызывается ;)
    _gaq.push(['_trackEvent', 'But', 'Buy', 'Everywhere']);
    if(elem.length) {
        var mini_basket = $('#mini-basket');
        var newImageWidth 	= elem.width() / 3;
        var newImageHeight	= elem.height() / 3;
        var gotoX = mini_basket.offset().left - elem.offset().left;
        var gotoY = mini_basket.offset().top - elem.offset().top;
        elem.clone()
            .prependTo(elem.parent())
            .css({'position' : 'absolute'})
            .animate({opacity: 0.9}, 100 )
            .animate({opacity: 0, marginLeft: gotoX, marginTop: gotoY, width: newImageWidth, height: newImageHeight}, 1200, function() {
                $(this).remove();
                OSX.call({
                    title: "Вы добавили товар в корзину.",
                    text: "Продолжайте покупки или <a href='/basket/' alt='Корзина'>оформите заказ</a>!"
                });
            });
    }
}
