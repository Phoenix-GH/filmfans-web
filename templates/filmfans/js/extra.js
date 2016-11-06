//to avoid error
var SqueezeBox = (function () {
    var sb = function() {};
    sb.prototype = { initialize : function() {}, assign : function () {} };
    return new sb();
})();

;(function($) {

    var baseEasings = {};

    $.each( [ "Quad", "Cubic", "Quart", "Quint", "Expo" ], function( i, name ) {
        baseEasings[ name ] = function( p ) {
            return Math.pow( p, i + 2 );
        };
    });

    $.extend( baseEasings, {
        Sine: function( p ) {
            return 1 - Math.cos( p * Math.PI / 2 );
        },
        Circ: function( p ) {
            return 1 - Math.sqrt( 1 - p * p );
        },
        Elastic: function( p ) {
            return p === 0 || p === 1 ? p :
                -Math.pow( 2, 8 * (p - 1) ) * Math.sin( ( (p - 1) * 80 - 7.5 ) * Math.PI / 15 );
        },
        Back: function( p ) {
            return p * p * ( 3 * p - 2 );
        },
        Bounce: function( p ) {
            var pow2,
                bounce = 4;

            while ( p < ( ( pow2 = Math.pow( 2, --bounce ) ) - 1 ) / 11 ) {}
            return 1 / Math.pow( 4, 3 - bounce ) - 7.5625 * Math.pow( ( pow2 * 3 - 2 ) / 22 - p, 2 );
        }
    });

    $.each( baseEasings, function( name, easeIn ) {
        $.easing[ "easeIn" + name ] = easeIn;
        $.easing[ "easeOut" + name ] = function( p ) {
            return 1 - easeIn( 1 - p );
        };
        $.easing[ "easeInOut" + name ] = function( p ) {
            return p < 0.5 ?
                easeIn( p * 2 ) / 2 :
                1 - easeIn( p * -2 + 2 ) / 2;
        };
    });

    debounce2 = function (method, delay, delayed) {

        var stick = 0;
        var timer = null;

        return function() {

            var context = this, args = arguments;

            var current = window.performance ? window.performance.now() : new Date().getTime();

            if (!stick || current - stick > delay) {
                stick = current;
				if (timer) clearTimeout(timer);
                return method.apply(context, args);
            }

			if (typeof delayed == 'function' && timer == null) {
				timer = setTimeout(function() {
					timer = null;
					delayed();
				}, delay);
			}
        };
    };

})(jQuery);

Function.prototype.debounceSoft = function (delay, delayed) {

	var stick = 0,
		timer = null,
		method = this;

	return function() {

		var context = this, args = arguments;

		var current = window.performance ? window.performance.now() : new Date().getTime();

		if (!stick || current - stick > delay) {

			stick = current;
			if (timer) {
				clearTimeout(timer);
				timer = null;
			}
			return method.apply(context, args);

		} else if (delayed && timer == null) {
			timer = setTimeout(function() {
				method.apply(context, args);
				timer = null;
			}, delayed);

		}
	};
};

Function.prototype.debounce = function(wait, immediate) {

	var timeout,
	    func = this;

	return function() {
		var context = this, args = arguments;
		var later = function() {
			timeout = null;
			if (!immediate) func.apply(context, args);
		};
		var callNow = immediate && !timeout;
		clearTimeout(timeout);
		timeout = setTimeout(later, wait);
		if (callNow) func.apply(context, args);
	};
};

//----------------------------------------------------------------

/* Mutex */

// Semaphores

function Semaphore(initialCount) {
	this._count = initialCount || 1;
	this._waiting = [];
}


Semaphore.prototype.signal = function () {
	this._count += 1;

	if (this._count > 0) {
		var waiter = this._waiting.shift();
		if (waiter) {
			waiter();
		}
	}
};


Semaphore.prototype.wait = function (cb) {
	this._count -= 1;

	if (this._count < 0) {
		this._waiting.push(cb);
	} else {
		cb();
	}
};


// Condition variables

function CondVariable(initialValue) {
	this._value = initialValue;
	this._waiting = [];
}


CondVariable.prototype.wait = function (fnTest, cb) {
	if (fnTest(this._value)) {
		return cb();
	}

	this._waiting.push({ fnTest: fnTest, cb: cb });
};


CondVariable.prototype.set = function (value) {
	this._value = value;

	for (var i = 0; i < this._waiting.length; i++) {
		var waiter = this._waiting[i];

		if (waiter.fnTest(value)) {
			waiter.cb();
			this._waiting.splice(i, 1);
			i -= 1;
		}
	}
};


// Mutex locks

function Mutex() {
	this.isLocked = false;
	this._waiting = [];
}


Mutex.prototype.lock = function (cb) {
	if (this.isLocked) {
		this._waiting.push(cb);
	} else {
		this.isLocked = true;
		cb();
	}
};


Mutex.prototype.timedLock = function (ttl, cb) {
	if (!this.isLocked) {
		this.isLocked = true;
		return cb();
	}

	var timer, that = this;

	this._waiting.push(function () {
		clearTimeout(timer);

		if (!cb) {
			that.unlock();
			return;
		}

		cb();
		cb = null;
	});

	timer = setTimeout(function () {
		if (cb) {
			cb(new Error('Lock timed out'));
			cb = null;
		}
	}, ttl);
};


Mutex.prototype.tryLock = function () {
	if (this.isLocked) {
		return false;
	}

	this.isLocked = true;
	return true;
};


Mutex.prototype.unlock = function () {
	if (!this.isLocked) {
		throw new Error('Mutex is not locked');
	}

	var waiter = this._waiting.shift();

	if (waiter) {
		waiter();
	} else {
		this.isLocked = false;
	}
};


// Read/Write locks

function ReadWriteLock() {
	this.isLocked = null;
	this._readLocks = 0;
	this._waitingToRead = [];
	this._waitingToWrite = [];
}


ReadWriteLock.prototype.readLock = function (cb) {
	if (this.isLocked === 'W') {
		this._waitingToRead.push(cb);
	} else {
		this._readLocks += 1;
		this.isLocked = 'R';
		cb();
	}
};


ReadWriteLock.prototype.writeLock = function (cb) {
	if (this.isLocked) {
		this._waitingToWrite.push(cb);
	} else {
		this.isLocked = 'W';
		cb();
	}
};


ReadWriteLock.prototype.timedReadLock = function (ttl, cb) {
	if (this.tryReadLock()) {
		return cb();
	}

	var timer, that = this;

	this._waitingToRead.push(function () {
		clearTimeout(timer);

		if (!cb) {
			that.unlock();
			return;
		}

		cb();
		cb = null;
	});

	timer = setTimeout(function () {
		if (cb) {
			cb(new Error('ReadLock timed out'));
			cb = null;
		}
	}, ttl);
};


ReadWriteLock.prototype.timedWriteLock = function (ttl, cb) {
	if (this.tryWriteLock()) {
		return cb();
	}

	var timer, that = this;

	this._waitingToWrite.push(function () {
		clearTimeout(timer);

		if (!cb) {
			that.unlock();
			return;
		}

		cb();
		cb = null;
	});

	timer = setTimeout(function () {
		if (cb) {
			cb(new Error('WriteLock timed out'));
			cb = null;
		}
	}, ttl);
};


ReadWriteLock.prototype.tryReadLock = function () {
	if (this.isLocked === 'W') {
		return false;
	}

	this.isLocked = 'R';
	this._readLocks += 1;
	return true;
};


ReadWriteLock.prototype.tryWriteLock = function () {
	if (this.isLocked) {
		return false;
	}

	this.isLocked = 'W';
	return true;
};


ReadWriteLock.prototype.unlock = function () {
	var waiter;

	if (this.isLocked === 'R') {
		this._readLocks -= 1;

		if (this._readLocks === 0) {
			// allow one write lock through

			waiter = this._waitingToWrite.shift();
			if (waiter) {
				this.isLocked = 'W';
				waiter();
			} else {
				this.isLocked = null;
			}
		}
	} else if (this.isLocked === 'W') {
		// allow all read locks or one write lock through

		var rlen = this._waitingToRead.length;

		if (rlen === 0) {
			waiter = this._waitingToWrite.shift();
			if (waiter) {
				this.isLocked = 'W';
				waiter();
			} else {
				this.isLocked = null;
			}
		} else {
			this.isLocked = 'R';
			this._readLocks = rlen;

			var waiters = this._waitingToRead.slice();
			this._waitingToRead = [];

			for (var i = 0; i < rlen; i++) {
				waiters[i]();
			}
		}
	} else {
		throw new Error('ReadWriteLock is not locked');
	}
};

/*
exports.createCondVariable = function (initialValue) {
	return new CondVariable(initialValue);
};

exports.createSemaphore = function (initialCount) {
	return new Semaphore(initialCount);
};

exports.createMutex = function () {
	return new Mutex();
};

exports.createReadWriteLock = function () {
	return new ReadWriteLock();
};
*/




/*!
* jQuery Textarea AutoSize plugin
* Author: Javier Julio
* Licensed under the MIT license
*/
;
(function ($) {

	var pluginName = "textareaAutoSize";
	var pluginDataName = "plugin_" + pluginName;

	var containsText = function (value) {
		return (value.replace(/\s/g, '').length > 0);
	};

	function Plugin(element, options) {
		this.element = element;
		this.$element = $(element);
		this.init();
	}

	Plugin.prototype = {
		init: function () {
			var height = this.$element.outerHeight();
			var diff = parseInt(this.$element.css('paddingBottom')) +
				parseInt(this.$element.css('paddingTop'));

			// Firefox: scrollHeight isn't full height on border-box
			if (this.element.scrollHeight + diff <= height) {
				diff = 0;
			}

			if (containsText(this.element.value)) {
				this.$element.height(this.element.scrollHeight);
			}

			// keyup is required for IE to properly reset height when deleting text
			this.$element.on('input keyup', function (event) {
				$(this)
					.height('auto')
					.height(this.scrollHeight - diff);
			});
		}
	};

	$.fn[pluginName] = function (options) {
		this.each(function () {
			if (!$.data(this, pluginDataName)) {
				$.data(this, pluginDataName, new Plugin(this, options));
			}
		});
		return this;
	};

})(jQuery);



// https://github.com/eusonlito/jquery.applink
;(function ($, window, document, undefined) {
	var pluginName = 'applink',
		defaults = {
			popup: 'auto',
			desktop: false,
			delegate: null,
			data: pluginName
		},

		popupOpened = false,

		agent = navigator.userAgent,

		IS_IPAD = agent.match(/iPad/i) !== null,
		IS_IPHONE = !IS_IPAD && ((agent.match(/iPhone/i) !== null) || (agent.match(/iPod/i) !== null)),
		IS_IOS = IS_IPAD || IS_IPHONE,
		IS_ANDROID = !IS_IOS && agent.match(/android/i) !== null,
		IS_MOBILE = IS_IOS || IS_ANDROID;

	var Callback = function ($element, settings) {
		var href = $element.attr('href'),
			applink = $element.data(settings.data);

		var enabled = (IS_MOBILE || settings.desktop) ? applink : false;
		enabled = ((typeof enabled !== 'undefined') && enabled) ? true : false;

		var popup = $element.data('popup');

		if ((typeof popup === 'undefined') || !popup) {
			popup = settings.popup;
		} else {
			popup = (popup.toString() === 'false') ? false : popup;
		}

		if (!enabled) {
			return Link(href, popup);
		}

		PopUp(applink);

		setTimeout(function () {
			if (BrowserHidden()) {
				popupOpened.close();
			} else {
				Link(href, popup);
			}
		}, 300);
	};

	var BrowserHidden = function () {
		if (typeof document.hidden !== 'undefined') {
			return document.hidden;
		} else if (typeof document.mozHidden !== 'undefined') {
			return document.mozHidden;
		} else if (typeof document.msHidden !== 'undefined') {
			return document.msHidden;
		} else if (typeof document.webkitHidden !== 'undefined') {
			return document.webkitHidden;
		}

		return false;
	};

	var Link = function (href, popup) {
		if ((popup === 'auto') && /^https?:\/\/(www\.)?(facebook|twitter)\.com/i.test(href)) {
			return PopUp(href);
		} else if ((popup !== 'auto') && popup) {
			return PopUp(href);
		}

		if (popupOpened && !popupOpened.closed) {
			popupOpened.close();
		}

		window.location = href;
	};

	var PopUp = function (href) {
		if (popupOpened && !popupOpened.closed) {
			popupOpened.location.replace(href);
			popupOpened.focus();

			return popupOpened;
		}

		var width = (screen.width > 620) ? 600 : screen.width,
			height = (screen.height > 300) ? 280 : screen.height,
			left = (screen.width / 2) - (width / 2),
			top = (screen.height / 2) - (height / 2),
			options = 'location=no,menubar=no,status=no,toolbar=no,scrollbars=no,directories=no,copyhistory=no'
				+ ',width=' + width + ',height=' + height + ',top=' + top + ',left=' + left;

		popupOpened = window.open(href, pluginName, options);
		popupOpened.focus();

		return popupOpened;
	};

	var Plugin = function (element, options) {
		this.element = element;

		this.settings = $.extend({}, defaults, options);

		this.init();
	};

	Plugin.prototype = {
		init: function () {
			var $element = $(this.element), that = this;

			$element.on('click.' + pluginName, this.settings.delegate, function (event) {
				event.preventDefault();
				Callback($(this), that.settings);
			});
		},

		destroy: function () {
			$(this.element).off('.' + pluginName);
		}
	};

	$.fn[pluginName] = function (options) {
		if ((options === undefined) || (typeof options === 'object')) {
			return this.each(function () {
				if (!$.data(this, 'plugin_' + pluginName)) {
					$.data(this, 'plugin_' + pluginName, new Plugin(this, options));
				}
			});
		}

		if ((typeof options !== 'string') || (options[0] === '_') || (options === 'init')) {
			return true;
		}

		var returns, args = arguments;

		this.each(function () {
			var instance = $.data(this, 'plugin_' + pluginName);

			if ((instance instanceof Plugin) && (typeof instance[options] === 'function')) {
				returns = instance[options].apply(instance, Array.prototype.slice.call(args, 1));
			}

			if (options === 'destroy') {
				$.data(this, 'plugin_' + pluginName, null);
			}
		});

		return (returns !== undefined) ? returns : this;
	};
})(jQuery, window, document);
