/*
 ---

 description: Custom configuration of Select2 tuned for Nooku Framework

 authors:
 - Stian Didriksen

 requires:
 - Select2

 license: GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>

 copyright: Copyright (C) 2007 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)

 ...
 */

(function ($) {
    "use strict";

    $.fn.koowaSelect2 = function (options) {

        var settings = $.extend(true, {
            width: "resolve",
            placeholder: options.placeholder,
            minimumInputLength: 2,
            ajax: {
                url: options.url,
                quietMillis: 100,
                data: function (term, page) { // page is the one-based page number tracked by Select2
                    var query = {
                        limit: 10, // page size
                        offset: (page-1)*10
                    };
                    query[options.queryVarName] = term;
                    return query;
                },
                results: function (data, page) {
                    var results = [],
                        more = (page * 10) < data[options.model].total; // whether or not there are more results available

                    $.each(data[options.model].data, function(i, item) {
                        results.push(item.data);
                    });

                    // notice we return the value of more so Select2 knows if more results can be loaded
                    return {results: results, more: more};
                }
            },
            initSelection: function(element, callback) {
                var id=$(element).val();
                if (id!=='') {
                    var data = {};
                    data[options.value] = id;
                    $.ajax(options.url, {
                        data: data
                    }).done(function(data) {
                        callback(data[options.model].data[0].data);
                    });
                }
            },
            formatResult: function (item) { return item[options.text]; },
            formatSelection: function (item) { return item[options.text]; },
            id: options.value
        }, options );

        this.each(function() {

            var element = $(this);

            //Workaround for Select2 refusing to ajaxify select elements
            if (element.get(0).tagName.toLowerCase() === "select") {
                var data = [];
                element.children().each(function(i, child){
                    if($(child).val()) {
                        data.push({id: $(child).val(), text: $(child).text()});
                    }
                });
                element.empty();
                element.get(0).typeName = 'input';

                settings.data = data;

                var newElement = $('<input />', {
                    name: element.attr('name'),
                    id: element.attr('id'),
                    value: options.selected,
                    onchange: element.attr('onchange')
                });
                element.replaceWith(newElement);
                element = newElement;
            }

            element.select2(settings);
        });
        return this;
    };

})(jQuery);