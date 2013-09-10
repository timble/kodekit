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
/*!
 * klass: a classical JS OOP façade
 * https://github.com/ded/klass
 * License MIT (c) Dustin Diaz & Jacob Thornton 2012
 */

!function (name, context, definition) {
    if (typeof define == 'function') define(definition)
    //else if (typeof module != 'undefined') module.exports = definition() //@NOTE this breaks qunit
    else context[name] = definition()
}('klass', this, function () {
    var context = this
        , old = context.klass
        , f = 'function'
        , fnTest = /xyz/.test(function () {xyz}) ? /\bsupr\b/ : /.*/
        , proto = 'prototype'

    function klass(o) {
        return extend.call(isFn(o) ? o : function () {}, o, 1)
    }

    function isFn(o) {
        return typeof o === f
    }

    function wrap(k, fn, supr) {
        return function () {
            var tmp = this.supr
            this.supr = supr[proto][k]
            var undef = {}.fabricatedUndefined
            var ret = undef
            try {
                ret = fn.apply(this, arguments)
            } finally {
                this.supr = tmp
            }
            return ret
        }
    }

    function process(what, o, supr) {
        for (var k in o) {
            if (o.hasOwnProperty(k)) {
                what[k] = isFn(o[k])
                    && isFn(supr[proto][k])
                    && fnTest.test(o[k])
                    ? wrap(k, o[k], supr) : o[k]
            }
        }
    }

    function extend(o, fromSub) {
        // must redefine noop each time so it doesn't inherit from previous arbitrary classes
        function noop() {}
        noop[proto] = this[proto]
        var supr = this
            , prototype = new noop()
            , isFunction = isFn(o)
            , _constructor = isFunction ? o : this
            , _methods = isFunction ? {} : o
        function fn() {
            if (this.initialize) this.initialize.apply(this, arguments)
            else {
                fromSub || isFunction && supr.apply(this, arguments)
                _constructor.apply(this, arguments)
            }
        }

        fn.methods = function (o) {
            process(prototype, o, supr)
            fn[proto] = prototype
            return this
        }

        fn.methods.call(fn, _methods).prototype.constructor = fn

        fn.extend = arguments.callee
        fn[proto].implement = fn.statics = function (o, optFn) {
            o = typeof o == 'string' ? (function () {
                var obj = {}
                obj[o] = optFn
                return obj
            }()) : o
            process(this, o, supr)
            return this
        }

        return fn
    }

    klass.noConflict = function () {
        context.klass = old
        return this
    }

    return klass
});

$(function() {
    $('.submittable').on('click.koowa', function(event){
        event.preventDefault();

        new Koowa.Form($(event.target).data('config')).submit();
    });

    $('.-koowa-grid').each(function() {
        new Koowa.Controller.Grid({
            form: this
        });
    });

    $('.-koowa-form').each(function() {
        new Koowa.Controller.Form({
            form: this
        });
    });
});

Koowa.Class = klass({
    options: {},
    /**
     * @returns {object}
     */
    getOptions: function() {
        return {};
    },
    initialize: function() {
        this.setOptions(this.getOptions());
    },
    setOptions: function(options) {
        if (typeof options === 'object') {
            this.options = $.extend(true, {}, this.options, options);
        }

        return this;
    }
});

/**
 * Creates a 'virtual form'
 *
 * @param   {object} config Configuration object. Accepted keys: method, url, params, element
 * @example new KForm({url:'foo=bar&id=1', params:{field1:'val1', field2...}}).submit();
 */
Koowa.Form = Koowa.Class.extend({
    initialize: function(config) {
        this.config = config;
        if(this.config.element) {
            this.form = $(document[this.config.element]);
        }
        else {
            this.form = $('<form/>', {
                name: 'dynamicform',
                method: this.config.method || 'POST',
                action: this.config.url
            });
            $(document.body).append(this.form);
        }
    },
    addField: function(name, value) {
        var elem = $('<input/>', {
            name: name,
            value: value,
            type: 'hidden'
        });
        elem.appendTo(this.form);

        return this;
    },

    submit: function() {
        var self = this;

        if (this.config.params) {
            $.each(this.config.params, function(name, value){
                self.addField(name, value);
            });
        }

        this.form.submit();
    }
});

/**
 * Grid class
 */
Koowa.Grid = Koowa.Class.extend({
    initialize: function(element){
        var self = this;

        this.element    = $(element);
        this.form       = this.element.is('form') ? this.element : this.element.closest('form');
        this.toggles    = this.element.find('.-koowa-grid-checkall');
        this.checkboxes = this.element.find('.-koowa-grid-checkbox').filter(function(i, checkbox) {
            return !$(checkbox).prop('disabled');
        });

        if(!this.checkboxes.length) {
            this.toggles.prop('disabled', true);
        }

        this.toggles.on('change.koowa', function(event, ignore){
            if(!ignore) {
                self.checkAll($(this).prop('checked'));
            }
        });

        this.checkboxes.on('change.koowa', function(event, ignore){
            if(!ignore) {
                self.uncheckAll();
            }
        });
    },
    checkAll: function(value){
        var changed = this.checkboxes.filter(function(i, checkbox){
            return $(checkbox).prop('checked') !== value;
        });

        this.checkboxes.prop('checked', value);
        changed.trigger('change', true);
    },

    uncheckAll: function(){
        var total = this.checkboxes.filter(function(i, checkbox){
            return $(checkbox).prop('checked') !== false;
        }).length;

        this.toggles.prop('checked', this.checkboxes.length === total);
        this.toggles.trigger('change', true);
    }
});

/**
 * Find all selected checkboxes' ids in the grid
 *
 * @param   {string|object|null} context   A DOM Element, Document, or jQuery to use as context
 * @return  array           The items' ids
 */
Koowa.Grid.getAllSelected = function(context) {
    return $('.-koowa-grid-checkbox:checked', context);
};

/**
 * Get a query string for selected checkboxes
 *
 * @param   {string|object|null} context   A DOM Element, Document, or jQuery to use as context
 * @return  array           The items' ids
 */
Koowa.Grid.getIdQuery = function(context) {
    return decodeURIComponent(this.getAllSelected(context).serialize());
};

/**
 * Controller class, execute actions complete with command chains
 */
Koowa.Controller = Koowa.Class.extend({
    form: null,
    toolbar: null,
    buttons: null,

    token_name: null,
    token_value: null,
    /**
     * @returns {object}
     */
    getOptions: function() {
        return $.extend(this.supr(), {
            toolbar: '.koowa-toolbar',
            url: window.location.href
        });
    },
    initialize: function(options){
        var self = this;

        this.supr();
        this.setOptions(options);

        this.form = $(this.options.form);

        this.setOptions(this.form.data());

        if (this.form.prop('action')) {
            this.options.url = this.form.attr('action');
        }

        this.toolbar = $(this.options.toolbar);
        this.form.data('controller', this);

        this.on('execute', function(){
            return self.execute.apply(self, arguments);
        });

        this.token_name = this.form.data('token-name');
        this.token_value = this.form.data('token-value');

        if(this.toolbar) {
            this.setToolbar();
        }
    },
    setToolbar: function() {
        var self = this;

        this.buttons = this.toolbar.find('.toolbar[data-action]');

        this.buttons.each(function() {
            var button = $(this),
                context = {},
                options = button.data(),
                data = options.data;

            if (options.eventAdded) {
                return;
            }

            if (typeof data !== 'object') {
                data = (data && $.type(data) === 'string') ? $.parseJSON(data) : {};
            }

            //Set token data
            if (self.token_name) {
                data[self.token_name] = self.token_value;
            }

            context.validate = options.novalidate !== 'novalidate';
            context.data   = data;
            context.action = options.action;

            button.on('click.koowa', function(event) {
                event.preventDefault();

                context.trigger = button;

                if (!button.hasClass('disabled')) {
                    self.setOptions(options);
                    self.trigger('execute', [context]);
                }
            });

            button.data('event-added', true);
        });
    },
    execute: function(event, context){
        var action   = context.action[0].toUpperCase() + context.action.substr(1),
            method = '_action' + action;

        if (typeof context.validate === 'undefined') {
            context.validate = true;
        }

        if (this.trigger('before'+action, context)) {
            method = this[method] ? method : '_actionDefault';

            this[method].call(this, context);

            this.trigger('after'+action, context);
        }

        return this;
    },
    on: function(type, fn){
        return this.form.on('koowa:'+type, fn);
    },

    off: function(type, fn){
        return this.form.off('koowa:'+type, fn);
    },

    trigger: function(type, args){
        var event = jQuery.Event('koowa:'+type);
        this.form.trigger(event, args);
        return !event.isDefaultPrevented();
    },

    checkValidity: function(){
        var buttons;

        if (this.buttons) {
            this.trigger('beforeValidate');

            buttons = this.buttons.filter('[data-novalidate!="novalidate"]');

            if (this.trigger('validate')) {
                buttons.removeClass('disabled');
            } else {
                buttons.addClass('disabled');
            }

            this.trigger('afterValidate');
        }
    }
});

/**
 * Controller class specialized for grids, extends Koowa.Controller
 *
 * @package     Koowa_Media
 * @subpackage  Javascript
 */
Koowa.Controller.Grid = Koowa.Controller.extend({
    getOptions: function() {
        return $.extend(this.supr(), {
            inputs: '.-koowa-grid-checkbox, .-koowa-grid-checkall',
            ajaxify: false
        });
    },
    initialize: function(options){
        var thead,
            self = this;

        this.supr(options);

        this.grid = new Koowa.Grid(this.form);

        this.on('validate', this.validate);

        if (this.options.inputs && this.buttons) {
            this.checkValidity();
            this.form.find(this.options.inputs).on('change.koowa', function(event, ignore){
                if (!ignore) {
                    self.checkValidity();
                }
            });
        }

        this.token_name = this.form.data('token-name');
        this.token_value = this.form.data('token-value');

        this.setTableHeaders();
        this.setTableRows();

        //<select> elements in headers and footers are for filters, so they need to submit the form on change
        this.form.find('thead select, tfoot select').on('change.koowa', function(){
            if (self.options.ajaxify) {
                event.preventDefault();

                self.options.transport(self.options.url, self.form.serialize(), 'get');
            }

            self.form.submit();
        });

    },

    setTableHeaders: function() {
        //Make the table headers "clickable"
        this.form.find('thead tr > *').each(function() {
            var element = $(this),
                link = element.find('a'),
                checkall = element.find('.-koowa-grid-checkall');

            if (link.length) {
                element.on('click.koowa', function(event){
                    //Don't do anything if the event target is the same as the element
                    if(event.target != element[0]) {
                        return;
                    }

                    //Run this check on click, so that progressive enhancements isn't bulldozed
                    if(link.prop('href')) {
                        window.location.href = link.prop('href');
                    } else {
                        link.trigger('click', event);
                    }
                });

                if(link.hasClass('-koowa-asc')) {
                    element.addClass('-koowa-asc');
                } else if(link.hasClass('-koowa-desc')) {
                    element.addClass('-koowa-desc');
                }

                return this;
            } else if(checkall.length) {
                //Making the <td> or <th> element that's the parent of a checkall checkbox toggle the checkbox when clicked
                element.on('click.koowa', function(event){
                    //Don't do anything if the event target is the same as the element
                    if(event.target != element[0]) {
                        return true;
                    }

                    //Checkall uses change for other purposes
                    checkall.prop('checked', checkall.is(':checked') ? false : true).trigger('change');
                });
            }

            element.addClass('void');
        });
    },
    setTableRows: function() {
        var self = this,
            checkboxes = this.form.find('tbody tr .-koowa-grid-checkbox');

        this.form.find('tbody tr').each(function(){
            var tr = $(this),
                checkbox = tr.find('.-koowa-grid-checkbox');

            if(tr.data('readonly') == true || !checkbox.length) {
                return;
            }

            // Trigger checkbox when the user clicks anywhere in the row
            tr.on('click.koowa', function(event){
                var target = $(event.target);
                if(target.is('[type=checkbox]') || target.is('[type=radio]')) {
                    return;
                }

                checkbox.prop('checked', !checkbox.prop('checked')).trigger('change');
            });

            // Checkbox should add selected and selected-multiple classes to the row
            checkbox.on('change.koowa', function(){
                var selected,
                    parent = tr.parent();

                if ($(this).is('[type=radio]')) {
                    parent.find('.selected').removeClass('selected');
                }

                $(this).prop('checked') ? tr.addClass('selected') : tr.removeClass('selected');

                selected = tr.hasClass('selected') + tr.siblings('.selected').length;

                if(selected > 1) {
                    parent.addClass('selected-multiple').removeClass('selected-single')
                } else {
                    parent.removeClass('selected-multiple').addClass('selected-single');
                }
            }).trigger('change', true);

            // Set up buttons such as publish/unpublish triggers
            tr.find('[data-action]').each(function() {
                var action = $(this),
                    context = {},
                    data = action.data('data'),
                    options = action.data(),
                    eventType = action.data('event-type');

                if (typeof data !== 'object') {
                    data = (data && $.type(data) === 'string') ? $.parseJSON(data) : {};
                }

                //Set token data
                if(self.token_name) {
                    data[self.token_name] = self.token_value;
                }

                if(!eventType) {
                    eventType = action.is('[type="radio"],[type="checkbox"],select') ? 'change' : 'click';
                }

                context.validate = options.novalidate !== 'novalidate';
                context.data   = data;
                context.action = options.action;

                action.on(eventType+'.koowa', function(){
                    checkboxes.prop('checked', '');
                    checkbox.prop('checked', 'checked');
                    checkboxes.trigger('change', true);

                    context.trigger = action;

                    self.setOptions(options);
                    self.trigger('execute', [context]);
                });
            });
        });
    },
    validate: function(){
        return Koowa.Grid.getIdQuery() || false;
    },

    _actionDefault: function(context) {
        var idQuery = Koowa.Grid.getIdQuery(),
            append  = this.options.url.match(/\?/) ? '&' : '?',
            options;

        if (context.validate && !this.trigger('validate', [context])) {
            return false;
        }

        options = {
            method:'post',
            url: this.options.url+(idQuery ? append+idQuery : ''),
            params: $.extend({}, {action: context.action}, context.data)
        };

        new Koowa.Form(options).submit();
    }
});

/**
 * Controller class specialized for forms, extends Koowa.Controller
 */
Koowa.Controller.Form = Koowa.Controller.extend({
    _actionDefault: function(context){
        if (context.validate && !this.trigger('validate', [context])) {
            return false;
        }

        this.form.append($('<input/>', {name: 'action', type: 'hidden', value: context.action}));
        this.form.submit();
    }

});

})(jQuery);