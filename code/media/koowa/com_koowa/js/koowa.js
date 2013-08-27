/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2007 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa for the canonical source repository
 */

/**
 * Koowa global namespace
 *
 * @author      Johan Janssens <johan@nooku.org>
 * @category    Nooku
 * @package     Nooku_Media
 * @subpackage  Javascript
 */
if(!Koowa) {
    var Koowa = {};
}

(function($){

$(document).ready(function() {
    $('.submittable').on('click', function(event){
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

/**
 * Creates a 'virtual form'
 *
 * @param   json    Configuration:  method, url, params, formelem
 * @example new KForm({method:'post', url:'foo=bar&id=1', params:{field1:'val1', field2...}}).submit();
 */
Koowa.Form = new Class({

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
        if (this.config.params) {
            $.each(this.config.params, function(name, value){
                this.addField(name, value);
            }.bind(this));
        }

        this.form.submit();
    }
});

/**
 * Grid class
 *
 * @package     Koowa_Media
 * @subpackage  Javascript
 */
Koowa.Grid = new Class({
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
 *
 * @package     Koowa_Media
 * @subpackage  Javascript
 */
Koowa.Controller = new Class({

    Implements: [Options, Events],
    
    form: null,
    toolbar: null,
    buttons: null,

    token_name: null,
    token_value: null,

    options: {
        toolbar: '.toolbar-list',
        url: window.location.href
    },
    
    initialize: function(options){
        this.setOptions(options);

        this.form = $(this.options.form);

        this.setOptions(this.form.data());

        if (this.form.attr('action')) {
            this.options.url = this.form.attr('action');
        }

        this.toolbar = $(this.options.toolbar);
        this.form.data('controller', this);

        this.addEvent('execute', this.execute.bind(this));

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
                options = button.data(),
                data = options.data,
                action = options.action,
                novalidate = options.novalidate === 'novalidate',
                eventAdded = options.eventAdded;

            if (eventAdded) {
                return;
            }

            if (typeof data !== 'object') {
                data = (data && $.type(data) === 'string') ? $.parseJSON(data) : {};
            }

            //Set token data
            if (self.token_name) {
                data[self.token_name] = self.token_value;
            }

            button.on('click', function(event) {
                event.preventDefault();

                if (!button.hasClass('disabled')) {
                    self.setOptions(options);
                    self.fireEvent('execute', [action, data, novalidate]);
                }
            });

            button.data('event-added', true);
        });
    },
    execute: function(event, action, data, novalidate){
        var method = '_action' + action[0].toUpperCase() + action.substr(1);

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
        var buttons;

        if (this.buttons) {
            this.fireEvent('before.validate');

            buttons = this.buttons.filter('[data-novalidate!="novalidate"]');

            if (this.fireEvent('validate')) {
                buttons.removeClass('disabled');
            } else {
                buttons.addClass('disabled');
            }

            this.fireEvent('after.validate');
        }
    }
});

/**
 * Controller class specialized for grids, extends Koowa.Controller
 *
 * @package     Koowa_Media
 * @subpackage  Javascript
 */
Koowa.Controller.Grid = new Class({

    Extends: Koowa.Controller,
    
    options: {
        inputs: '.-koowa-grid-checkbox, .-koowa-grid-checkall'
    },
    initialize: function(options){
        var thead,
            self = this;
        
        this.parent(options);

        this.grid = new Koowa.Grid(this.form);

        this.addEvent('validate', this.validate);

        if (this.options.inputs && this.buttons) {
            this.checkValidity();
            this.form.find(this.options.inputs).on('change', function(event, ignore){
                if (!ignore) {
                    this.checkValidity();
                }
            }.bind(this));
        }

        this.token_name = this.form.data('token-name');
        this.token_value = this.form.data('token-value');

        this.setTableHeaders();
        this.setTableRows();

        //<select> elements in headers and footers are for filters, so they need to submit the form on change
        this.form.find('thead select, tfoot select').on('change', function(){
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
                element.on('click', function(event){
                    //Don't do anything if the event target is the same as the element
                    if($(event.target)[0] != element[0]) {
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
                element.on('click', function(event){
                    //Don't do anything if the event target is the same as the element
                    if($(event.target)[0] != element[0]) {
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
            tr.on('click', function(event){
                if($(event.target).is('[type=checkbox]')) {
                    return;
                }

                checkbox.prop('checked', !checkbox.prop('checked')).trigger('change');
            });

            // Checkbox should add selected and selected-multiple classes to the row
            checkbox.on('change', function(){
                var selected,
                    parent = tr.parent();

                $(this).prop('checked') ? tr.addClass('selected') : tr.removeClass('selected');

                selected = tr.hasClass('selected') + tr.siblings('.selected').length;

                if(selected > 1) {
                    parent.addClass('selected-multiple').removeClass('selected-single')
                } else {
                    parent.removeClass('selected-multiple').addClass('selected-single');
                }
            }.bind(checkbox)).trigger('change', true);

            // Set up buttons such as publish/unpublish triggers
            tr.find('[data-action]').each(function() {
                var action = $(this),
                    data = action.data('data'),
                    options = action.data(),
                    actionName = action.data('action'),
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

                action.on(eventType, function(){
                    checkboxes.prop('checked', '');
                    checkbox.prop('checked', 'checked');
                    checkboxes.trigger('change', true);

                    self.setOptions(options);
                    self.fireEvent('execute', [actionName, data, true]);
                });
            });
        });
    },
    validate: function(){
        return Koowa.Grid.getIdQuery() || false;
    },

    _action_default: function(action, data, novalidate){
        var idQuery, append, options;

        if(!novalidate && !this.fireEvent('validate')) {
            return false;
        }

        idQuery = Koowa.Grid.getIdQuery();
        append = this.options.url.match(/\?/) ? '&' : '?'; // mootools?
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
Koowa.Controller.Form = new Class({

    Extends: Koowa.Controller,

    _action_default: function(action, data, novalidate){
        if(!novalidate && !this.fireEvent('validate')) {
            return false;
        }

        this.form.append($('<input/>', {name: 'action', type: 'hidden', value: action}));
        this.form.submit();
    }

});

})(jQuery);