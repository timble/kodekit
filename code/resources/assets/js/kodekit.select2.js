/**
 * Kodekit - http://timble.net/kodekit
 *
 * @copyright   Copyright (C) 2007 - 2016 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license     MPL v2.0 <https://www.mozilla.org/en-US/MPL/2.0>
 * @link        https://github.com/timble/kodekit for the canonical source repository
 */

 /*
 ---

 description: Custom configuration of Select2 tuned for Kodekit

 authors:
 - Stian Didriksen

 requires:
 - Select2

 ...
 */

(function ($) {
    "use strict";

    $.fn.kodekitSelect2 = function(options) {
        var defaults = {
            width: "resolve",
            minimumInputLength: 2,
            ajax: {
                url: options.url,
                dataType: 'json',
                delay: 100,
                data: function (params) {
                    var page  = params.page || 1,  // page is the one-based page number tracked by Select2
                        query = {
                            limit: 10, // page size
                            offset: (page-1)*10
                        };
                    query[options.queryVarName] = params.term;

                    return query;
                },
                processResults: function (data, page) {
                    var results = [],
                        more = (page * 10) < data.meta.total; // whether or not there are more results available

                    $.each(data.data, function(i, item) {
                        // Change format to what select2 expects
                        item.id   = item.attributes[options.value];
                        item.text = item.attributes[options.text];
                        results.push(item);
                    });

                    // notice we return the value of more so Select2 knows if more results can be loaded
                    return {results: results, more: more};
                }
            }
        };

        var settings = $.extend( {}, defaults, options);

        return this.each(function() {
            $(this).select2(settings);
        });
    };
})(kQuery);
