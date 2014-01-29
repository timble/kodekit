/*
 ---

 description: Custom configuration of bootstrap-datepicker tuned for Koowa

 authors:
 - Stian Didriksen

 requires:
 - bootstrap-datepicker

 license: GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>

 copyright: Copyright (C) 2007 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)

 ...
 */

(function ($) {
    "use strict";

    $.fn.koowaDatepicker = function (options) {

        var settings = {
            //@todo beforeShowDate to wrap dropdown-menu with div.koowa
        }
        if (typeof(options) === 'object') {
            $.extend(true, settings, options);
        }

        this.each(function() {
            $(this).datepicker(settings);
        });

        return this;
    };

})(kQuery);