/**
 * Joomlatools Framework - https://www.joomlatools.com/developer/framework/
 *
 * @copyright	Copyright (C) 2007 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		https://github.com/joomlatools/joomlatools-framework for the canonical source repository
 */

if(!Kodekit) {
    /** @namespace */
    var Kodekit = {};
}

(function($) {
    var debounce = function(func, wait, immediate) {
        var timeout;
        return function() {
            var context = this, args = arguments;
            var later = function() {
                timeout = null;
                if (!immediate) func.apply(context, args);
            };
            var callNow = immediate && !timeout;
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
            if (callNow) func.apply(context, args);
        };
    };

    Kodekit.EntityStore = {};

    Kodekit.EntityStore.createFormBinding = function(store, property, element) {
        store.watch(function(state) {
            return state.entity[property];
        }, function(newVal, oldVal) {

            if (newVal != oldVal) {
                var $element = $(element);

                var $field = $element.is(':input') ? $element : $element.find(':input[name="'+property+'"]');

                if ($field.length && $field.val() !== newVal) {
                    $field.val(newVal).trigger('change');
                }
            }
        });
    };

    Kodekit.EntityStore.create = function(options) {
        var store = new Vuex.Store({
            state: {
                entity: {}
            },
            mutations: {
                setEntity: function(state, entity) {
                    state.entity = entity;
                },
                setProperty: function(state, properties) {
                    $.each(properties, function(key, value) {
                        Vue.set(state.entity, key, value);
                    });
                }
            }
        });

        if (options && options.entity) {
            store.commit('setEntity', options.entity);
        }

        if (options && options.form) {
            var form = $(options.form);

            form.on('input change', ':input', debounce(function(event) {
                var $target = $(event.target),
                    name = $target.attr('name');

                if (typeof name !== 'undefined' && typeof store.state.entity[name] !== 'undefined') {
                    var obj = {};
                    obj[name] = $target.val();
                    store.commit('setProperty', obj);
                }
            }, 300));

            if (options.bindings) {
                $.each(options.bindings, function(i, property) {
                    Kodekit.EntityStore.createFormBinding(store, property, form);
                });
            }
        }

        return store;
    }
})(kQuery);
