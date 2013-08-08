/*
 ---

 description: Custom configuration of Select2 tuned for Nooku Framework

 authors:
 - Stian Didriksen

 requires:
 - Select2

 license: @TODO

 ...
 */

(function ($) {
    "use strict";

    $.fn.koowaSelect2 = function (options) {

        this.each(function () {
            $(this).select2(options);
        });
        return this;
    };

    // plugin defaults, accessible to users
    $.fn.koowaSelect2.defaults = {
        width: "resolve"
    };

})(jQuery);