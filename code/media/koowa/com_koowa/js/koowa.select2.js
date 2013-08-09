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

        var settings = $.extend(true, {
            width: "resolve",
            placeholder: options.placeholder,
            minimumInputLength: 2,
            ///*
            ajax: {
                url: options.url,
                quietMillis: 100,
                data: function (term, page) { // page is the one-based page number tracked by Select2
                    return {
                        search: term, //search term
                        limit: 10, // page size
                        offset: (page-1)*10
                    };
                },
                results: function (data, page) {
                    var results = [],
                        more = (page * 10) < data.documents.total; // whether or not there are more results available

                    $.each(data.documents.data, function(i, document) {
                        results.push(document.data);
                    });

                    // notice we return the value of more so Select2 knows if more results can be loaded
                    return {results: results, more: more};
                }
            },
            initSelection: function(element, callback) {
                var id=$(element).val();
                console.log('stian', id);
                if (id!=='') {
                    $.ajax(options.url, {//@TODO fix url
                        data: {
                            view: 'document',
                            slug: id
                        }
                    }).done(function(data) { callback(data.data); });
                }
            },
            formatResult: function (item) { return item.title; },
            formatSelection: function (item) { return item.title; },
            id: 'slug'
             //*/
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
                //settings.ajax = false;

                var newElement = $('<input />');
                var replaced = element.replaceWith(newElement);
                element = newElement;
            }

            element.select2(settings);
        });
        return this;
    };

})(jQuery);