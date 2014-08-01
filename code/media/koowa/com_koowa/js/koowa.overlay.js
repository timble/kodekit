/**
 * Nooku Framework - http://nooku.org/framework
 *
 * @copyright	Copyright (C) 2007 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		https://github.com/nooku/nooku-framework for the canonical source repository
 */
(function($){
/**
 * Overlay class
 */
Koowa.Overlay = Koowa.Class.extend({
    element : null,
    /**
     * @returns {object}
     */
    getOptions: function() {
        return $.extend(true, this.supr(), {
            selector: 'body',
            ajaxify: true,
            method: 'get',
            cache: false,
            dataType: 'text',
            evalScripts: false,
            evalStyles: false,
            transport: $.ajax
        });
    },
    initialize: function(element, options) {
        var self = this;

        this.supr();

        this.element = $(element);

        this.setOptions(options).setOptions(this.element.data());

        this.options.complete = function(jqXHR) {
            var element = $('<div>'+jqXHR.responseText+'</div>'),
                scripts = element.find('script').detach(),
                styles = element.find('link[type=text\\/css],style').detach(),
                body = element.find(self.options.selector).length ? element.find(self.options.selector) : element;

            self.element.empty().append(body);

            if (self.options.evalScripts) {
                scripts.appendTo('head');
            }

            if (self.options.evalStyles) {
                styles.appendTo('head');
            }

            if (self.options.ajaxify) {
                self.element.find('a[href]').each(function(i, el){
                    var link = $(el);

                    //Avoid links with data-noasync attributes
                    if(link.data('noasync') != null) {
                        return;
                    }

                    link.on('click', function(event){
                        event.preventDefault();
                        event.stopPropagation();
                        self.send({url: this.href, data: {tmpl:''}});
                    });
                });

                self.element.find('.submittable').on('click', function(event){
                    event.preventDefault();

                    new Koowa.Form($(event.target).data('config')).submit();
                });

                self.element.find('.-koowa-grid').each(function(i, el){
                    var grid = $(el);

                    new Koowa.Controller.Grid({
                        form: grid,
                        ajaxify: true,
                        transport: function(url, data, method){
                            data += '&tmpl=';
                            self.send({url: url, data: data, method: method});
                        }
                    });
                });

                self.element.find('.-koowa-form').each(function(i, el){
                    var form = $(el);
                    new Koowa.Controller.Form({
                        form: form,
                        ajaxify: true,
                        transport: function(url, data, method) {
                            data += '&tmpl=';
                            self.send({url: url, data: data, method: method});
                        }
                    });
                });
            }
        };

        this.options.transport(this.options);
    },
    send: function(options){
        options = $.extend(true, {}, this.options, options);

        this.options.transport(options);
    }
});

})(window.kQuery);