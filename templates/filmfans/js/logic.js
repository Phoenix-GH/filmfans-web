jQuery(document).ready(function($) {

    "use strict";
    
    var $doc = $(document);
    var $win = $(window);
    var $stick = $('.stick-header').first();

    $stick.append($('header nav.navbar').clone());
    var search = $('header .search-container').clone();
    search.find('h1, .search-types, #ffservice1s').remove();
    search.find('form').attr('id', '');
    $stick.append(search);

    $win.on('scroll resize', (function() {
        var offset = $('body .maincontent').first().offset();
        var scroll = $win.scrollTop();
        var fixed = false;
        if (offset.top && offset.top < scroll) fixed = true;

        $('body').toggleClass('header-fixed', fixed);

    }).debounceSoft(200, 200));

    $win.on('resize', function() {
        $('main').css('min-height', Math.max(1, $win.height() - $('footer').outerHeight() - $('header').outerHeight()) + 'px');
    });

    $doc.on('addcontent', function(e, el) {
        el = $(el);
        el.find('[data-toggle="tooltip"]').tooltip()
        el.find('a[data-applink]').applink( { popup: true } );
    });

    $doc.trigger('addcontent', $('body'));

    $('#search-form-cycle').ffsearch();
    $('.fffilter').ffcontent();
    $('.ffsubitems').ffsubcontent();
    $('.ffhscroll').ffhscroll();
    $('.ffajaxform').ffajaxform();

    $('.selectpick').each( function() {
        var that = $(this);
        var data = that.data();

        if (data.placeholder)
            data.noneSelectedText = data.placeholder;

        $.fn.selectpicker.call(that, data);

        /*
        var _selectpick = function() {
            var $this = $(this);
            var bs = $this.siblings('.bootstrap-select');
            if (!$this.val())
                bs.find('.filter-option').html($this.data('placeholder'));
            $this.siblings('.selectpick-clear').css('visibility', $this.val() ? '' : 'hidden');
        };

        that.closest('.btn-section').append($('<a class="selectpick-clear">x</a>'));

        that.change(_selectpick);
        _selectpick.apply(that);.
        */
    });

    if (/Android|webOS|iPhone|iPad|iPod|BlackBerry/i.test(navigator.userAgent)) {
        $('.selectpick').selectpicker('mobile');
    }

    $doc.on('click', '.selectpick-clear', function() {
        var s = $(this).siblings('.selectpick').first();
        if (s.val()) {
            s.val('');
            s.selectpicker('refresh');
            s.trigger('change');
        }
    });

    $doc.on('click', '.ffrate-zero', function() {
        $(this).hide().siblings('.ffitemRatingForm').show();
    });

    $('.ffloader').addClass('stick-to-parent').on('scroll', function(e) {

        var $this = $(this);
        var parent = $this.parent();
        var eh = $this.outerHeight();
        var top = Math.max(0, $win.height() / 2 - eh / 2 + $win.scrollTop() - parent.offset().top);
        top = Math.min(top, parent.height() - eh);

        $this.css('top', top + 'px');
    });

    if ($('.stick-to-parent').length > 0)
        $win.on('scroll resize', (function() { $('.stick-to-parent').trigger('scroll'); }).debounceSoft(200, 200));

    // K2 Rating
	$doc.on('click', '.ffitemRatingForm a', function(event){
		event.preventDefault();

        var self = $(this);

		var itemID = self.attr('rel');
		if (!itemID) {
			itemID = self.data('id');
		}
		var log = $('#itemRatingLog' + itemID).empty().addClass('formLogLoading');
		var rating = self.html();
		$.ajax({
			url: ffSiteURL+"index.php?option=com_k2&view=item&task=vote&format=raw&user_rating=" + rating + "&itemID=" + itemID,
			type: 'get',
			success: function(response){
				log.removeClass('formLogLoading');
				log.html(response);
				$.ajax({
					url: ffSiteURL+"index.php?option=com_k2&view=item&task=getVotesPercentage&format=raw&itemID=" + itemID,
					type: 'get',
					success: function(percentage){
						$('#itemCurrentRating' + itemID).css('width', percentage + "%");
						setTimeout(function(){
                            var rr = parseFloat(percentage) / 10;
							$.ajax({
								url: ffSiteURL+"index.php?option=com_k2&view=item&task=getVotesNum&format=raw&itemID=" + itemID,
								type: 'get',
								success: function(response){
									log.html(rr.toFixed(2) + '<span>&nbsp;&nbsp;' + response + '</span>');
								}
							});
						}, 2000);
					}
				});
			}
		});
	});

    $doc.on('click', '.likes > a', function() {
        var self = $(this);
        var parent = self.closest('.likes');
        var url = self.data('ajax');
        if (!url) return;
        self.addClass('loading');
        $.ajax({
            url: url,
            type: 'get',
            dataType: 'json',
            success: function (data) {
                if (data.ok) {

                    if (!data.value) data.value = 0;

                    parent.find('.like')
                        .toggleClass('active', data.value > 0)
                        .find('span').html(data.likes ? data.likes : 0);

                    parent.find('.dislike')
                        .toggleClass('active', data.value < 0)
                        .find('span').html(data.dislikes ? data.dislikes : 0)

                } else if (data.redirect) {
                    bootbox.confirm(data.message, function(result) {
                        if (result)
                            window.location.href = data.redirect;
                    });
                } else if (data.error)
                    bootbox.alert(data.error);
            }
        }).always(function () {
            self.removeClass('loading');
        })
    });

    $doc.on('change', '.btn-group-react :radio', function() {
        var parent = $(this).closest('.btn-group-react');
        var fieldset = parent.closest('fieldset');
        var checked = parent.find('.active :radio');
        if (!checked.length)
            checked = parent.find(':radio:checked');
        var url = parent.data('ajax');
        if (!url) return;
        parent.addClass('loading');
        fieldset.prop('disabled', true);
        $.ajax({
            url: url,
            type: 'get',
            data: { ffreaction : checked.val() },
            dataType: 'json',
            success: function (data) {
                if (data.ok) {

                    parent.find(':radio:checked').prop('checked', false);
                    parent.find('.active').removeClass('active');

                    if (data.reaction)
                        parent.find(':radio[value="' + data.reaction + '"]').prop('checked', true);

                    parent.find(':radio:checked').closest('.btn').addClass('active');

                } else if (data.redirect) {
                    bootbox.confirm(data.message, function(result) {
                        if (result)
                            window.location.href = data.redirect;
                    });
                } else if (data.error)
                    bootbox.alert(data.error);
            }
        }).always(function () {
            parent.removeClass('loading');
            fieldset.prop('disabled', false);
        })
    });

    $doc.on('click', '.ffwarning-close', function (event) {
        event.preventDefault();
        $(this).closest('.ffwarning').slideUp(function () {
            jQuery(this).remove();
        });
    });

    $win.trigger('resize');

});

;(function($) {

    "use strict";

    //Search tabs cycling
    $.ffsearch = function(form, options) {
        var defs = { 'select' : '#ffservice1s', 'effects' : false };
        var that = this;

        that.form = $(form);
        that.options = $.extend({}, defs, that.form.data(), options);
        that.wrapper = that.form.find('.search-types');
        that.container = that.form.find('.search-types-items');

        if (!that.container.length) return;

        that.input = $(form).find('input[type="text"]');
        that.select = $(that.options.select);

        that.items = that.container.find('.search-type');
        that.quantity = that.items.length;
        that.current = $();
        that.currentlabel = $();

        that.items.clone().appendTo(that.container);
        that.items.clone().prependTo(that.container);

        that.items = that.container.find('.search-type');

        that.doresize();

        that.items.click(function() {
            that.select.val($(this).find('.label').data('value'));
            that.update(false, this, true);
        });

        $(window).on('resize', (function() {
            that.doresize();
            that.set(false);
        }).debounce(200));

        that.update(true);
    };

    $.ffsearch.prototype = {
        closest : function() {

            var that = this;
            that.center = that.container.width() / 2;

            var cur = $(),
                diff = that.wrapper.width();

            that.container.find('[data-value="' + that.select.val() + '"]').each(function () {
                var self = $(this).closest('.search-type');
                var pos = self.position();
                var d = Math.abs(that.center - pos.left);
                if (diff > d) {
                    diff = d;
                    cur = $(this);
                }
            });

            return cur;
        },
        update : function(noanimation, element, dosubmit) {

            var that = this;

            if (element) {
                that.current = $(element);
                that.currentlabel = that.current.find('.label');
            } else {
                that.currentlabel = that.closest();
                that.current = that.currentlabel.closest('.search-type');
            }

            that.set(noanimation, dosubmit);
        },
        set : function(noanimation, dosubmit) {

            var that = this;

            that.container.find('.selected').removeClass('selected');
            that.currentlabel.addClass('selected');

            var ph = that.currentlabel.data('placeholder');
            if (!ph) ph = that.input.first().data('placeholder');
            that.input.attr('placeholder', ph);

            that.animate(noanimation, dosubmit);
        },
        animate : function(noanimation, dosubmit) {

            var that = this,
                bound = that.wrapper.width() / 2,
                maxbound = that.wrapper.width() / 2,
                halfwidth =  that.container.width() / 2,
                left = -that.container.position().left,
                child_width = 0,
                children = null;

            that.container.stop(true, false);

            var shift = (that.current.length ? that.current.position().left + that.current.width() / 2 : halfwidth) - bound;

            children = that.container.find('.search-type');

            child_width = children.first().width();
            maxbound = Math.min(bound, child_width * (that.quantity - 1) / 2);

            var lb = Math.min(shift, left) - bound / 2,
                rb = Math.max(shift, left) + bound * 2.5;

            children = children.filter(function () {
                var self = $(this);
                var p = self.position().left;
                return lb < p & p + self.width() < rb;
            });

            if (noanimation) {
                that.effect(that.container.find('.search-type'), shift + bound, maxbound);
                that.container.css('left', -shift);
                return;
            }

            that.container.animate({ 'left' : -shift}, {
                duration: 600,
                easing: 'easeInOutQuad',
                progress: function () {
                    that.effect(children, -that.container.position().left + bound, maxbound);
                },
                complete: function() {
                    that.reset();
                    if (dosubmit && $.trim(that.input.val())) that.form.submit();
                }
            });
        },
        effect : function(children, center, bound) {

            if (!this.options.effects || !children) return;

            if (bound <= 180) {
                children.find('.label').css({
                    zIndex  : '',
                    opacity : '',
                    left    : 0,
                    right   : '',
                    width   : ''
                });
                return;
            }

            children.each(function() {
                var self = $(this);
                var w = self.width();
                var shift = self.position().left + self.width() / 2 - center;
                var side = shift > 0;
                shift = Math.abs(shift);
                var index = side ? Math.floor(shift/w) : Math.ceil(shift/w);

                var cent = Math.min(Math.max(shift/bound, 0), 1);
                var shrink = w*cent*0.2;

                var left = index - Math.max(shrink*(shift/w - 1), 0);

                if (shift + left  > bound) {
                    self.find('.label').css('opacity', 0.01);
                    return;
                }

                self.find('.label').css({
                    zIndex  : 50 - index,
                    opacity : 1 - cent*cent*0.6,
                    left    : side ? left + 'px' : 'auto',
                    right   : !side ? left + 'px' : 'auto',
                    width   : w- shrink + 'px'
                });
            })
        },
        reset : function() {
            var that = this;
            that.update(true);
        },
        doresize : function() {
            var that = this;
            that.container.css('width', that.items.first().outerWidth()*that.items.length*1.1);
        }
    };

    $.fn.ffsearch = function() {

        $(this).each(function () {
            $(this).data('ffsearch', new $.ffsearch(this));
        });

    };

    $.ffcontent = function(form, options) {

        var defs = {};
        var that = this;

        that.form = $(form);
        that.options = $.extend({}, defs, options);
        that.mutex = new Mutex();
        that.ajax = null;
        that.container = that.form.closest('.ffcontainer');
        if (!that.container.length)
            that.container = $('.ffcontainer').first();
        that.more = that.container.find('.ffmore');
        that.items = that.container.find('.ffitems');
        that.pagination = that.container.find('.ffpagination');

        that.form.find(':input:not(.passive)').change(function () {
            that.form.submit();
        });

        that.form.submit( function (e) {
            e.preventDefault();
            var url = that.form.attr('action');
            url = url ? url : window.location.href;

            that.load(url, true);
        });

        that.more.find('.ffmore-do').click( function (e) {
            e.preventDefault();

            var url = that.pagination.find('.pagination-next a').first().attr('href');
            if (url)
                that.load(url, false);
            else
                that.more.hide();
        });

        if (that.pagination.data('ajax'))
            that.load(that.pagination.data('ajax'), true);

        that.update();

        that.switchLoad(true, true);
        that.mutex.lock( function() {
            that.items.imagesLoaded(function () {
                that.items.masonry({itemSelector: '.ffitem'});
                that.switchLoad(false);
                that.mutex.unlock();
            });
        });
    };

    $.ffcontent.prototype = {
        load: function(url, replace) {

            var that = this;

            if (that.ajax && that.ajax.abort)
                that.ajax.abort();

            if (replace) {
                var torepl = that.items.find('.ffitem');
                if (torepl.length) that.items.masonry('remove', torepl);
            }

            that.items.css('height', that.items.height());

            that.switchLoad(true, replace);

            var data = that.form.serialize();
            data += '&ffajax=1';

            var success = false;

            that.ajax = $.ajax({
                type: 'POST',
                url: url,
                dataType: 'html',
                data: data,
                success: function (data) {

                    var html = $(' ' + data);

                    if (html.attr('id') !== 'ffajax')
                        html = html.find('#ffajax');

                    if (html.length == 0) {
                        bootbox.alert('Unknown error. Please contact support.');
                        return;
                    }

                    var items = html.find('.ffresult .ffitem');

                    that.items.find('.ffempty').remove();

                    if (items.length > 0) {

                        success = true;

                        that.pagination.html(html.find('.ffpagination').html());

                        that.mutex.lock( function() {

                            items.imagesLoaded(function () {

                                if (replace) {
                                    that.items.html('');
                                }

                                that.items.append(items);
                                that.items.masonry('appended', items);
                                that.items.masonry();

                                if (typeof FB != 'undefined') FB.XFBML.parse(that.items.get(0));

                                that.switchLoad(false);

                                that.mutex.unlock();
                            });
                        });

                    } else if (replace) {
                        that.items.html(html.find('.ffresult').html());
                        that.items.masonry();
                        that.pagination.html('');
                    }
                }
            }).always(function () {
                if (!success)
                    that.switchLoad(false);
            });
        },
        switchLoad: function(load, replace) {
            this.container.toggleClass('loading', load);
            this.container.toggleClass('loading-replace', load && replace);
            $(window).trigger('scroll');
            this.update();
        },
        update: function() {

            var that = this;

            if (that.pagination.find('.pagination-next a').length > 0) {
                that.more.slideDown();
            } else
                that.more.slideUp();
        }
    };

    $.fn.ffcontent = function() {
        $(this).each(function () {
            $(this).data('ffcontent', new $.ffcontent(this));
        });
    };

    $.ffsubcontent = function(container, options) {
        var defs = { ajax: '', empty: 'No items found', emptytoggle: '', count: 5, exclude: 0, request: {}, moretitle : 'Load more...' };
        var that = this;

        that.container = $(container);
        that.options = $.extend({}, defs, that.container.data(), options);
        that.ajax = null;

        that.items = $('<div></div>').appendTo(that.container);

        if (that.options.more) {
            that.more = $('<div class="more-container"><a class="more"><span>' + that.options.moretitle + '</span></a></div>').appendTo(that.container);

            that.more.on('click', function(e) {
                if (that.nexturl) that.load(that.nexturl, that.options.count);
            });
        } else {
            that.more = $('<a class="more"></a>');
            that.container.addClass('nomore');
        }

        that.switchLoad(true);
        that.load(that.options.ajax, that.options.count);
    };

    $.ffsubcontent.prototype = {

        load: function(url, count) {

            var that = this;

            if (!url) {
                that.noItems();
                that.switchLoad(true);
                return;
            }

            if (that.ajax && that.ajax.abort)
                that.ajax.abort();

            that.switchLoad(true);

            var data = { ffajax: 1, fflimit: count, ffexclude: that.options.exclude };

            data = $.extend({}, data, that.options.request);

            that.ajax = $.ajax({
                type: 'POST',
                url: url,
                dataType: 'html',
                data: data,
                success: function (data) {

                    var html = $(' ' + data);

                    if (html.attr('id') !== 'ffajax')
                        html = html.find('#ffajax');

                    if (html.length == 0) {
                        bootbox.alert('Unknown error. Please contact support.');
                        return;
                    }

                    that.nexturl = html.find('.pagination-next a').first().attr('href');

                    var items = html.find('.ffresult .ffitem');

                    if (items.length > 0) {

                        that.items.append(items);

                        items.find('a.count').on('click', function(e) {
                            e.preventDefault();

                            var item = $(this).closest('.ffrowitem');
                            var parent = item.find('.comments');
                            if (item.hasClass('comments-on')) return;

                            item.addClass('comments-on');
                            parent.contents().filter( function() { return this.nodeType === 8; }).replaceWith(function(){ return this.data; });
                            if (typeof FB != 'undefined') FB.XFBML.parse(parent.get(0));
                        });

                        $(document).trigger('addcontent', items);

                        if ($.fn.dotdotdot) {

                            items.find('.ellipsis').dotdotdot({
                                watch: "window",
                                after: "a.readmore"
                            });

                            items.find('.ellipsis').trigger("isTruncated", function( isTruncated ) {
                                if ( !isTruncated ) {
                                    $(this).removeClass('ellipsis').dotdotdot('destroy');
                                }
                            });

                            items.find('.ellipsis a.readmore').on('click', function() {
                                var item = $(this).closest('.ellipsis');
                                var content = item.triggerHandler("originalContent");
                                item.dotdotdot('destroy');

                                var height = item.height();
                                item.removeClass('ellipsis');
                                item.html('');
                                item.append(content);
                                item.css('height', 'auto');
                                var autoHeight = item.height();
                                item.height(height).animate( { height: autoHeight }, 500, 'easeOutQuad', function () { item.css('height', 'auto'); });
                            });
                        }

                        if (typeof FB != 'undefined') FB.XFBML.parse(that.items.get(0));

                        if (that.options.emptytoggle) $(that.options.emptytoggle).show();

                    } else
                        that.noItems();
                }
            }).always(function () {
                that.switchLoad(false);
            });
        },
        noItems: function() {
            if (this.items.find('.ffitem').length == 0)
                this.container.html('<div class="ffempty">' + this.options.empty + '</div>');

            if (this.options.emptytoggle) $(this.options.emptytoggle).hide();
        },
        switchLoad: function(load) {
            this.container.toggleClass('loading', load);
            this.container.toggleClass('nomore', !this.nexturl);
        }
    };

    $.fn.ffsubcontent = function() {
        $(this).each(function () {
            $(this).data('ffsubcontent', new $.ffsubcontent(this));
        });
    };

    $.ffhscroll = function(container, options) {

        var defs = {};
        var that = this;

        that.container = $(container);
        that.options = $.extend({}, defs, that.container.data(), options);

        that.items = that.container.find('.vl-container');
        that.elements = that.container.find('.vl-item');
        that.next = that.container.find('.vl-next');
        that.prev = that.container.find('.vl-prev');

        that.next.on('click', function () {
            that.items.finish();
            that.animate(1);
        });

        that.prev.on('click', function () {
            that.animate(-1);
        });

        that.elements.on('click', function () {
            that.animate(0, $(this));
        });

        $(window).on('resize', (function() {
            that.init();
        } ).debounceSoft(100, true));

        that.init();
    };

    $.ffhscroll.prototype = {
        init: function() {
            var that = this;

            this.items.finish();

            that.count = that.elements.length;
            that.elwidth = that.elements.first().outerWidth();
            that.width = that.count * that.elwidth;
            that.scroll = that.width > that.container.innerWidth();

            this.items.css('width', that.width + 'px');

            that.container.toggleClass('to-scroll', that.scroll);

            that.animate(0);
        },
        animate: function(direction, element) {

            var that = this,
                w = that.container.width(),
                offset = 0;

            this.items.finish();

            if (that.scroll) {

                var min = 0,
                    max = that.width - w;

                offset = - that.items.position().left;

                if (direction > 0)
                    offset += w;
                else if (direction < 0)
                    offset -= w;

                if (max != offset)
                    offset = Math.floor(offset / that.elwidth) * that.elwidth;

                if (element) {
                    max = element.position().left;
                    min = max + element.outerWidth() - w;
                }

                offset = Math.min(max, offset);
                offset = Math.max(min, offset);
            }

            that.next.toggleClass('disabled', offset >= that.width - w);
            that.prev.toggleClass('disabled', offset <= 0);

            this.items.animate({ 'left' : (-offset) + "px"}, 600, 'easeInOutQuad');
        }
    };

    $.fn.ffhscroll = function() {
        $(this).each(function () {
            $(this).data('ffhscroll', new $.ffhscroll(this));
        });
    };

    $.ffajaxform = function(form, options) {

        var defs = {};
        var that = this;

        that.form = $(form);
        that.options = $.extend({}, defs, that.form.data(), options);

        if (typeof that.options.success != 'function')
            that.options.success = new Function(that.options.success);

        that.form.on('submit', function(e) {
            e.preventDefault();
            that.submit();
        });

    };

    $.ffajaxform.prototype = {
        submit: function() {

            var that = this,
                _error_msg = 'Unknown error. Please, save your work elsewhere and reload page.',
                request = that.form.serialize();

            that.switchLoad(true);

            that.ajax = $.ajax({
                type: 'POST',
                url: that.form.attr('action'),
                dataType: 'json',
                data: request,
                success: function (data) {

                    if (data.ok) {
                        that.data = data;
                        if (typeof that.options.success == 'function')
                            that.options.success.call(that);
                    } else if (data.error) {
                        bootbox.alert(data.error);
                    } else
                        bootbox.alert(_error_msg);
                }
            }).fail(function () {
                bootbox.alert(_error_msg);
            }).always(function () {
                that.switchLoad(false);
            });
        },
        switchLoad: function(load) {
            this.form.toggleClass('loading', load);
            this.form.find('fieldset').toggleClass('disabled', load).prop('disabled', load);
        }
    };

    $.fn.ffajaxform = function() {
        $(this).each(function () {
            $(this).data('ffajaxform', new $.ffajaxform(this));
        });
    };

})(jQuery);



