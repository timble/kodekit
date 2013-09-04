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
            evalStyles: false
        });
    },
    initialize: function(element, options) {
        var self = this;

        this.supr();

        this.element = $(element);

        this.setOptions(options).setOptions(this.element.data());

        this.options.complete = function(jqXHR) {
            var parsed = $.parseHTML(jqXHR.responseText, document, true),
                element = $(parsed),
                body = element.find(self.options.selector).length ? element.find(self.options.selector) : element;
            self.element.empty().append(body);

            if (self.options.evalScripts) {
                self.evaluateScripts(element.find('script[type=text/javascript]'));
            }

            if (self.options.evalStyles) {
                self.evaulateStyles(element.find('link[type=text/css]'));
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
        styles.each(function(style) {
            $(style).appendTo($('head'));
        });
    },
    evaluateScripts: function(scripts) {
        var script,
            loadScript = function(script){
                new $.getScript(script.src, function() {
                    if(scripts.length) {
                        loadScript(scripts.shift());
                    } else {
                        //Remove existing domready events as they've fired by now anyway
                        $(document).off('ready');

                        $(document).ready();
                    }
                });
            };

        scripts = scripts.filter(function(script) {
            if(!script.src) {
                return false;
            }

            return !$('head').getElement('script[src$=' + script.src.replace(location.origin, '') + ']');
        });

        if(scripts.length) {
            loadScript(scripts.shift());
        }
    }
});

})(jQuery);