(function($) {

    "use strict";

    var pluginName = 'ffAffiliateLinks';

    $[pluginName] = function (container, options) {

        var self = this;
        self.container = $(container);
        self.options = $.extend({}, options);

        self._tmpl = self.container.find('.ff-afflinks-tmpl');
        self.tmpl = self._tmpl.html().replace(/data-name/gi, 'name');
        self.items = self.container.find('.ff-afflinks');

        self.ind = parseInt(self._tmpl.data('next'));

        self.container.find('.ff-afflinks-add').on('click', function() {
            self.additem();
        });

        self.container.on('click', '.ff-afflinks-remove', function() {
            var row = $(this).closest('.ff-afflink');
            self.removeitem(row);
        });

    };

    $[pluginName].prototype = {
        additem : function() {
            var self = this;
            var html = self.tmpl.replace(/\%\%\%/gi, self.ind);
            self.items.append(html);
            self.ind++;
        },
        removeitem : function(row) {
            row.remove();
        }
    };

    $.fn[pluginName] = function() {

        $(this).each(function () {
            $(this).data('ffAffiliateLinks', new $[pluginName](this));
        });

    };

})(jQuery);

jQuery(document).on('onFFLoad', function(e, parent) {
    jQuery(parent).find('.ff-afflinks-container').ffAffiliateLinks();
});