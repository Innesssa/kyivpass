jCommon = {};

/**
 * @author John Resig (http://jquery.com/), Vlad Yakovlev (red.scorpix@gmail.com)
 * @version 1.0
 */
jCommon.browser = (function() {
	var userAgent = window.navigator.userAgent.toLowerCase();

	return {
		version: (userAgent.match( /.+(?:rv|it|ra|ie)[\/: ]([\d.]+)/ ) || [0,'0'])[1],
		webkit: /webkit/.test(userAgent),
		opera: /opera/.test(userAgent),
		msie: /msie/.test(userAgent) && !/opera/.test(userAgent),
		mozilla: /mozilla/.test(userAgent ) && !/(compatible|webkit)/.test(userAgent),
		safari: /safari/.test(userAgent) && !/chrome/.test(userAgent),
		chrome: /chrome/.test(userAgent)
	};
})();

/**
 * Создает куки или возвращает значение.
 *
 * @example $.cookie('the_cookie', 'the_value');
 * @desc Задает куки для сессии.
 * @example $.cookie('the_cookie', 'the_value', { expires: 7, path: '/', domain: 'site.com', secure: true });
 * @desc Создает куки с опциями.
 * @example $.cookie('the_cookie', null);
 * @desc Удаляет куки.
 * @example $.cookie('the_cookie');
 * @desc Возвращает значение куки.
 *
 * @param String name Имя куки.
 * @param String value Значение куки.
 * @param Object options Объект опций куки.
 * @option Number|Date expires Either an integer specifying the expiration date from now on in days or a Date object.
 *                             If a negative value is specified (e.g. a date in the past), the cookie will be deleted.
 *                             If set to null or omitted, the cookie will be a session cookie and will not be retained
 *                             when the the browser exits.
 * @option String path The value of the path atribute of the cookie (default: path of page that created the cookie).
 * @option String domain The value of the domain attribute of the cookie (default: domain of page that created the cookie).
 * @option Boolean secure If true, the secure attribute of the cookie will be set and the cookie transmission will
 *                        require a secure protocol (like HTTPS).
 * @return Значение куки или объект jCommon.
 * @author Klaus Hartl/klaus.hartl@stilbuero.de
 * @version 1.0
 */
 
jCommon.cookie = function(name, value, options) {

	if ('undefined' != typeof value) {
		options = options || {};

		if (null === value) {
			value = '';
			options.expires = -1;
		}

		var expires = '';

		if (options.expires && ('number' == typeof options.expires || options.expires.toUTCString)) {
			var date;

			if ('number' == typeof options.expires) {
				date = new Date();
				date.setTime(date.getTime() + (options.expires * 24 * 60 * 60 * 1000));
			} else {
				date = options.expires;
			}

			expires = '; expires=' + date.toUTCString(); // use expires attribute, max-age is not supported by IE
		}

		// CAUTION: Needed to parenthesize options.path and options.domain
		// in the following expressions, otherwise they evaluate to undefined
		// in the packed version for some reason...
		var path = options.path ? '; path=' + options.path : '';
		var domain = options.domain ? '; domain=' + options.domain : '';
		var secure = options.secure ? '; secure' : '';
		document.cookie = [name, '=', encodeURIComponent(value), expires, path, domain, secure].join('');

		return this;
	}

	var cookieValue = null;

	if (document.cookie && '' != document.cookie) {
		var cookies = document.cookie.split(';');

		for (var i = 0; i < cookies.length; i++) {
			var cookie = jQuery.trim(cookies[i]);

			// Does this cookie string begin with the name we want?
			if (cookie.substring(0, name.length + 1) == (name + '=')) {
				cookieValue = decodeURIComponent(cookie.substring(name.length + 1));

				break;
			}
		}
	}

	return cookieValue;
};

/**
 * Объект для работы с клавиатурными сокращениями.
 *
 * @example
 * jCommon.shortcuts.unbind('next');
 * jCommon.shortcuts.bind('prev', 'http://ya.ru', 0x24, true);
 *
 * @version 1.0
 * @date 2009-08-11
 */
jCommon.shortcuts = (function() {
	var navigationLinks = {
		'start': { keyCode: 0x24, ctrlKey: true, altKey: false },
		'prev':  { keyCode: 0x25, ctrlKey: true, altKey: false },
		'up':    { keyCode: 0x26, ctrlKey: true, altKey: false },
		'next':  { keyCode: 0x27, ctrlKey: true, altKey: false },
		'down':  { keyCode: 0x28, ctrlKey: true, altKey: false }
	};

	$(function() {
		$('link').each(function() {

			var rel = $(this).attr('rel');

			if (navigationLinks[rel]) {
				navigationLinks[rel].href = $(this).attr('href');
			}
		});

		$(document).keydown(function(event) {
			var links = navigationLinks, inputs;

			for (var rel in links) {
				if (
					links[rel].keyCode == event.keyCode
					&& links[rel].ctrlKey == event.ctrlKey
					&& links[rel].altKey == event.altKey
				) {
					try{inputs = $('input:focus, textarea:focus')[0]}catch(e){inputs = null}
					if (!inputs) {
						if ('string' == typeof links[rel].href && '' != links[rel].href) {
							document.location = links[rel].href;
						} else if ($.isFunction(links[rel].href)) {
							return links[rel].href(event);
						}
					}
				}
			}
		});
	});

	return {
		/**
		 * Привязывает к шорткату клавиатуры действие.
		 * @param {String} name Тип действия.
		 * @param {String|Function} href Если строка, то осуществлять переход по адресу, если функция, то выполнить функцию (первый параметр — объект Event).
		 * @param {Number} keyCode Код нажатой клавиши.
		 * @param {Boolean} [ctrlKey] Нажат ли <code>Ctrl</code>.
		 * @param {Boolean} [altKey] Нажат ли <code>Alt</code>.
		 */
		bind: function(name, href, keyCode, ctrlKey, altKey) {
			ctrlKey = new Boolean(ctrlKey);
			altKey = new Boolean(altKey);

			navigationLinks[name] = {
				href: href,
				keyCode: keyCode,
				ctrlKey: ctrlKey,
				altKey: altKey
			};
		},

		/**
		 * Удаляет действие для шортката.
		 * @param {String} name Тип действия.
		 */
		unbind: function(name) {
			delete navigationLinks[name];
		},

		/**
		 * Удаляет все шорткаты.
		 */
		unbindAll: function() {
			navigationLinks = {};
		}
	};
})();


/**
 * Фикс полупрозрачных PNG в IE6.
 * @param {Element} element Элемент, у которого нужно сделать фикс.
 *
 * @version 1.0
 */
jCommon.fixIePng = (function() {
	var
		prefix = 'file:///',
		/** Путь к прозрачному гифу. */
		gifPath = prefix == location.href.substr(0, prefix.length) ? './i/e.gif' : '/f/1/global/e.gif';

	return jCommon.browser.msie && 6 >= parseInt(jCommon.browser.version) ? function(element) {
		/** @type {String} */
		var src;

		if ('IMG' == element.tagName || ('INPUT' == element.tagName && 'image' == element.type)) {
			if (/\.png$/.test(element.src)) {
				src = element.src;
				element.src = gifPath;
			}
		} else {
			src = element.currentStyle.backgroundImage.match(/url\("(.+\.png)"\)/i);

			if (src) {
				src = src[1];
				element.runtimeStyle.backgroundImage = 'none';
			}
		}

		var reScaleMode = /iesizing\-(\w+)/;
		var m = reScaleMode.exec(element.className);

		if (src) {
			var scaleMode = m ? m[1] : 'crop';
			element.runtimeStyle.filter = "progid:DXImageTransform.Microsoft.AlphaImageLoader(src='" + src + "',sizingMethod='" + scaleMode + "')";
		}
	} : function() {};
})();

/**
 * Эмулирует поведение <input type="search" />, как в Webkit.
 * @version 1.0
 */
jCommon.webkitPlaceholder = (function() {

	if (jCommon.browser.webkit) return { bind: function() {} };

	$(function() {
		$('input[placeholder]').each(function () {
			bind(this);
		});
	});

	/**
	 * Добавляет функцию плейсхолдера элементу.
	 * @param {String|Array[Element]|Element|jQuery} elem Поле ввода
	 * @param {String} [class_empty] Класс для пустого поля ввода
	 */
	function bind(elem, classEmpty) {
		elem = $(elem);
		classEmpty = ('string' === typeof classEmpty) ? classEmpty : 'empty';

		elem.focus(function () {
			if (this.value === $(this).attr('placeholder')) {
				this.value = '';
			}

			$(this).removeClass(classEmpty);
		});

		elem.blur(function () {
			if (!this.value.length) {
				this.value = $(this).attr('placeholder');
				$(this).addClass(classEmpty);
			}
		});

		elem.val().length || $(elem).blur();
	}

	return {
		/**
		 * Вручную добавляет функцию плейсхолдера элементу.
		 * @param {String|Array[Element]|Element|jQuery} elem Поле ввода.
		 * @param {String} [class_empty] Класс для пустого поля ввода.
		 */
		bind: bind
	};
})();

jQuery.preventDefaultEvent = function(e, options) {
    options = options || {shift:1, ctrl:1, alt:1, meta:1};
    var href = e.currentTarget.href;
    if(((options.shift && e.shiftKey)
        || (options.alt && e.altKey)
        || (options.ctrl && e.ctrlKey)
        || (options.meta && e.metaKey))
        && href && href.indexOf('#') != 0
        && href.indexOf('javascript:') != 0
    ) return true;
    e.preventDefault();
    return false;
}