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

    $.fn.datepicker.DPGlobal.template = '<div class="koowa">' + $.fn.datepicker.DPGlobal.template + '</div>';

})(kQuery);