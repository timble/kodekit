/**
 * @category	Nooku
 * @package     Nooku_Media
 * @subpackage  Javascript
 * @copyright   Copyright (C) 2007 - 2012 Johan Janssens. All rights reserved.
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.nooku.org
 */

/**
 * Koowa global namespace
 *
 * @author      Johan Janssens <johan@nooku.org>
 * @category    Nooku
 * @package     Nooku_Media
 * @subpackage  Javascript
 */
if(!Koowa) var Koowa = {};
Koowa.version = 0.7;

/* Shims, making newer javascript features work in older browsers */

/* https://developer.mozilla.org/en-US/docs/JavaScript/Reference/Global_Objects/Function/bind */
if (!Function.prototype.bind) {
    Function.prototype.bind = function (oThis) {
        if (typeof this !== "function") {
            // closest thing possible to the ECMAScript 5 internal IsCallable function
            throw new TypeError("Function.prototype.bind - what is trying to be bound is not callable");
        }

        var aArgs = Array.prototype.slice.call(arguments, 1),
            fToBind = this,
            fNOP = function () {},
            fBound = function () {
                return fToBind.apply(this instanceof fNOP && oThis
                    ? this
                    : oThis,
                    aArgs.concat(Array.prototype.slice.call(arguments)));
            };

        fNOP.prototype = this.prototype;
        fBound.prototype = new fNOP();

        return fBound;
    };
}

/* Class.js ported from MooTools */
(function($){

    (function(){

// instanceOf

        var instanceOf = this.instanceOf = function(item, object){
            if (item == null) return false;
            var constructor = item.$constructor || item.constructor;
            while (constructor){
                if (constructor === object) return true;
                constructor = constructor.parent;
            }
            return item instanceof object;
        };

// Function overloading

        var Function = this.Function;

        var enumerables = true;
        for (var i in {toString: 1}) enumerables = null;
        if (enumerables) enumerables = ['hasOwnProperty', 'valueOf', 'isPrototypeOf', 'propertyIsEnumerable', 'toLocaleString', 'toString', 'constructor'];

        Function.prototype.overloadSetter = function(usePlural){
            var self = this;
            return function(a, b){
                if (a == null) return this;
                if (usePlural || typeof a != 'string'){
                    for (var k in a) self.call(this, k, a[k]);
                    if (enumerables) for (var i = enumerables.length; i--;){
                        k = enumerables[i];
                        if (a.hasOwnProperty(k)) self.call(this, k, a[k]);
                    }
                } else {
                    self.call(this, a, b);
                }
                return this;
            };
        };

        Function.prototype.overloadGetter = function(usePlural){
            var self = this;
            return function(a){
                var args, result;
                if (usePlural || typeof a != 'string') args = a;
                else if (arguments.length > 1) args = arguments;
                if (args){
                    result = {};
                    for (var i = 0; i < args.length; i++) result[args[i]] = self.call(this, args[i]);
                } else {
                    result = self.call(this, a);
                }
                return result;
            };
        };

        Function.prototype.extend = function(key, value){
            this[key] = value;
        }.overloadSetter();

        Function.prototype.implement = function(key, value){
            this.prototype[key] = value;
        }.overloadSetter();

// From

        var slice = Array.prototype.slice;

        Function.from = function(item){
            return (typeOf(item) == 'function') ? item : function(){
                return item;
            };
        };

        Array.from = function(item){
            if (item == null) return [];
            return (Type.isEnumerable(item) && typeof item != 'string') ? ($.type(item) == 'array') ? item : slice.call(item) : [item];
        };

// Type

        var Type = this.Type = function(name, object){
            if (name){
                var lower = name.toLowerCase();
                var typeCheck = function(item){
                    return (typeOf(item) == lower);
                };

                Type['is' + name] = typeCheck;
                if (object != null){
                    object.prototype.$family = (function(){
                        return lower;
                    }).hide();
                    //<1.2compat>
                    object.type = typeCheck;
                    //</1.2compat>
                }
            }

            if (object == null) return null;

            object.extend(this);
            object.$constructor = Type;
            object.prototype.$constructor = object;

            return object;
        };

        var toString = Object.prototype.toString;

        Type.isEnumerable = function(item){
            return (item != null && typeof item.length == 'number' && toString.call(item) != '[object Function]' );
        };

        var hooks = {};

        var hooksOf = function(object){
            var type = $.type(object.prototype);
            return hooks[type] || (hooks[type] = []);
        };

        var implement = function(name, method){
            if (method && method.$hidden) return;

            var hooks = hooksOf(this);

            for (var i = 0; i < hooks.length; i++){
                var hook = hooks[i];
                if (typeOf(hook) == 'type') implement.call(hook, name, method);
                else hook.call(this, name, method);
            }

            var previous = this.prototype[name];
            if (previous == null || !previous.$protected) this.prototype[name] = method;

            if (this[name] == null && $.type(method) == 'function') extend.call(this, name, function(item){
                return method.apply(item, slice.call(arguments, 1));
            });
        };

        var extend = function(name, method){
            if (method && method.$hidden) return;
            var previous = this[name];
            if (previous == null || !previous.$protected) this[name] = method;
        };

        Type.implement({

            implement: implement.overloadSetter(),

            extend: extend.overloadSetter(),

            alias: function(name, existing){
                implement.call(this, name, this.prototype[existing]);
            }.overloadSetter(),

            mirror: function(hook){
                hooksOf(this).push(hook);
                return this;
            }

        });

        new Type('Type', Type);

// Default Types

        var force = function(name, object, methods){
            var isType = (object != Object),
                prototype = object.prototype;

            if (isType) object = new Type(name, object);

            for (var i = 0, l = methods.length; i < l; i++){
                var key = methods[i],
                    generic = object[key],
                    proto = prototype[key];

                if (generic) generic.protect();

                if (isType && proto){
                    delete prototype[key];
                    prototype[key] = proto.protect();
                }
            }

            if (isType) object.implement(prototype);

            return force;
        };

        force('String', String, [
            'charAt', 'charCodeAt', 'concat', 'indexOf', 'lastIndexOf', 'match', 'quote', 'replace', 'search',
            'slice', 'split', 'substr', 'substring', 'toLowerCase', 'toUpperCase'
        ])('Array', Array, [
            'pop', 'push', 'reverse', 'shift', 'sort', 'splice', 'unshift', 'concat', 'join', 'slice',
            'indexOf', 'lastIndexOf', 'filter', 'forEach', 'every', 'map', 'some', 'reduce', 'reduceRight'
        ])('Number', Number, [
            'toExponential', 'toFixed', 'toLocaleString', 'toPrecision'
        ])('Function', Function, [
            'apply', 'call', 'bind'
        ])('RegExp', RegExp, [
            'exec', 'test'
        ])('Object', Object, [
            'create', 'defineProperty', 'defineProperties', 'keys',
            'getPrototypeOf', 'getOwnPropertyDescriptor', 'getOwnPropertyNames',
            'preventExtensions', 'isExtensible', 'seal', 'isSealed', 'freeze', 'isFrozen'
        ])('Date', Date, ['now']);

        Object.extend = extend.overloadSetter();

// fixes NaN returning as Number

        Number.prototype.$family = function(){
            return isFinite(this) ? 'number' : 'null';
        }.hide();

// Number.random

        Number.extend('random', function(min, max){
            return Math.floor(Math.random() * (max - min + 1) + min);
        });

// forEach, each

        var hasOwnProperty = Object.prototype.hasOwnProperty;
        Object.extend('forEach', function(object, fn, bind){
            for (var key in object){
                if (hasOwnProperty.call(object, key)) fn.call(bind, object[key], key, object);
            }
        });

        Object.each = Object.forEach;

        Array.implement({

            forEach: function(fn, bind){
                for (var i = 0, l = this.length; i < l; i++){
                    if (i in this) fn.call(bind, this[i], i, this);
                }
            },

            each: function(fn, bind){
                Array.forEach(this, fn, bind);
                return this;
            }

        });

// Array & Object cloning, Object merging and appending

        var cloneOf = function(item){
            switch ($.type(item)){
                case 'array': return item.clone();
                case 'object': return Object.clone(item);
                default: return item;
            }
        };

        Array.implement('clone', function(){
            var i = this.length, clone = new Array(i);
            while (i--) clone[i] = cloneOf(this[i]);
            return clone;
        });

        var mergeOne = function(source, key, current){
            switch ($.type(current)){
                case 'object':
                    if ($.type(source[key]) == 'object') Object.merge(source[key], current);
                    else source[key] = Object.clone(current);
                    break;
                case 'array': source[key] = current.clone(); break;
                default: source[key] = current;
            }
            return source;
        };

        Object.extend({

            merge: function(source, k, v){
                if ($.type(k) == 'string') return mergeOne(source, k, v);
                for (var i = 1, l = arguments.length; i < l; i++){
                    var object = arguments[i];
                    for (var key in object) mergeOne(source, key, object[key]);
                }
                return source;
            },

            clone: function(object){
                var clone = {};
                for (var key in object) clone[key] = cloneOf(object[key]);
                return clone;
            },

            append: function(original){
                for (var i = 1, l = arguments.length; i < l; i++){
                    var extended = arguments[i] || {};
                    for (var key in extended) original[key] = extended[key];
                }
                return original;
            }

        });

    })();

    var Class = this.Class = new Type('Class', function(params){
        if (instanceOf(params, Function)) params = {initialize: params};

        var newClass = function(){
            reset(this);
            if (newClass.$prototyping) return this;
            this.$caller = null;
            var value = (this.initialize) ? this.initialize.apply(this, arguments) : this;
            this.$caller = this.caller = null;
            return value;
        }.extend(this).implement(params);

        newClass.$constructor = Class;
        newClass.prototype.$constructor = newClass;
        newClass.prototype.parent = parent;

        return newClass;
    });

    var parent = function(){
        if (!this.$caller) throw new Error('The method "parent" cannot be called.');
        var name = this.$caller.$name,
            parent = this.$caller.$owner.parent,
            previous = (parent) ? parent.prototype[name] : null;
        if (!previous) throw new Error('The method "' + name + '" has no parent.');
        return previous.apply(this, arguments);
    };

    var reset = function(object){
        for (var key in object){
            var value = object[key];
            switch ($.type(value)){
                case 'object':
                    var F = function(){};
                    F.prototype = value;
                    object[key] = reset(new F);
                    break;
                case 'array': object[key] = value.clone(); break;
            }
        }
        return object;
    };

    var wrap = function(self, key, method){
        if (method.$origin) method = method.$origin;
        var wrapper = function(){
            if (method.$protected && this.$caller == null) throw new Error('The method "' + key + '" cannot be called.');
            var caller = this.caller, current = this.$caller;
            this.caller = current; this.$caller = wrapper;
            var result = method.apply(this, arguments);
            this.$caller = current; this.caller = caller;
            return result;
        }.extend({$owner: self, $origin: method, $name: key});
        return wrapper;
    };

    var implement = function(key, value, retain){
        if (Class.Mutators.hasOwnProperty(key)){
            value = Class.Mutators[key].call(this, value);
            if (value == null) return this;
        }

        if ($.type(value) == 'function'){
            if (value.$hidden) return this;
            this.prototype[key] = (retain) ? value : wrap(this, key, value);
        } else {
            Object.merge(this.prototype, key, value);
        }

        return this;
    };

    var getInstance = function(klass){
        klass.$prototyping = true;
        var proto = new klass;
        delete klass.$prototyping;
        return proto;
    };

    Class.implement('implement', implement.overloadSetter());

    Class.Mutators = {

        Extends: function(parent){
            this.parent = parent;
            this.prototype = getInstance(parent);
        },

        Implements: function(items){
            Array.from(items).each(function(item){
                var instance = new item;
                for (var key in instance) implement.call(this, key, instance[key], true);
            }, this);
        }
    };

    Koowa.Class = Class;

    this.Class = Class;

}.bind(Koowa))(jQuery);

(function($){

    $(function(){
        //@TODO needs testing
        $('.submitable').on('click', function(event){
            event = new Event(event);
            new Koowa.Form(JSON.decode(event.target.getProperty('rel'))).submit();
        });

        $('.-koowa-grid').each(function(){
            var grid = $(this);
            new Koowa.Grid(grid);

            var toolbar = grid.data('toolbar') || '.toolbar';
            new Koowa.Controller.Grid({form: grid, toolbar: grid.data('no-toolbar') ? false : $(toolbar)});
        });

        $('.-koowa-form').each(function(){
            var form = $(this), toolbar = form.data('toolbar') || '.toolbar';
            new Koowa.Controller.Form({form: form, toolbar: form.data('no-toolbar') ? false : $(toolbar)});
        });
    });

    /* Section: Base utilities */
    Koowa.Options = new Koowa.Class({
        setOptions: function(){
            var options = this.options = Object.merge.apply(null, [{}, this.options].append(arguments));
            if (this.addEvent) for (var option in options){
                if ($.type(options[option]) != 'function' || !(/^on[A-Z]/).test(option)) continue;
                this.addEvent(option, options[option]);
                delete options[option];
            }
            return this;
        }
    });

    /* Section: Classes */

    /**
     * Creates a 'virtual form'
     *
     * @param   json    Configuration:  method, url, params, formelem
     * @example new KForm({method:'post', url:'foo=bar&id=1', params:{field1:'val1', field2...}}).submit();
     */
    Koowa.Form = new Koowa.Class({

        initialize: function(config) {
            this.config = config;
            if(this.config.element) {
                this.form = $(document[this.config.element]);
            }
            else {
                this.form = $('<form/>', {
                    method: this.config.method,
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
            $.each(this.config.params, function(name, value){
                this.addField(name, value);
            }.bind(this));
            this.form.submit();
        }
    });


    /**
     * Grid class
     *
     * @package     Koowa_Media
     * @subpackage  Javascript
     */
    Koowa.Grid = new Koowa.Class({

        initialize: function(element){

            this.element    = $(element);
            this.form       = this.element.is('form') ? this.element : this.element.closest('form');
            this.toggles    = this.element.find('.-koowa-grid-checkall');
            //@TODO rewrite to use a getter instead, or .live
            this.checkboxes = this.element.find('.-koowa-grid-checkbox').filter(function(i, checkbox) {
                var $checkbox = $(checkbox);
                return !$checkbox.is(':disabled');
            });

            if(!this.checkboxes.length) {
                this.toggles.prop('disabled', true);
            }

            var self = this;
            this.toggles.on('change', function(event, ignore){
                if(!ignore) {
                    self.checkAll($(this).prop('checked'));
                }
            });

            this.checkboxes.on('change', function(event, ignore){
                if(!ignore) {
                    self.uncheckAll();
                }
            });
        },

        checkAll: function(value){

            var changed = this.checkboxes.filter(function(i, checkbox){
                var $checkbox = $(checkbox);
                return $checkbox.prop('checked') !== value;
            });

            this.checkboxes.prop('checked', value);
            changed.trigger('change', true);
        },

        uncheckAll: function(){

            var total = this.checkboxes.filter(function(i, checkbox){
                var $checkbox = $(checkbox);
                return $checkbox.prop('checked') !== false;
            }).length;

            this.toggles.prop('checked', this.checkboxes.length === total);
            this.toggles.trigger('change', true);

        }
    });
    /**
     * Find all selected checkboxes' ids in the grid
     *
     * @LEGACY  methods calling expects an js array to be returned,
     *          without legacy we should just return the jQuery object
     *
     * @param   mixed   A DOM Element, Document, or jQuery to use as context
     * @return  array   The items' ids
     */
    Koowa.Grid.getAllSelected = function(context) {
        return $.makeArray(this.getAllSelectedElements(context));
    };
    Koowa.Grid.getAllSelectedElements = function(context) {
        return $('.-koowa-grid-checkbox:checked', context);
    };
    Koowa.Grid.getIdQuery = function(context) {
        // We could do $.param(this.getAllSelected(scope)) but this way is faster since we iterate once not twice
        return this.getAllSelectedElements(context).serialize();
    };



    /**
     * Controller class, execute actions complete with command chains
     *
     * @package     Koowa_Media
     * @subpackage  Javascript
     */
    Koowa.Controller = new Koowa.Class({

        Implements: [Koowa.Options],

        form: null,
        toolbar: null,
        buttons: null,

        options: {
            toolbar: false,
            ajaxify: false,
            url: window.location.href
        },

        initialize: function(options){

            this.setOptions(options);

            this.form = this.options.form;
            this.toolbar = this.options.toolbar;
            if(this.form.action) this.options.url = this.form.action;

            //Set options that is coming from data attributes on the form element
            this.setOptions(this.getOptions(this.form));

            this.form.data('controller', this);

            this.addEvent('execute', this.execute.bind(this));

            //Attach toolbar buttons actions
            if(this.toolbar) {
                this.buttons = this.toolbar.find('.toolbar').filter(function(){
                    var button = $(this);
                    return button.data('action');
                });

                var self = this, token_name = this.form.data('token-name'), token_value = this.form.data('token-value');
                this.buttons.each(function(){
                    var button = $(this), data = button.data('data'), options = self.getOptions(button), action = button.data('action');
                    data = (data && $.type(data) === 'string') ? eval('(' + data + ')') : {};

                    //Set token data
                    if(token_name) {
                        data[token_name] = token_value;
                    }

                    button.on('click', function(event){
                        event.preventDefault();
                        if(!button.hasClass('disabled')) {
                            self.setOptions(options);
                            self.fireEvent('execute', [action, data, button.get('data-novalidate') === 'novalidate']);
                        }
                    });
                });
            }
        },

        execute: function(event, action, data, novalidate){
            var method = '_action'+action.capitalize();

            this.options.action = action;
            if(this.fireEvent('before.'+action, [data, novalidate])) {
                if(this[method]) {
                    this[method].call(this, data, novalidate);
                } else {
                    this._action_default.call(this, action, data, novalidate);
                }
                this.fireEvent('after.'+action, [data, novalidate]);
            }

            return this;
        },

        /* @TODO refactor to use jQuery.fn.on, but keep addEvent for legacy */
        addEvent: function(type, fn){
            // @TODO test if this.form.on(type, fn) works just as good as this code
            return this.form.on.apply(this.form, [type, fn]);
        },

        /* @TODO refactor to use jQuery.fn.on, but keep addEvent for legacy */
        removeEvent: function(type, fn){
            // @TODO test if this.form.on(type, fn) works just as good as this code
            return this.form.off.apply(this.form, [type, fn]);
        },

        fireEvent: function(type, args){
            var event = jQuery.Event(type);
            this.form.trigger(event, args);
            return !event.isDefaultPrevented();
        },

        checkValidity: function(){
            if(this.buttons) {
                this.fireEvent('before.validate');
                var buttons = this.buttons.filter(function(){
                    var button = $(this);
                    return button.data('novalidate') !== 'novalidate';
                });

                /* We use a class for this state instead of a data attribute because not all browsers supports attribute selectors */
                if(this.fireEvent('validate')) {
                    buttons.removeClass('disabled');
                } else {
                    buttons.addClass('disabled');
                }
                this.fireEvent('after.validate');
            }
        },

        //@TODO using jQuery.data(), html5 data attributes are automatically supported, this method is no longer needed
        getOptions: function(element){
            return element.data();
        }
    });

    /**
     * Controller class specialized for grids, extends Koowa.Controller
     *
     * @package     Koowa_Media
     * @subpackage  Javascript
     */
    Koowa.Controller.Grid = new Koowa.Class({

        Extends: Koowa.Controller,

        options: {
            inputs: '.-koowa-grid-checkbox, .-koowa-grid-checkall'
        },

        initialize: function(options){

            this.parent(options);

            this.addEvent('validate', this.validate);

            //Perform grid validation and set the right classes on toolbar buttons
            if(this.options.inputs && this.buttons) {
                this.checkValidity();
                //@TODO rewrite to use .delegate like functionality
                this.form.find(this.options.inputs).on('change', function(event, ignore){
                    if(!ignore) this.checkValidity();
                }.bind(this));
            }

            //Make the table headers "clickable"
            var thead = this.form.find('thead').filter(function(i, thead){
                var $thead = $(thead);
                return $thead.siblings().length;
            }).each(function(i, thead){
                $(thead).find('tr > *').each(function(i, el){
                    var element = $(el), link = element.find('a');
                    if(link.length) {
                        element.on('click', function(event){
                            //Don't do anything if the event target is the same as the element
                            if(event.target != el) return;

                            //Run this check on click, so that progressive enhancements isn't bulldozed
                            if(link.prop('href')) {
                                window.location.href = link.prop('href');
                            } else {
                                link.trigger('click', event);
                            }
                        });
                        if(link.hasClass('-koowa-asc'))  element.addClass('-koowa-asc');
                        if(link.hasClass('-koowa-desc')) element.addClass('-koowa-desc');

                        return this;
                    }

                    //Making the <td> or <th> element that's the parent of a checkall checkbox toggle the checkbox when clicked
                    var checkall = element.find('.-koowa-grid-checkall');
                    if(checkall.length) {
                        element.on('click', function(event){
                            //Don't do anything if the event target is the same as the element
                            if(event.target != el) return true;

                            //Checkall uses change for other purposes
                            checkall.prop('checked', checkall.is(':checked') ? false : true).trigger('change');
                        });

                        return;
                    }

                    element.addClass('void');
                });
            });

            //<select> elements in headers and footers are for filters, so they need to submit the form on change
            var selects = this.form.find('thead select, tfoot select');
            if(this.options.ajaxify) {
                selects.on('change', function(event){
                    event.preventDefault();
                    //@TODO still mootools depending
                    this.options.transport(this.form.prop('action'), this.form.serialize(), 'get');
                }.bind(this));
            } else if(selects.length) {
                selects.on('change', function(){
                    //@TODO make jquery
                    this.form.submit();
                }.bind(this));
            }

            //Pick up actions that are in the grid itself
            var token_name = this.form.data('tokenName'),
                token_value = this.form.data('tokenValue'),
                checkboxes = this.form.find('tbody tr .-koowa-grid-checkbox');
            this.form.find('tbody tr').each(function(i, el){
                var tr = $(el);
                //skip rows that are readonly
                if(tr.data('readonly') == true) {
                    return;
                }

                var checkbox = tr.find('.-koowa-grid-checkbox'), id, actions;
                if(!checkbox.length) {
                    return;
                }

                tr.on('click', function(event){
                    if($(event.target).hasClass('toggle-state') || $(event.target).is('[type=checkbox]')) return;
                    var checkbox = $(this).find('input[type=checkbox]'), checked = checkbox.prop('checked');
                    if(checked) {
                        $(this).removeClass('selected');
                        checkbox.prop('checked', false);
                    } else {
                        $(this).addClass('selected');
                        checkbox.prop('checked', true);
                    }
                    checkbox.trigger('change');
                }).on('dblclick', function(event){
                    if($(event.target).is('a') || $(event.target).is('td') || event.target == this) {
                        window.location.href = $(this).find('a').prop('href');
                    }
                }).on('contextmenu', function(event){
                    var modal = $(this).find('a.modal');
                    if(modal) {
                        event.preventDefault();
                        modal.trigger('click');
                    }
                });


                checkbox.on('change', function(){
                    this.prop('checked') ? tr.addClass('selected') : tr.removeClass('selected');
                    var selected = tr.hasClass('selected') + tr.siblings('.selected').length, parent = tr.parent();
                    if(selected > 1) {
                        parent.addClass('selected-multiple').removeClass('selected-single')
                    } else {
                        parent.removeClass('selected-multiple').addClass('selected-single');
                    }
                }.bind(checkbox)).trigger('change', true);


                id = {name: checkbox.prop('name'), value: checkbox.prop('value')};
                //Attributes with hyphens don't work with the MT 1.2 selector engine, it's fixed in 1.3 so this is a workaround
                actions = tr.find('*').filter(function(i, action){
                    var $action = $(action);
                    return $action.data('action');
                });

                actions.each(function(i, el){
                    var action = $(el),
                        data = action.data('data'),
                        options = this.getOptions(action),
                        actionName = action.data('action'),
                        eventType = action.data('eventType'),
                        onchange;

                    data = (data && $.type(data) === 'string') ? eval('(' + data + ')') : {};

                    //Set token data
                    if(token_name) {
                        data[token_name] = token_value;
                    }

                    if(!eventType) {
                        eventType = action.is('[type="radio"],[type="checkbox"],select') ? 'change' : 'click';
                    }

                    action.on(eventType, function(){
                        checkboxes.prop('checked', '');
                        checkbox.prop('checked', 'checked');
                        checkboxes.trigger('change', true);

                        this.setOptions(options);
                        this.fireEvent('execute', [actionName, data, true]);
                    }.bind(this));


                }.bind(this));

            }.bind(this));
        },

        validate: function(){
            return Koowa.Grid.getIdQuery() || false;
        },

        _action_default: function(action, data, novalidate){
            if(!novalidate && !this.fireEvent('validate')) {
                return false;
            }

            var idQuery = Koowa.Grid.getIdQuery(),
                append = this.options.url.match(/\?/) ? '&' : '?',
                options = {
                    method:'post',
                    url: this.options.url+(idQuery ? append+idQuery : ''),
                    params: $.extend({}, {action: action}, data)
                };
            new Koowa.Form(options).submit();
        }

    });

    /**
     * Controller class specialized for forms, extends Koowa.Controller
     *
     * @package     Koowa_Media
     * @subpackage  Javascript
     */
    Koowa.Controller.Form = new Koowa.Class({

        Extends: Koowa.Controller,

        _action_default: function(action, data, novalidate){
            if(!novalidate && !this.fireEvent('validate')) {
                return false;
            }

            this.form.append($('<input/>', {name: 'action', type: 'hidden', value: action}));
            this.form.submit();
        }

    });

    /**
     * Overlay class
     *
     * @package     Koowa_Media
     * @subpackage  Javascript
     */
    //@TODO this bit still requires MooTools
    if(this.MooTools) {
    Koowa.Overlay = new Class({
        Extends: Request,
        element : null,

        options: {
            selector: 'body',
            ajaxify: true,
            method: 'get',
            evalScripts: true,
            evalStyles: true,

            onComplete: function() {
                var element = new Element('div', {html: this.response.text}),
                    body = element.getElement(this.options.selector) || element,
                    self = this,
                    scripts,
                    styles;

                this.element.empty().grab(body);

                if (this.options.evalScripts) {
                    scripts = element.getElements('script[type=text/javascript]');
                    scripts = scripts.filter(function(script) {
                        if(!script.src) return false;
                        if(document.head.getElement('script[src$='+script.src.replace(location.origin, '')+']')) return false;
                        return true;
                    });
                    if(scripts.length) {
                        var self = this, script = scripts.shift(), loadScript = function(script){
                            new Asset.javascript(script.src, {id: script.id, onload: function(){
                                if(scripts.length) {
                                    script = scripts.shift();
                                    loadScript(script);
                                } else {
                                    //Remove existing domready events as they've fired by now anyway
                                    delete window.retrieve('events').domready;

                                    if(self._tmp_scripts) {
                                        $exec(self._tmp_scripts);
                                    }

                                    window.fireEvent('domready');
                                }
                            }});
                        };
                        loadScript(script);
                    };
                }

                if (this.options.evalStyles) {
                    styles = element.getElements('link[type=text/css]');
                    styles.each(function(style) {
                        new Asset.css(style.href, {id: style.id });
                    }.bind(this));
                }

                if (this.options.ajaxify) {
                    this.element.getElements('a[href]').each(function(link){
                        //Avoid links with data-noasync attributes
                        if(link.getAttribute('data-noasync') !== null) return;
                        link.addEvent('click', function(event){
                            event.stop();
                            self.get(this.href, {tmpl:''});
                        });
                    });

                    /* @TODO
                     this.element.getElements('.submitable').addEvent('click', function(event){
                     event = new Event(event);
                     new Koowa.Form(JSON.decode(event.target.getProperty('rel'))).submit();
                     });
                     */

                    this.element.getElements('.-koowa-grid').each(function(grid){
                        new Koowa.Grid(grid);

                        new Koowa.Controller.Grid({form: grid, ajaxify: true, transport: function(url, data, method){
                            data += '&tmpl=';
                            this.send({url: url, data: data, method: method});
                        }.bind(this)});
                    }, this);

                    this.element.getElements('.-koowa-form').each(function(form){
                        new Koowa.Controller.Form({form: form, ajaxify: true, transport: function(url, data, method){
                            data += '&tmpl=';
                            this.send({url: url, data: data, method: method});
                        }.bind(this)});
                    }, this);
                }
            }
        },

        initialize: function(element, options) {
            if(typeof options === 'string') {
                options = JSON.evaluate(options);
            }

            this.element = document.id(element);

            this.options.url = this.element.getAttribute('data-url');
            this.parent(options);

            this.send();
        },

        processScripts: function(text){
            if(this.options.evalScripts) {
                var scripts, text = text.replace(/<script[^>]*>([\s\S]*?)<\/script>/gi, function(){
                    scripts += arguments[1] + '\n';
                    return '';
                });
                this._tmp_scripts = scripts;
            }
            return this.parent(text);
        }
    });
    } else {
        if(this.console) {
            console.log('You cannot use Koowa.Overlay yet without loading MooTools');
        }
    }

})(jQuery);