;(function($) {

    "use strict";

    var pluginName = 'filmFansAdmin';

    $[pluginName] = function (options) {

        if (typeof this._load == 'undefined')
            return new $[pluginName](options);

        var self = this;

        var body = $('body');
        var stored = body.data('filmFansAdmin');
        if (typeof stored == 'function')
            return stored;

        this.wrapper = $('<div id="filmFansAdmin" class="filmFansAdmin loading"></div>');
        this.container = $('<div class="filmFansAdmin-container"></div>').appendTo(this.wrapper);
        this.container.wrap('<div class="filmFansAdmin-wrap"></div>');
        this.wrapper.insertAfter($('.adminFormK2, #k2TabBasic .k2Table').first());
        $('<div style="clear: both;"></div>').insertBefore(this.wrapper);

        body.data('filmFansAdmin', this);

        $(document).on('change', '#catid', function(e) {
            self._load();
        });

        $(document).on('change', 'filmFansAdmin-wrap :input', function(e) {
            self.changed = true;
        });

        self._load();
    };

    $[pluginName].prototype = {

        _load: function () {

            var self = this;

            var data = { catid: $('#catid').val(), 'ffaction' : 'load' };

            this.wrapper.addClass('loading');

            $.ajax({
                type: 'POST',
                url: $(location).attr('href'),
                dataType: 'html',
                data: data,
                success: function (data) {
                    self.container.html(data);
                    jQuery(document).trigger('onFFLoad', self.container);
                    self.changed = false;
                }
            }).always(function () {
                self.wrapper.removeClass('loading');
            });
        }

    };

    $(function() {

        $(document).on('onFFLoad', function(e, parent) {

            parent = $(parent);

            parent.find('.k2ffitem').each( function () {

                var that = $(this);

                var search = that.find('.k2ffitem-search');

                var defaults = {
                    minLength: 0,
                    /*focus: function (event, ui) {
                        search.val(ui.item.label);
                        return false;
                    },*/
                    select: function (event, ui) {
                        that.find(".k2ffitem-item").toggle(ui.item.value > 0);
                        that.find(".k2ffitem-id").val(ui.item.value);
                        that.find(".k2ffitem-item h4").html(ui.item.label);
                        that.find(".k2ffitem-item img").attr('src', ui.item.image);
                        return false;
                    }
                };

                if (that.data('ajax')) defaults.source = that.data('ajax');

                var data = $.extend({}, defaults, that.data('init'));

                search.autocomplete(data).click(function() {
                    $(this).autocomplete('search', $(this).val())
                });
            });
        });

        $(function() {
            $(document).trigger('onFFLoad', $('body'));
        });
    });

})(jQuery);