var globalCacheForjQueryReplacement = window.jQuery;
window.jQuery = window.kQuery;
/*! jQuery UI - v1.11.4 - 2016-01-08
 * http://jqueryui.com
 * Includes: widget.js
 * Copyright jQuery Foundation and other contributors; Licensed MIT */

(function( factory ) {
    if ( typeof define === "function" && define.amd ) {

        // AMD. Register as an anonymous module.
        define([ "jquery" ], factory );
    } else {

        // Browser globals
        factory( kQuery );
    }
}(function( $ ) {
    /*!
     * jQuery UI Widget 1.11.4
     * http://jqueryui.com
     *
     * Copyright jQuery Foundation and other contributors
     * Released under the MIT license.
     * http://jquery.org/license
     *
     * http://api.jqueryui.com/jQuery.widget/
     */


    var widget_uuid = 0,
        widget_slice = Array.prototype.slice;

    $.cleanData = (function( orig ) {
        return function( elems ) {
            var events, elem, i;
            for ( i = 0; (elem = elems[i]) != null; i++ ) {
                try {

                    // Only trigger remove when necessary to save time
                    events = $._data( elem, "events" );
                    if ( events && events.remove ) {
                        $( elem ).triggerHandler( "remove" );
                    }

                    // http://bugs.jquery.com/ticket/8235
                } catch ( e ) {}
            }
            orig( elems );
        };
    })( $.cleanData );

    $.widget = function( name, base, prototype ) {
        var fullName, existingConstructor, constructor, basePrototype,
        // proxiedPrototype allows the provided prototype to remain unmodified
        // so that it can be used as a mixin for multiple widgets (#8876)
            proxiedPrototype = {},
            namespace = name.split( "." )[ 0 ];

        name = name.split( "." )[ 1 ];
        fullName = namespace + "-" + name;

        if ( !prototype ) {
            prototype = base;
            base = $.Widget;
        }

        // create selector for plugin
        $.expr[ ":" ][ fullName.toLowerCase() ] = function( elem ) {
            return !!$.data( elem, fullName );
        };

        $[ namespace ] = $[ namespace ] || {};
        existingConstructor = $[ namespace ][ name ];
        constructor = $[ namespace ][ name ] = function( options, element ) {
            // allow instantiation without "new" keyword
            if ( !this._createWidget ) {
                return new constructor( options, element );
            }

            // allow instantiation without initializing for simple inheritance
            // must use "new" keyword (the code above always passes args)
            if ( arguments.length ) {
                this._createWidget( options, element );
            }
        };
        // extend with the existing constructor to carry over any static properties
        $.extend( constructor, existingConstructor, {
            version: prototype.version,
            // copy the object used to create the prototype in case we need to
            // redefine the widget later
            _proto: $.extend( {}, prototype ),
            // track widgets that inherit from this widget in case this widget is
            // redefined after a widget inherits from it
            _childConstructors: []
        });

        basePrototype = new base();
        // we need to make the options hash a property directly on the new instance
        // otherwise we'll modify the options hash on the prototype that we're
        // inheriting from
        basePrototype.options = $.widget.extend( {}, basePrototype.options );
        $.each( prototype, function( prop, value ) {
            if ( !$.isFunction( value ) ) {
                proxiedPrototype[ prop ] = value;
                return;
            }
            proxiedPrototype[ prop ] = (function() {
                var _super = function() {
                        return base.prototype[ prop ].apply( this, arguments );
                    },
                    _superApply = function( args ) {
                        return base.prototype[ prop ].apply( this, args );
                    };
                return function() {
                    var __super = this._super,
                        __superApply = this._superApply,
                        returnValue;

                    this._super = _super;
                    this._superApply = _superApply;

                    returnValue = value.apply( this, arguments );

                    this._super = __super;
                    this._superApply = __superApply;

                    return returnValue;
                };
            })();
        });
        constructor.prototype = $.widget.extend( basePrototype, {
            // TODO: remove support for widgetEventPrefix
            // always use the name + a colon as the prefix, e.g., draggable:start
            // don't prefix for widgets that aren't DOM-based
            widgetEventPrefix: existingConstructor ? (basePrototype.widgetEventPrefix || name) : name
        }, proxiedPrototype, {
            constructor: constructor,
            namespace: namespace,
            widgetName: name,
            widgetFullName: fullName
        });

        // If this widget is being redefined then we need to find all widgets that
        // are inheriting from it and redefine all of them so that they inherit from
        // the new version of this widget. We're essentially trying to replace one
        // level in the prototype chain.
        if ( existingConstructor ) {
            $.each( existingConstructor._childConstructors, function( i, child ) {
                var childPrototype = child.prototype;

                // redefine the child widget using the same prototype that was
                // originally used, but inherit from the new version of the base
                $.widget( childPrototype.namespace + "." + childPrototype.widgetName, constructor, child._proto );
            });
            // remove the list of existing child constructors from the old constructor
            // so the old child constructors can be garbage collected
            delete existingConstructor._childConstructors;
        } else {
            base._childConstructors.push( constructor );
        }

        $.widget.bridge( name, constructor );

        return constructor;
    };

    $.widget.extend = function( target ) {
        var input = widget_slice.call( arguments, 1 ),
            inputIndex = 0,
            inputLength = input.length,
            key,
            value;
        for ( ; inputIndex < inputLength; inputIndex++ ) {
            for ( key in input[ inputIndex ] ) {
                value = input[ inputIndex ][ key ];
                if ( input[ inputIndex ].hasOwnProperty( key ) && value !== undefined ) {
                    // Clone objects
                    if ( $.isPlainObject( value ) ) {
                        target[ key ] = $.isPlainObject( target[ key ] ) ?
                            $.widget.extend( {}, target[ key ], value ) :
                            // Don't extend strings, arrays, etc. with objects
                            $.widget.extend( {}, value );
                        // Copy everything else by reference
                    } else {
                        target[ key ] = value;
                    }
                }
            }
        }
        return target;
    };

    $.widget.bridge = function( name, object ) {
        var fullName = object.prototype.widgetFullName || name;
        $.fn[ name ] = function( options ) {
            var isMethodCall = typeof options === "string",
                args = widget_slice.call( arguments, 1 ),
                returnValue = this;

            if ( isMethodCall ) {
                this.each(function() {
                    var methodValue,
                        instance = $.data( this, fullName );
                    if ( options === "instance" ) {
                        returnValue = instance;
                        return false;
                    }
                    if ( !instance ) {
                        return $.error( "cannot call methods on " + name + " prior to initialization; " +
                            "attempted to call method '" + options + "'" );
                    }
                    if ( !$.isFunction( instance[options] ) || options.charAt( 0 ) === "_" ) {
                        return $.error( "no such method '" + options + "' for " + name + " widget instance" );
                    }
                    methodValue = instance[ options ].apply( instance, args );
                    if ( methodValue !== instance && methodValue !== undefined ) {
                        returnValue = methodValue && methodValue.jquery ?
                            returnValue.pushStack( methodValue.get() ) :
                            methodValue;
                        return false;
                    }
                });
            } else {

                // Allow multiple hashes to be passed on init
                if ( args.length ) {
                    options = $.widget.extend.apply( null, [ options ].concat(args) );
                }

                this.each(function() {
                    var instance = $.data( this, fullName );
                    if ( instance ) {
                        instance.option( options || {} );
                        if ( instance._init ) {
                            instance._init();
                        }
                    } else {
                        $.data( this, fullName, new object( options, this ) );
                    }
                });
            }

            return returnValue;
        };
    };

    $.Widget = function( /* options, element */ ) {};
    $.Widget._childConstructors = [];

    $.Widget.prototype = {
        widgetName: "widget",
        widgetEventPrefix: "",
        defaultElement: "<div>",
        options: {
            disabled: false,

            // callbacks
            create: null
        },
        _createWidget: function( options, element ) {
            element = $( element || this.defaultElement || this )[ 0 ];
            this.element = $( element );
            this.uuid = widget_uuid++;
            this.eventNamespace = "." + this.widgetName + this.uuid;

            this.bindings = $();
            this.hoverable = $();
            this.focusable = $();

            if ( element !== this ) {
                $.data( element, this.widgetFullName, this );
                this._on( true, this.element, {
                    remove: function( event ) {
                        if ( event.target === element ) {
                            this.destroy();
                        }
                    }
                });
                this.document = $( element.style ?
                    // element within the document
                    element.ownerDocument :
                    // element is window or document
                element.document || element );
                this.window = $( this.document[0].defaultView || this.document[0].parentWindow );
            }

            this.options = $.widget.extend( {},
                this.options,
                this._getCreateOptions(),
                options );

            this._create();
            this._trigger( "create", null, this._getCreateEventData() );
            this._init();
        },
        _getCreateOptions: $.noop,
        _getCreateEventData: $.noop,
        _create: $.noop,
        _init: $.noop,

        destroy: function() {
            this._destroy();
            // we can probably remove the unbind calls in 2.0
            // all event bindings should go through this._on()
            this.element
                .unbind( this.eventNamespace )
                .removeData( this.widgetFullName )
                // support: jquery <1.6.3
                // http://bugs.jquery.com/ticket/9413
                .removeData( $.camelCase( this.widgetFullName ) );
            this.widget()
                .unbind( this.eventNamespace )
                .removeAttr( "aria-disabled" )
                .removeClass(
                    this.widgetFullName + "-disabled " +
                    "ui-state-disabled" );

            // clean up events and states
            this.bindings.unbind( this.eventNamespace );
            this.hoverable.removeClass( "ui-state-hover" );
            this.focusable.removeClass( "ui-state-focus" );
        },
        _destroy: $.noop,

        widget: function() {
            return this.element;
        },

        option: function( key, value ) {
            var options = key,
                parts,
                curOption,
                i;

            if ( arguments.length === 0 ) {
                // don't return a reference to the internal hash
                return $.widget.extend( {}, this.options );
            }

            if ( typeof key === "string" ) {
                // handle nested keys, e.g., "foo.bar" => { foo: { bar: ___ } }
                options = {};
                parts = key.split( "." );
                key = parts.shift();
                if ( parts.length ) {
                    curOption = options[ key ] = $.widget.extend( {}, this.options[ key ] );
                    for ( i = 0; i < parts.length - 1; i++ ) {
                        curOption[ parts[ i ] ] = curOption[ parts[ i ] ] || {};
                        curOption = curOption[ parts[ i ] ];
                    }
                    key = parts.pop();
                    if ( arguments.length === 1 ) {
                        return curOption[ key ] === undefined ? null : curOption[ key ];
                    }
                    curOption[ key ] = value;
                } else {
                    if ( arguments.length === 1 ) {
                        return this.options[ key ] === undefined ? null : this.options[ key ];
                    }
                    options[ key ] = value;
                }
            }

            this._setOptions( options );

            return this;
        },
        _setOptions: function( options ) {
            var key;

            for ( key in options ) {
                this._setOption( key, options[ key ] );
            }

            return this;
        },
        _setOption: function( key, value ) {
            this.options[ key ] = value;

            if ( key === "disabled" ) {
                this.widget()
                    .toggleClass( this.widgetFullName + "-disabled", !!value );

                // If the widget is becoming disabled, then nothing is interactive
                if ( value ) {
                    this.hoverable.removeClass( "ui-state-hover" );
                    this.focusable.removeClass( "ui-state-focus" );
                }
            }

            return this;
        },

        enable: function() {
            return this._setOptions({ disabled: false });
        },
        disable: function() {
            return this._setOptions({ disabled: true });
        },

        _on: function( suppressDisabledCheck, element, handlers ) {
            var delegateElement,
                instance = this;

            // no suppressDisabledCheck flag, shuffle arguments
            if ( typeof suppressDisabledCheck !== "boolean" ) {
                handlers = element;
                element = suppressDisabledCheck;
                suppressDisabledCheck = false;
            }

            // no element argument, shuffle and use this.element
            if ( !handlers ) {
                handlers = element;
                element = this.element;
                delegateElement = this.widget();
            } else {
                element = delegateElement = $( element );
                this.bindings = this.bindings.add( element );
            }

            $.each( handlers, function( event, handler ) {
                function handlerProxy() {
                    // allow widgets to customize the disabled handling
                    // - disabled as an array instead of boolean
                    // - disabled class as method for disabling individual parts
                    if ( !suppressDisabledCheck &&
                        ( instance.options.disabled === true ||
                        $( this ).hasClass( "ui-state-disabled" ) ) ) {
                        return;
                    }
                    return ( typeof handler === "string" ? instance[ handler ] : handler )
                        .apply( instance, arguments );
                }

                // copy the guid so direct unbinding works
                if ( typeof handler !== "string" ) {
                    handlerProxy.guid = handler.guid =
                        handler.guid || handlerProxy.guid || $.guid++;
                }

                var match = event.match( /^([\w:-]*)\s*(.*)$/ ),
                    eventName = match[1] + instance.eventNamespace,
                    selector = match[2];
                if ( selector ) {
                    delegateElement.delegate( selector, eventName, handlerProxy );
                } else {
                    element.bind( eventName, handlerProxy );
                }
            });
        },

        _off: function( element, eventName ) {
            eventName = (eventName || "").split( " " ).join( this.eventNamespace + " " ) +
                this.eventNamespace;
            element.unbind( eventName ).undelegate( eventName );

            // Clear the stack to avoid memory leaks (#10056)
            this.bindings = $( this.bindings.not( element ).get() );
            this.focusable = $( this.focusable.not( element ).get() );
            this.hoverable = $( this.hoverable.not( element ).get() );
        },

        _delay: function( handler, delay ) {
            function handlerProxy() {
                return ( typeof handler === "string" ? instance[ handler ] : handler )
                    .apply( instance, arguments );
            }
            var instance = this;
            return setTimeout( handlerProxy, delay || 0 );
        },

        _hoverable: function( element ) {
            this.hoverable = this.hoverable.add( element );
            this._on( element, {
                mouseenter: function( event ) {
                    $( event.currentTarget ).addClass( "ui-state-hover" );
                },
                mouseleave: function( event ) {
                    $( event.currentTarget ).removeClass( "ui-state-hover" );
                }
            });
        },

        _focusable: function( element ) {
            this.focusable = this.focusable.add( element );
            this._on( element, {
                focusin: function( event ) {
                    $( event.currentTarget ).addClass( "ui-state-focus" );
                },
                focusout: function( event ) {
                    $( event.currentTarget ).removeClass( "ui-state-focus" );
                }
            });
        },

        _trigger: function( type, event, data ) {
            var prop, orig,
                callback = this.options[ type ];

            data = data || {};
            event = $.Event( event );
            event.type = ( type === this.widgetEventPrefix ?
                type :
            this.widgetEventPrefix + type ).toLowerCase();
            // the original event may come from any element
            // so we need to reset the target on the new event
            event.target = this.element[ 0 ];

            // copy original event properties over to the new event
            orig = event.originalEvent;
            if ( orig ) {
                for ( prop in orig ) {
                    if ( !( prop in event ) ) {
                        event[ prop ] = orig[ prop ];
                    }
                }
            }

            this.element.trigger( event, data );
            return !( $.isFunction( callback ) &&
            callback.apply( this.element[0], [ event ].concat( data ) ) === false ||
            event.isDefaultPrevented() );
        }
    };

    $.each( { show: "fadeIn", hide: "fadeOut" }, function( method, defaultEffect ) {
        $.Widget.prototype[ "_" + method ] = function( element, options, callback ) {
            if ( typeof options === "string" ) {
                options = { effect: options };
            }
            var hasOptions,
                effectName = !options ?
                    method :
                    options === true || typeof options === "number" ?
                        defaultEffect :
                    options.effect || defaultEffect;
            options = options || {};
            if ( typeof options === "number" ) {
                options = { duration: options };
            }
            hasOptions = !$.isEmptyObject( options );
            options.complete = callback;
            if ( options.delay ) {
                element.delay( options.delay );
            }
            if ( hasOptions && $.effects && $.effects.effect[ effectName ] ) {
                element[ method ]( options );
            } else if ( effectName !== method && element[ effectName ] ) {
                element[ effectName ]( options.duration, options.easing, callback );
            } else {
                element.queue(function( next ) {
                    $( this )[ method ]();
                    if ( callback ) {
                        callback.call( element[ 0 ] );
                    }
                    next();
                });
            }
        };
    });

    var widget = $.widget;



}));
;(function(window, document, $) {

    $.widget("koowa.scopebar", {

        widgetEventPrefix: 'scopebar:',

        options: {
            template: function() {

            }
        },

        _create: function() {
            var prototype = $('.k-js-filter-prototype');

            this.template = prototype.clone();
            this.template.removeClass('.k-js-filter-prototype');

            prototype.remove();

            this._addEvents();

            var container = $('.k-js-filter-container');

            $('.k-js-filters div[data-filter]').each(function(i, item) {
                var template = prototype.clone();

                item = $(this);

                item.addClass('k-js-dropdown-content k-scopebar-dropdown__body__content');

                template.find('.k-js-dropdown-body').prepend(item);
                template.find('.k-js-dropdown-title').html(item.data('title'));

                var dropdown_button = template.find('.k-js-dropdown-button'),
                    tooltip = dropdown_button.data('tooltip-title');

                if (tooltip) {
                    tooltip = tooltip.replace('%s', item.data('title'));

                    dropdown_button.ktooltip({
                        "container":".k-ui-container",
                        "delay":{"show":500,"hide":50},
                        'title': tooltip
                    });
                }


                var label_el = template.find('.k-js-dropdown-label'),
                    label = item.data('label'),
                    count = item.data('count');

                if (count && count > 0) {
                    label = count;
                }

                if (label) {
                    label_el.attr('data-has-label', '').html(label);
                } else {
                    label_el.removeAttr('data-has-label').hide();
                }

                item.show();
                template.show();

                container.append(template);

                var length   = container.find('.k-js-dropdown-label[data-has-label]').length,
                    count_el = $('.k-js-filter-count');

                if (length) {
                    count_el.show();
                } else {
                    count_el.hide();
                }
            });
        },

        _addEvents: function() {
            // Dropdown menu
            var self = this,
                hasActive = function() {
                    return $('.k-js-dropdown').hasClass('k-is-active');
                };

            // Keyboard navigation
            $(document).keyup(function (e) {
                // Go to next dropdown with right arrow
                if (e.keyCode == 39 && hasActive()) {
                    var nextItem = $('.k-js-dropdown.k-is-active').next().find($('.k-js-dropdown-button'));

                    if ( nextItem.hasClass('k-js-dropdown-button') ) {
                        // Close active item
                        self.closeDropdown();

                        // Open hovered item
                        self.openDropdown(nextItem);
                    }

                }

                // Go to previous dropdown with left arrow
                if (e.keyCode == 37 && hasActive()) {
                    var prevItem = $('.k-js-dropdown.k-is-active').prev().find($('.k-js-dropdown-button'));

                    if ( prevItem.hasClass('k-js-dropdown-button') ) {
                        // Close active item
                        self.closeDropdown();

                        // Open hovered item
                        self.openDropdown(prevItem);
                    }

                }

                // Close dropdown on esc key
                if (e.keyCode == 27 && hasActive()) {
                    self.closeDropdown();
                }
            });

            // Close dropdown on clicking outside
            // Do not close item on clicking the dropdown body
            $('html').click(function (event) {
                var target = $(event.target),
                    isSelect2 = event.target.className.search('select2-') !== -1,
                    isDatepicker = (target.parents('.datepicker-dropdown').length > 0 || (target.is('td') && target.hasClass('day')));

                if (!isSelect2 && !isDatepicker && target.parents('.k-js-filter-container').length === 0) {
                    self.closeDropdown();
                }
            });

            this.element.on('click', '*', function(event) {
                var button = $(event.target);

                if (!button.hasClass('k-js-dropdown-button')) {
                    button = button.parents('.k-js-dropdown-button');
                }

                if (button.length === 0) {
                    return;
                }

                if (button.parent().hasClass('k-is-active')) {
                    self.closeDropdown();
                } else {
                    self.openDropdown(button);
                }

                event.stopPropagation();
            });

            this.element.on('mouseenter mouseleave', '*', function(event) {
                var button = $(event.target);

                if (!button.hasClass('k-js-dropdown-button')) {
                    button = button.parents('.k-js-dropdown-button');
                }

                if (button.length === 0) {
                    return;
                }

                // Check if any dropdown is active
                // Check if the hovered item isn't the active item
                if (hasActive() && (!button.parent().hasClass('k-is-active')) ) {
                    // Close active item
                    self.closeDropdown();

                    // Open hovered item
                    self.openDropdown(button);

                    // Set focus to hovered item
                    button.focus();
                }
            });

            submitForm = function(form, box) {
                box.find('select').each(function(i, select) {
                    var value = $(select).val();

                    if (!value || value === '' || (typeof value === 'object' && value.length === 1 && value[0] === '')) {
                        var name = $(select).attr('name');
                        name = name.replace('[]', '');
                        $(select).removeAttr('name');
                        $(form).append('<input type="hidden" name="'+name+'" value="" />');
                    }
                });

                self._trigger('beforeSubmitForm', null, {form: form, box: box});

                form.submit();
            };

            this.element.on('click', '.k-js-clear-filter', function(event) {
                event.preventDefault();

                var box = $(event.target).parents('.k-js-dropdown');

                box.find(':input')
                    .not(':button, :submit, :reset, :hidden')
                    .removeAttr('checked')
                    .removeAttr('selected')
                    .not(':checkbox, :radio')
                    .val('')
                    .filter('select').trigger('change'); // For select2

                var form = event.target.form;

                if (form) {
                    submitForm(form, box);
                }

            }).on('click', '.k-js-apply-filter', function(event) {
                event.preventDefault();

                var form = event.target.form,
                    box = $(event.target).parents('.k-js-dropdown');

                if (form) {
                    submitForm(form, box);
                }
            });
        },

        openDropdown: function(element) {
            var parent = element.parent();

            this.closeDropdown();

            // Set active class to parent
            parent.addClass('k-is-active');

            // Find select elements in dropdown
            var select = parent.find('select');

            // Also open select2 when opening dropdown
            if (select.length === 1 && select.data('select2')) {
                //select.select2('open');
            }

            element.focus();
        },

        closeDropdown: function() {
            // Find active dropdown
            var activeItem = $('.k-js-dropdown.k-is-active');

            // Find select elements in active dropdown
            var select = activeItem.find('select');

            // Remove active class from active item
            activeItem.removeClass('k-is-active');

            // Close select2 when closing dropdown
            if (select.data('select2')) {
                select.select2('close');
            }
        }

    });

} (window, document, kQuery));
/**
 * Joomlatools Framework - https://www.joomlatools.com/developer/framework/
 *
 * @copyright	Copyright (C) 2015 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		https://github.com/joomlatools/joomlatools-framework for the canonical source repository
 */

if(!Koowa) {
    /** @namespace */
    var Koowa = {};
}


/**
 * Polyfill for Function.prototype.bind, for older browsers that don't implement it natively
 */
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

/*!
 * klass: a classical JS OOP façade
 * https://github.com/ded/klass
 * License MIT (c) Dustin Diaz & Jacob Thornton 2012
 */
var klass = (function () {
    var context = this
        , old = context.klass
        , f = 'function'
        , fnTest = /xyz/.test(function () {xyz}) ? /\bsupr\b/ : /.*/
        , proto = 'prototype';



    function klass(o) {
        return extend.call(isFn(o) ? o : function () {}, o, 1)
    }

    function isFn(o) {
        return typeof o === f
    }

    function wrap(k, fn, supr) {
        return function () {
            var tmp = this.supr;
            this.supr = supr[proto][k];
            var undef = {}.fabricatedUndefined;
            var ret = undef;
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
        noop[proto] = this[proto];
        var supr = this
            , prototype = new noop()
            , isFunction = isFn(o)
            , _constructor = isFunction ? o : this
            , _methods = isFunction ? {} : o;
        function fn() {
            if (this.initialize) this.initialize.apply(this, arguments);
            else {
                fromSub || isFunction && supr.apply(this, arguments);
                _constructor.apply(this, arguments);
            }
        }

        fn.methods = function (o) {
            process(prototype, o, supr);
            fn[proto] = prototype;
            return this
        };

        fn.methods.call(fn, _methods).prototype.constructor = fn;

        fn.extend = arguments.callee;
        fn[proto].implement = fn.statics = function (o, optFn) {
            o = typeof o == 'string' ? (function () {
                var obj = {};
                obj[o] = optFn;
                return obj
            }()) : o;
            process(this, o, supr);
            return this
        };

        return fn
    }

    klass.noConflict = function () {
        context.klass = old;
        return this
    };

    return klass
})();

(function($) {

/** @class */
Koowa.Class = klass({

    /**
     * @memberOf Koowa.Class#
     */
    options: {},
    /**
     * @returns {object}
     */
    getOptions: function() {
        return {};
    },

    /**
     * @constructs Koowa.Class
     * */
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

})(window.kQuery);

/**
 * Joomlatools Framework - https://www.joomlatools.com/developer/framework/
 *
 * @copyright	Copyright (C) 2015 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		https://github.com/joomlatools/joomlatools-framework for the canonical source repository
 */

if (typeof Koowa === 'undefined') {
    Koowa = {};
}


(function($){
/**
 * Grid class
 */
Koowa.Grid = Koowa.Class.extend({
    initialize: function(element){
        var self = this;

        this.element    = $(element);
        this.form       = this.element.is('form') ? this.element : this.element.closest('form');
        this.checkall   = this.element.find('.k-js-grid-checkall');
        this.checkboxes = this.element.find('.k-js-grid-checkbox').filter(function(i, checkbox) {
            return !$(checkbox).prop('disabled');
        });

        if(!this.checkboxes.length) {
            this.checkall.prop('disabled', true);
        }

        this.checkall.on('change.koowa', function(event, ignore){
            if(!ignore) {
                self.checkAll($(this).prop('checked'));
            }
        });

        this.checkboxes.on('change.koowa', function(event, ignore){
            if(!ignore) {
                self.setCheckAll();
            }
        });

        this.setScopebar();
        this.setTableHeaders();
        this.setTableRows();
    },
    setScopebar: function() {
        var self = this;
        $('.k-js-filter-container', this.form).scopebar({
            beforeSubmitForm: function () {
                self.uncheckAll();
            }
        });
    },
    setTableHeaders: function() {
        //Make the table headers "clickable" and make checkall work
        this.form.on('click.koowa', 'th', function(event) {
            var $target = $(event.target);
            var link     = $target.find('a');

            if (link.length) {
                //Run this check on click, so that progressive enhancements isn't bulldozed
                if(link.prop('href')) {
                    window.location.href = link.prop('href');
                } else {
                    link.trigger('click', event);
                }
            }
            else {
                var checkall = $target.find('.k-js-grid-checkall');

                if (checkall.length) {
                    checkall.prop('checked', checkall.is(':checked') ? false : true).trigger('change');
                }
            }
        });
    },
    setTableRows: function() {
        // Trigger checkbox when the user clicks anywhere in the row
        this.form.on('click.koowa', 'tr', function(event) {
            var target = $(event.target);

            if(target.is('[type=radio], [type=checkbox], a[href], span.footable-toggle')) {
                return;
            }

            var tr = target.is('tr') ? target : target.parents('tr'),
                checkbox = tr.find('.k-js-grid-checkbox');

            if(tr.data('readonly') == true || !checkbox.length) {
                return;
            }

            if (checkbox.length) {
                checkbox.prop('checked', !checkbox.prop('checked')).trigger('change');
            }
        });

        // Checkbox should add selected and selected-multiple classes to the row
        $('.k-js-grid-checkbox').on('change.koowa', function(event) {
            var selected,
                target = $(event.target),
                tr     = target.parents('tr'),
                parent = tr.parent();

            if (target.is('[type=radio]')) {
                parent.find('.k-is-selected').removeClass('k-is-selected');
            }

            $(this).prop('checked') ? tr.addClass('k-is-selected') : tr.removeClass('k-is-selected');

            selected = parent.find('.k-is-selected').length;

            if(selected > 1) {
                parent.addClass('k-has-selected-multiple').removeClass('k-has-selected-single')
            } else if (selected === 1) {
                parent.removeClass('k-has-selected-multiple').addClass('k-has-selected-single');
            } else {
                parent.removeClass('k-has-selected-multiple').removeClass('k-has-selected-single');
            }
        }).trigger('change', true);
    },
    checkAll: function(value){
        var changed = this.checkboxes.filter(function(i, checkbox){
            return $(checkbox).prop('checked') !== value;
        });

        this.checkboxes.prop('checked', value);
        changed.trigger('change', true);
    },
    uncheckAll: function() {
        this.checkAll(false);
    },
    setCheckAll: function(){
        var total = this.checkboxes.filter(function(i, checkbox){
            return $(checkbox).prop('checked') !== false;
        }).length;

        this.checkall.prop('checked', this.checkboxes.length === total);
        this.checkall.trigger('change', true);
    }
});

/**
 * Find all selected checkboxes' ids in the grid
 *
 * @param   {string|object|null} [context]   A DOM Element, Document, or jQuery to use as context
 * @return  array           The items' ids
 */
Koowa.Grid.getAllSelected = function(context) {
    return $('.k-js-grid-checkbox:checked', context);
};

/**
 * Get a query string for selected checkboxes
 *
 * @param   {string|object|null} [context]   A DOM Element, Document, or jQuery to use as context
 * @return  array           The items' ids
 */
Koowa.Grid.getIdQuery = function(context) {
    return decodeURIComponent(this.getAllSelected(context).serialize());
};

$(function() {
    $('.k-js-grid').each(function(i, el) {
        new Koowa.Grid($(el));
    })
});


})(window.kQuery);
/**
 * Joomlatools Framework - https://www.joomlatools.com/developer/framework/
 *
 * @copyright	Copyright (C) 2007 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		https://github.com/joomlatools/joomlatools-framework for the canonical source repository
 */

if(!Koowa) {
    /** @namespace */
    var Koowa = {};
}

(function($) {

$(function() {
    $('.k-js-submittable').on('click.koowa', function(event){
        event.preventDefault();

        new Koowa.Form($(event.target).data('config')).submit();
    });

    $('.k-js-grid-controller').each(function() {
        new Koowa.Controller.Grid({
            form: this
        });
    });

    $('.k-js-form-controller').each(function() {
        new Koowa.Controller.Form({
            form: this
        });
    });
});

if (!Koowa.Translator) {
    Koowa.Translator = Koowa.Class.extend({
        translations: {},
        translate: function(string, parameters) {
            if (typeof this.translations[string.toLowerCase()] !== 'undefined') {
                string = this.translations[string.toLowerCase()];
            }

            if (typeof parameters === 'object' && parameters !== null) {
                for (var key in parameters) {
                    if (parameters.hasOwnProperty(key)) {
                        // Escape for regular expression
                        var pattern = '{'+key+'}'.replace(/[\-\[\]\/\{\}\(\)\*\+\?\.\\\^\$\|]/g, "\\$&");

                        string  = string.replace(new RegExp(pattern, 'g'), parameters[key]);
                    }
                }
            }

            return string;
        },
        loadTranslations: function(object) {
            for (var string in object) {
                if (object.hasOwnProperty(string)) {
                    this.translations[string.toLowerCase()] = object[string];
                }
            }

            return this;
        }
    });

    Koowa.translator = new Koowa.Translator();
    Koowa.translate = Koowa.translator.translate.bind(Koowa.translator);
}

/**
 * Creates a 'virtual form'
 *
 * @param   {object} config Configuration object. Accepted keys: method, url, params, element
 * @example new Koowa.Form({url:'foo=bar&id=1', params:{field1:'val1', field2...}}).submit();
 * @extends Koowa.Class
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
        if ($.isArray(value)) {
            var self = this,
                n;

            if (name.substr(-2) === '[]') {
                name = name.substr(0, name.length-2);
            }

            $.each(value, function(i, v) {
                n = name+'['+i+']';
                self.addField(n, v);
            });
        } else {
            var elem = $('<input/>', {
                name: name,
                value: value,
                type: 'hidden'
            });
            elem.appendTo(this.form);
        }

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
            toolbar: '.k-toolbar',
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

                if (!button.hasClass('k-is-disabled')) {
                    var prompt = button.data('prompt');

                    if (prompt && !confirm(prompt)) {
                        return;
                    }

                    self.setOptions(options);
                    self.trigger('execute', [context]);
                }
            });

            button.data('event-added', true);
        });
    },
    execute: function(event, context){
        if (context.action[0]) {
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
        }

        return this;
    },
    on: function(type, fn){
        return this.form.on('k:'+type, fn);
    },

    off: function(type, fn){
        return this.form.off('k:'+type, fn);
    },

    trigger: function(type, args){
        var event = $.Event('k:'+type);
        this.form.trigger(event, args);
        return !event.isDefaultPrevented();
    },

    checkValidity: function(){
        var buttons;

        if (this.buttons) {
            this.trigger('beforeValidate');

            buttons = this.buttons.filter('[data-novalidate!="novalidate"]');

            if (this.trigger('validate')) {
                buttons.removeClass('k-is-disabled');
            } else {
                buttons.addClass('k-is-disabled');
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
            inputs: '.k-js-grid-checkbox, .k-js-grid-checkall',
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

        this.setTableRows();

        // <select> elements in headers and footers are for filters, so they need to submit the form on change
        this.form.find('thead select, tfoot select, .k-pagination select').on('change.koowa', function(){
            // We need to uncheck rows here otherwise only selected rows will be visible after submitting the form
            self.grid.uncheckAll();

            if (self.options.ajaxify) {
                event.preventDefault();

                self.options.transport(self.options.url, self.form.serialize(), 'get');
            }

            self.form.submit();
        });

    },


    setTableRows: function() {
        var self = this,
            checkboxes = this.form.find('tbody tr .k-js-grid-checkbox');

        this.form.find('tbody tr').each(function(){
            var tr = $(this),
                checkbox = tr.find('.k-js-grid-checkbox');

            if(tr.data('readonly') == true || !checkbox.length) {
                return;
            }

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

                action.on(eventType+'.k-ui-namespace', function(){
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

    _actionDelete: function(context) {
        context.method = 'delete';

        return this._actionDefault(context);
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
            params: $.extend({}, {_action: context.action}, context.data)
        };

        if (context.method) {
            options.params._method = context.method;
        }

        new Koowa.Form(options).submit();
    }
});

/**
 * Controller class specialized for forms, extends Koowa.Controller
 */
Koowa.Controller.Form = Koowa.Controller.extend({
    _actionDelete: function(context) {
        context.method = 'delete';

        return this._actionDefault(context);
    },
    _actionDefault: function(context){
        if (context.validate && !this.trigger('validate', [context])) {
            return false;
        }

        this.form.append($('<input/>', {name: '_action', type: 'hidden', value: context.action}));

        if (context.method) {
            this.form.append($('<input/>', {name: '_method', type: 'hidden', value: context.method}));
        }

        this.trigger('submit', [context]);
        this.form.submit();
    }

});

})(window.kQuery);

window.jQuery = globalCacheForjQueryReplacement;
globalCacheForjQueryReplacement = undefined;