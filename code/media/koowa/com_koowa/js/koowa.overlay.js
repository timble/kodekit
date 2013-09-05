/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2007 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa for the canonical source repository
 */
if(!Koowa) {
    var Koowa = {};
}

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
                //self.evaluateScripts(scripts);
            }

            if (self.options.evalStyles) {
                self.evaulateStyles(styles);
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

        $.ajax(this.options);
    },
    send: function(options){
        options = $.extend(true, {}, this.options, options);

        $.ajax(options);
    },
    evaulateStyles: function(styles) {

        /*
        //@TODO
        var url = this.options.url;
        styles.map(function(){
            if($(this).is('style')) return this;
            return $(this).attr('href', url + $(this).attr('href'));
        });
        //*/

        styles.appendTo('head');
    },
    evaluateScripts: function(scripts) {
        var script,
            loadScript = function(script){
                var callback = function() {
                    if(scripts.length) {
                        var script = scripts.last();
                        scripts = scripts.not(script);
                        loadScript(script);
                    } else {
                        //Remove existing domready events as they've fired by now anyway
                        $(document).off('ready');

                        $(document).ready();
                    }
                };

                var el = script[0];
                if(script.attr('src')) {
                    $.ajax({
                        url: script.attr('src'),
                        complete: function(){
                            //ready state stuff load check to continue the queue
                            document.head.appendChild(el);
                            callback.call();
                        }
                    });
                    //script.on('load', callback).appendTo('head');
                    //new $.getScript(script.attr('src'), callback);

                } else {
                    script.appendTo('head');
                    callback.call();
                }

            };

        scripts = scripts.filter(function(i, el) {
            var script = $(el);

            if(script.attr('src')) {
                return $('script[src$="' + script.attr('src').replace(location.origin, '') + '"]').length < 1;
            }

            return true;
        });

        if(scripts.length) {
            var script = scripts.last();
            scripts = scripts.not(script);
            loadScript(scripts);
        }
    }
});

})(jQuery);