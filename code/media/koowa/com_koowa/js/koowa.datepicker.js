/*
 ---

 description: Custom configuration of bootstrap-datepicker tuned for Koowa

 authors:
 - Stian Didriksen

 requires:
 - bootstrap-datepicker

 license: GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>

 copyright: Copyright (C) 2007 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)

 ...
 */

(function ($) {
    "use strict";

    $.fn.koowaDatepicker = function (options) {

        if(!$.fn.koowaDatepicker.container) $.fn.koowaDatepicker.container = $('<div class="koowa"></div>').appendTo('body');

        var settings = {
                parentEl: $.fn.koowaDatepicker.container
            };
        if (typeof(options) === 'object') {
            $.extend(true, settings, options);
        }

        this.each(function() {
            $(this).datepicker(settings);
        });

        return this;
    };

})(kQuery);