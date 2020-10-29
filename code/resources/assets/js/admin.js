//kquery.set.js
var globalCacheForjQueryReplacement = window.jQuery;
window.jQuery = window.kQuery;


//footable-processed.js
(function($) { var jQuery = $;﻿/*!
 * FooTable - Awesome Responsive Tables
 * Version : 2.0.3
 * http://fooplugins.com/plugins/footable-jquery/
 *
 * Requires jQuery - http://jquery.com/
 *
 * Copyright 2014 Steven Usher & Brad Vincent
 * Released under the MIT license
 * You are free to use FooTable in commercial projects as long as this copyright header is left intact.
 *
 * Date: 11 Nov 2014
 */
(function ($, w, undefined) {
    w.footable = {
        options: {
            delay: 100, // The number of millseconds to wait before triggering the react event
            breakpoints: { // The different screen resolution breakpoints
                phone: 480,
                tablet: 1024
            },
            parsers: {  // The default parser to parse the value out of a cell (values are used in building up row detail)
                alpha: function (cell) {
                    return $(cell).data('value') || $.trim($(cell).text());
                },
                numeric: function (cell) {
                    var val = $(cell).data('value') || $(cell).text().replace(/[^0-9.\-]/g, '');
                    val = parseFloat(val);
                    if (isNaN(val)) val = 0;
                    return val;
                }
            },
            addRowToggle: true,
            calculateWidthOverride: null,
            toggleSelector: ' > tbody > tr:not(.footable-row-detail)', //the selector to show/hide the detail row
            columnDataSelector: '> thead > tr:last-child > th, > thead > tr:last-child > td', //the selector used to find the column data in the thead
            detailSeparator: ':', //the separator character used when building up the detail row
            toggleHTMLElement: '<span />', // override this if you want to insert a click target rather than use a background image.
            createGroupedDetail: function (data) {
                var groups = { '_none': { 'name': null, 'data': [] } };
                for (var i = 0; i < data.length; i++) {
                    var groupid = data[i].group;
                    if (groupid !== null) {
                        if (!(groupid in groups))
                            groups[groupid] = { 'name': data[i].groupName || data[i].group, 'data': [] };

                        groups[groupid].data.push(data[i]);
                    } else {
                        groups._none.data.push(data[i]);
                    }
                }
                return groups;
            },
            createDetail: function (element, data, createGroupedDetail, separatorChar, classes) {
                /// <summary>This function is used by FooTable to generate the detail view seen when expanding a collapsed row.</summary>
                /// <param name="element">This is the div that contains all the detail row information, anything could be added to it.</param>
                /// <param name="data">
                ///  This is an array of objects containing the cell information for the current row.
                ///  These objects look like the below:
                ///    obj = {
                ///      'name': String, // The name of the column
                ///      'value': Object, // The value parsed from the cell using the parsers. This could be a string, a number or whatever the parser outputs.
                ///      'display': String, // This is the actual HTML from the cell, so if you have images etc you want moved this is the one to use and is the default value used.
                ///      'group': String, // This is the identifier used in the data-group attribute of the column.
                ///      'groupName': String // This is the actual name of the group the column belongs to.
                ///    }
                /// </param>
                /// <param name="createGroupedDetail">The grouping function to group the data</param>
                /// <param name="separatorChar">The separator charactor used</param>
                /// <param name="classes">The array of class names used to build up the detail row</param>

                var groups = createGroupedDetail(data);
                for (var group in groups) {
                    if (groups[group].data.length === 0) continue;
                    if (group !== '_none') element.append('<div class="' + classes.detailInnerGroup + '">' + groups[group].name + '</div>');

                    for (var j = 0; j < groups[group].data.length; j++) {
                        var separator = (groups[group].data[j].name) ? separatorChar : '';
                        element.append($('<div></div>').addClass(classes.detailInnerRow).append($('<div></div>').addClass(classes.detailInnerName)
                                .append(groups[group].data[j].name + separator)).append($('<div></div>').addClass(classes.detailInnerValue)
                                .attr('data-bind-value', groups[group].data[j].bindName).append(groups[group].data[j].display)));
                    }
                }
            },
            classes: {
                main: 'footable',
                loading: 'footable-loading',
                loaded: 'footable-loaded',
                toggle: 'footable-toggle',
                disabled: 'footable-disabled',
                detail: 'footable-row-detail',
                detailCell: 'footable-row-detail-cell',
                detailInner: 'footable-row-detail-inner',
                detailInnerRow: 'footable-row-detail-row',
                detailInnerGroup: 'footable-row-detail-group',
                detailInnerName: 'footable-row-detail-name',
                detailInnerValue: 'footable-row-detail-value',
                detailShow: 'footable-detail-show'
            },
            triggers: {
                initialize: 'footable_initialize',                      //trigger this event to force FooTable to reinitialize
                resize: 'footable_resize',                              //trigger this event to force FooTable to resize
                redraw: 'footable_redraw',                              //trigger this event to force FooTable to redraw
                toggleRow: 'footable_toggle_row',                       //trigger this event to force FooTable to toggle a row
                expandFirstRow: 'footable_expand_first_row',            //trigger this event to force FooTable to expand the first row
                expandAll: 'footable_expand_all',                       //trigger this event to force FooTable to expand all rows
                collapseAll: 'footable_collapse_all'                    //trigger this event to force FooTable to collapse all rows
            },
            events: {
                alreadyInitialized: 'footable_already_initialized',     //fires when the FooTable has already been initialized
                initializing: 'footable_initializing',                  //fires before FooTable starts initializing
                initialized: 'footable_initialized',                    //fires after FooTable has finished initializing
                resizing: 'footable_resizing',                          //fires before FooTable resizes
                resized: 'footable_resized',                            //fires after FooTable has resized
                redrawn: 'footable_redrawn',                            //fires after FooTable has redrawn
                breakpoint: 'footable_breakpoint',                      //fires inside the resize function, when a breakpoint is hit
                columnData: 'footable_column_data',                     //fires when setting up column data. Plugins should use this event to capture their own info about a column
                rowDetailUpdating: 'footable_row_detail_updating',      //fires before a detail row is updated
                rowDetailUpdated: 'footable_row_detail_updated',        //fires when a detail row is being updated
                rowCollapsed: 'footable_row_collapsed',                 //fires when a row is collapsed
                rowExpanded: 'footable_row_expanded',                   //fires when a row is expanded
                rowRemoved: 'footable_row_removed',                     //fires when a row is removed
                reset: 'footable_reset'                                 //fires when FooTable is reset
            },
            debug: false, // Whether or not to log information to the console.
            log: null
        },

        version: {
            major: 0, minor: 5,
            toString: function () {
                return w.footable.version.major + '.' + w.footable.version.minor;
            },
            parse: function (str) {
                var version = /(\d+)\.?(\d+)?\.?(\d+)?/.exec(str);
                return {
                    major: parseInt(version[1], 10) || 0,
                    minor: parseInt(version[2], 10) || 0,
                    patch: parseInt(version[3], 10) || 0
                };
            }
        },

        plugins: {
            _validate: function (plugin) {
                ///<summary>Simple validation of the <paramref name="plugin"/> to make sure any members called by FooTable actually exist.</summary>
                ///<param name="plugin">The object defining the plugin, this should implement a string property called "name" and a function called "init".</param>

                if (!$.isFunction(plugin)) {
                  if (w.footable.options.debug === true) console.error('Validation failed, expected type "function", received type "{0}".', typeof plugin);
                  return false;
                }
                var p = new plugin();
                if (typeof p['name'] !== 'string') {
                    if (w.footable.options.debug === true) console.error('Validation failed, plugin does not implement a string property called "name".', p);
                    return false;
                }
                if (!$.isFunction(p['init'])) {
                    if (w.footable.options.debug === true) console.error('Validation failed, plugin "' + p['name'] + '" does not implement a function called "init".', p);
                    return false;
                }
                if (w.footable.options.debug === true) console.log('Validation succeeded for plugin "' + p['name'] + '".', p);
                return true;
            },
            registered: [], // An array containing all registered plugins.
            register: function (plugin, options) {
                ///<summary>Registers a <paramref name="plugin"/> and its default <paramref name="options"/> with FooTable.</summary>
                ///<param name="plugin">The plugin that should implement a string property called "name" and a function called "init".</param>
                ///<param name="options">The default options to merge with the FooTable's base options.</param>

                if (w.footable.plugins._validate(plugin)) {
                    w.footable.plugins.registered.push(plugin);
                    if (typeof options === 'object') $.extend(true, w.footable.options, options);
                }
            },
            load: function(instance){
              var loaded = [], registered, i;
              for(i = 0; i < w.footable.plugins.registered.length; i++){
                try {
                  registered = w.footable.plugins.registered[i];
                  loaded.push(new registered(instance));
                } catch (err) {
                  if (w.footable.options.debug === true) console.error(err);
                }
              }
              return loaded;
            },
            init: function (instance) {
                ///<summary>Loops through all registered plugins and calls the "init" method supplying the current <paramref name="instance"/> of the FooTable as the first parameter.</summary>
                ///<param name="instance">The current instance of the FooTable that the plugin is being initialized for.</param>

                for (var i = 0; i < instance.plugins.length; i++) {
                    try {
                      instance.plugins[i]['init'](instance);
                    } catch (err) {
                        if (w.footable.options.debug === true) console.error(err);
                    }
                }
            }
        }
    };

    var instanceCount = 0;

    $.fn.footable = function (options) {
        ///<summary>The main constructor call to initialize the plugin using the supplied <paramref name="options"/>.</summary>
        ///<param name="options">
        ///<para>A JSON object containing user defined options for the plugin to use. Any options not supplied will have a default value assigned.</para>
        ///<para>Check the documentation or the default options object above for more information on available options.</para>
        ///</param>

        options = options || {};
        var o = $.extend(true, {}, w.footable.options, options); //merge user and default options
        return this.each(function () {
            instanceCount++;
            var footable = new Footable(this, o, instanceCount);
            $(this).data('footable', footable);
        });
    };

    //helper for using timeouts
    function Timer() {
        ///<summary>Simple timer object created around a timeout.</summary>
        var t = this;
        t.id = null;
        t.busy = false;
        t.start = function (code, milliseconds) {
            ///<summary>Starts the timer and waits the specified amount of <paramref name="milliseconds"/> before executing the supplied <paramref name="code"/>.</summary>
            ///<param name="code">The code to execute once the timer runs out.</param>
            ///<param name="milliseconds">The time in milliseconds to wait before executing the supplied <paramref name="code"/>.</param>

            if (t.busy) {
                return;
            }
            t.stop();
            t.id = setTimeout(function () {
                code();
                t.id = null;
                t.busy = false;
            }, milliseconds);
            t.busy = true;
        };
        t.stop = function () {
            ///<summary>Stops the timer if its runnning and resets it back to its starting state.</summary>

            if (t.id !== null) {
                clearTimeout(t.id);
                t.id = null;
                t.busy = false;
            }
        };
    }

    function Footable(t, o, id) {
        ///<summary>Inits a new instance of the plugin.</summary>
        ///<param name="t">The main table element to apply this plugin to.</param>
        ///<param name="o">The options supplied to the plugin. Check the defaults object to see all available options.</param>
        ///<param name="id">The id to assign to this instance of the plugin.</param>

        var ft = this;
        ft.id = id;
        ft.table = t;
        ft.options = o;
        ft.breakpoints = [];
        ft.breakpointNames = '';
        ft.columns = {};
        ft.plugins = w.footable.plugins.load(ft);

        var opt = ft.options,
            cls = opt.classes,
            evt = opt.events,
            trg = opt.triggers,
            indexOffset = 0;

        // This object simply houses all the timers used in the FooTable.
        ft.timers = {
            resize: new Timer(),
            register: function (name) {
                ft.timers[name] = new Timer();
                return ft.timers[name];
            }
        };

        ft.init = function () {
            var $window = $(w), $table = $(ft.table);

            w.footable.plugins.init(ft);

            if ($table.hasClass(cls.loaded)) {
                //already loaded FooTable for the table, so don't init again
                ft.raise(evt.alreadyInitialized);
                return;
            }

            //raise the initializing event
            ft.raise(evt.initializing);

            $table.addClass(cls.loading);

            // Get the column data once for the life time of the plugin
            $table.find(opt.columnDataSelector).each(function () {
                var data = ft.getColumnData(this);
                ft.columns[data.index] = data;
            });

            // Create a nice friendly array to work with out of the breakpoints object.
            for (var name in opt.breakpoints) {
                ft.breakpoints.push({ 'name': name, 'width': opt.breakpoints[name] });
                ft.breakpointNames += (name + ' ');
            }

            // Sort the breakpoints so the smallest is checked first
            ft.breakpoints.sort(function (a, b) {
                return a['width'] - b['width'];
            });

            $table
                .unbind(trg.initialize)
                //bind to FooTable initialize trigger
                .bind(trg.initialize, function () {
                    //remove previous "state" (to "force" a resize)
                    $table.removeData('footable_info');
                    $table.data('breakpoint', '');

                    //trigger the FooTable resize
                    $table.trigger(trg.resize);

                    //remove the loading class
                    $table.removeClass(cls.loading);

                    //add the FooTable and loaded class
                    $table.addClass(cls.loaded).addClass(cls.main);

                    //raise the initialized event
                    ft.raise(evt.initialized);
                })
                .unbind(trg.redraw)
                //bind to FooTable redraw trigger
                .bind(trg.redraw, function () {
                    ft.redraw();
                })
                .unbind(trg.resize)
                //bind to FooTable resize trigger
                .bind(trg.resize, function () {
                    ft.resize();
                })
                .unbind(trg.expandFirstRow)
                //bind to FooTable expandFirstRow trigger
                .bind(trg.expandFirstRow, function () {
                    $table.find(opt.toggleSelector).first().not('.' + cls.detailShow).trigger(trg.toggleRow);
                })
                .unbind(trg.expandAll)
                //bind to FooTable expandFirstRow trigger
                .bind(trg.expandAll, function () {
                    $table.find(opt.toggleSelector).not('.' + cls.detailShow).trigger(trg.toggleRow);
                })
                .unbind(trg.collapseAll)
                //bind to FooTable expandFirstRow trigger
                .bind(trg.collapseAll, function () {
                    $table.find('.' + cls.detailShow).trigger(trg.toggleRow);
                });

            //trigger a FooTable initialize
            $table.trigger(trg.initialize);

            //bind to window resize
            $window
                .bind('resize.footable', function () {
                    ft.timers.resize.stop();
                    ft.timers.resize.start(function () {
                        ft.raise(trg.resize);
                    }, opt.delay);
                });
        };

        ft.addRowToggle = function () {
            if (!opt.addRowToggle) return;

            var $table = $(ft.table),
                hasToggleColumn = false;

            //first remove all toggle spans
            $table.find('span.' + cls.toggle).remove();

            for (var c in ft.columns) {
                var col = ft.columns[c];
                if (col.toggle) {
                    hasToggleColumn = true;
                    var selector = '> tbody > tr:not(.' + cls.detail + ',.' + cls.disabled + ') > td:nth-child(' + (parseInt(col.index, 10) + 1) + '),' +
                                            '> tbody > tr:not(.' + cls.detail + ',.' + cls.disabled + ') > th:nth-child(' + (parseInt(col.index, 10) + 1) + ')';
                    $table.find(selector).not('.' + cls.detailCell).prepend($(opt.toggleHTMLElement).addClass(cls.toggle));
                    return;
                }
            }
            //check if we have an toggle column. If not then add it to the first column just to be safe
            if (!hasToggleColumn) {
                $table
                    .find('> tbody > tr:not(.' + cls.detail + ',.' + cls.disabled + ') > td:first-child')
                                        .add('> tbody > tr:not(.' + cls.detail + ',.' + cls.disabled + ') > th:first-child')
                    .not('.' + cls.detailCell)
                    .prepend($(opt.toggleHTMLElement).addClass(cls.toggle));
            }
        };

        ft.setColumnClasses = function () {
            var $table = $(ft.table);
            for (var c in ft.columns) {
                var col = ft.columns[c];
                if (col.className !== null) {
                    var selector = '', first = true;
                    $.each(col.matches, function (m, match) { //support for colspans
                        if (!first) selector += ', ';
                        selector += '> tbody > tr:not(.' + cls.detail + ') > td:nth-child(' + (parseInt(match, 10) + 1) + ')';
                        first = false;
                    });
                    //add the className to the cells specified by data-class="blah"
                    $table.find(selector).not('.' + cls.detailCell).addClass(col.className);
                }
            }
        };

        //moved this out into it's own function so that it can be called from other add-ons
        ft.bindToggleSelectors = function () {
            var $table = $(ft.table);

            if (!ft.hasAnyBreakpointColumn()) return;

            $table.find(opt.toggleSelector).unbind(trg.toggleRow).bind(trg.toggleRow, function (e) {
                var $row = $(this).is('tr') ? $(this) : $(this).parents('tr:first');
                ft.toggleDetail($row);
            });

            $table.find(opt.toggleSelector).unbind('click.footable').bind('click.footable', function (e) {
                if ($table.is('.breakpoint') && $(e.target).is('td,th,.'+ cls.toggle)) {
                    $(this).trigger(trg.toggleRow);
                }
            });
        };

        ft.parse = function (cell, column) {
            var parser = opt.parsers[column.type] || opt.parsers.alpha;
            return parser(cell);
        };

        ft.getColumnData = function (th) {
            var $th = $(th), hide = $th.data('hide'), index = $th.index();
            hide = hide || '';
            hide = jQuery.map(hide.split(','), function (a) {
                return jQuery.trim(a);
            });
            var data = {
                'index': index,
                'hide': { },
                'type': $th.data('type') || 'alpha',
                'name': $th.data('name') || $.trim($th.text()),
                'ignore': $th.data('ignore') || false,
                'toggle': $th.data('toggle') || false,
                'className': $th.data('class') || null,
                'matches': [],
                'names': { },
                'group': $th.data('group') || null,
                'groupName': null,
                'isEditable': $th.data('editable')
            };

            if (data.group !== null) {
                var $group = $(ft.table).find('> thead > tr.footable-group-row > th[data-group="' + data.group + '"], > thead > tr.footable-group-row > td[data-group="' + data.group + '"]').first();
                data.groupName = ft.parse($group, { 'type': 'alpha' });
            }

            var pcolspan = parseInt($th.prev().attr('colspan') || 0, 10);
            indexOffset += pcolspan > 1 ? pcolspan - 1 : 0;
            var colspan = parseInt($th.attr('colspan') || 0, 10), curindex = data.index + indexOffset;
            if (colspan > 1) {
                var names = $th.data('names');
                names = names || '';
                names = names.split(',');
                for (var i = 0; i < colspan; i++) {
                    data.matches.push(i + curindex);
                    if (i < names.length) data.names[i + curindex] = names[i];
                }
            } else {
                data.matches.push(curindex);
            }

            data.hide['default'] = ($th.data('hide') === "all") || ($.inArray('default', hide) >= 0);

            var hasBreakpoint = false;
            for (var name in opt.breakpoints) {
                data.hide[name] = ($th.data('hide') === "all") || ($.inArray(name, hide) >= 0);
                hasBreakpoint = hasBreakpoint || data.hide[name];
            }
            data.hasBreakpoint = hasBreakpoint;
            var e = ft.raise(evt.columnData, { 'column': { 'data': data, 'th': th } });
            return e.column.data;
        };

        ft.getViewportWidth = function () {
            return window.innerWidth || (document.body ? document.body.offsetWidth : 0);
        };

        ft.calculateWidth = function ($table, info) {
            if (jQuery.isFunction(opt.calculateWidthOverride)) {
                return opt.calculateWidthOverride($table, info);
            }
            if (info.viewportWidth < info.width) info.width = info.viewportWidth;
            if (info.parentWidth < info.width) info.width = info.parentWidth;
            return info;
        };

        ft.hasBreakpointColumn = function (breakpoint) {
            for (var c in ft.columns) {
                if (ft.columns[c].hide[breakpoint]) {
                    if (ft.columns[c].ignore) {
                        continue;
                    }
                    return true;
                }
            }
            return false;
        };

        ft.hasAnyBreakpointColumn = function () {
            for (var c in ft.columns) {
                if (ft.columns[c].hasBreakpoint) {
                    return true;
                }
            }
            return false;
        };

        ft.resize = function () {
            var $table = $(ft.table);

            if (!$table.is(':visible')) {
                return;
            } //we only care about FooTables that are visible

            if (!ft.hasAnyBreakpointColumn()) {
				$table.trigger(trg.redraw);
				return;
            } //we only care about FooTables that have breakpoints

            var info = {
                'width': $table.width(),                  //the table width
                'viewportWidth': ft.getViewportWidth(),   //the width of the viewport
                'parentWidth': $table.parent().width()    //the width of the parent
            };

            info = ft.calculateWidth($table, info);

            var pinfo = $table.data('footable_info');
            $table.data('footable_info', info);
            ft.raise(evt.resizing, { 'old': pinfo, 'info': info });

            // This (if) statement is here purely to make sure events aren't raised twice as mobile safari seems to do
            if (!pinfo || (pinfo && pinfo.width && pinfo.width !== info.width)) {

                var current = null, breakpoint;
                for (var i = 0; i < ft.breakpoints.length; i++) {
                    breakpoint = ft.breakpoints[i];
                    if (breakpoint && breakpoint.width && info.width <= breakpoint.width) {
                        current = breakpoint;
                        break;
                    }
                }

                var breakpointName = (current === null ? 'default' : current['name']),
                    hasBreakpointFired = ft.hasBreakpointColumn(breakpointName),
                    previousBreakpoint = $table.data('breakpoint');

                $table
                    .data('breakpoint', breakpointName)
                    .removeClass('default breakpoint').removeClass(ft.breakpointNames)
                    .addClass(breakpointName + (hasBreakpointFired ? ' breakpoint' : ''));

                //only do something if the breakpoint has changed
                if (breakpointName !== previousBreakpoint) {
                    //trigger a redraw
                    $table.trigger(trg.redraw);
                    //raise a breakpoint event
                    ft.raise(evt.breakpoint, { 'breakpoint': breakpointName, 'info': info });
                }
            }

            ft.raise(evt.resized, { 'old': pinfo, 'info': info });
        };

        ft.redraw = function () {
            //add the toggler to each row
            ft.addRowToggle();

            //bind the toggle selector click events
            ft.bindToggleSelectors();

            //set any cell classes defined for the columns
            ft.setColumnClasses();

            var $table = $(ft.table),
                breakpointName = $table.data('breakpoint'),
                hasBreakpointFired = ft.hasBreakpointColumn(breakpointName);

            $table
                .find('> tbody > tr:not(.' + cls.detail + ')').data('detail_created', false).end()
                .find('> thead > tr:last-child > th')
                .each(function () {
                    var data = ft.columns[$(this).index()], selector = '', first = true;
                    $.each(data.matches, function (m, match) {
                        if (!first) {
                            selector += ', ';
                        }
                        var count = match + 1;
                        selector += '> tbody > tr:not(.' + cls.detail + ') > td:nth-child(' + count + ')';
                        selector += ', > tfoot > tr:not(.' + cls.detail + ') > td:nth-child(' + count + ')';
                        selector += ', > colgroup > col:nth-child(' + count + ')';
                        first = false;
                    });

                    selector += ', > thead > tr[data-group-row="true"] > th[data-group="' + data.group + '"]';
                    var $column = $table.find(selector).add(this);
                    if (breakpointName !== '') {
                      if (data.hide[breakpointName] === false) $column.addClass('footable-visible').show();
                      else $column.removeClass('footable-visible').hide();
                    }

                    if ($table.find('> thead > tr.footable-group-row').length === 1) {
                        var $groupcols = $table.find('> thead > tr:last-child > th[data-group="' + data.group + '"]:visible, > thead > tr:last-child > th[data-group="' + data.group + '"]:visible'),
                            $group = $table.find('> thead > tr.footable-group-row > th[data-group="' + data.group + '"], > thead > tr.footable-group-row > td[data-group="' + data.group + '"]'),
                            groupspan = 0;

                        $.each($groupcols, function () {
                            groupspan += parseInt($(this).attr('colspan') || 1, 10);
                        });

                        if (groupspan > 0) $group.attr('colspan', groupspan).show();
                        else $group.hide();
                    }
                })
                .end()
                .find('> tbody > tr.' + cls.detailShow).each(function () {
                    ft.createOrUpdateDetailRow(this);
                });

            $table.find("[data-bind-name]").each(function () {
                ft.toggleInput(this);
            });

            $table.find('> tbody > tr.' + cls.detailShow + ':visible').each(function () {
                var $next = $(this).next();
                if ($next.hasClass(cls.detail)) {
                    if (!hasBreakpointFired) $next.hide();
                    else $next.show();
                }
            });

            // adding .footable-first-column and .footable-last-column to the first and last th and td of each row in order to allow
            // for styling if the first or last column is hidden (which won't work using :first-child or :last-child)
            $table.find('> thead > tr > th.footable-last-column, > tbody > tr > td.footable-last-column').removeClass('footable-last-column');
            $table.find('> thead > tr > th.footable-first-column, > tbody > tr > td.footable-first-column').removeClass('footable-first-column');
            $table.find('> thead > tr, > tbody > tr')
                .find('> th.footable-visible:last, > td.footable-visible:last')
                .addClass('footable-last-column')
                .end()
                .find('> th.footable-visible:first, > td.footable-visible:first')
                .addClass('footable-first-column');

            ft.raise(evt.redrawn);
        };

        ft.toggleDetail = function (row) {
            var $row = (row.jquery) ? row : $(row),
                $next = $row.next();

            //check if the row is already expanded
            if ($row.hasClass(cls.detailShow)) {
                $row.removeClass(cls.detailShow);

                //only hide the next row if it's a detail row
                if ($next.hasClass(cls.detail)) $next.hide();

                ft.raise(evt.rowCollapsed, { 'row': $row[0] });

            } else {
                ft.createOrUpdateDetailRow($row[0]);
                $row.addClass(cls.detailShow)
                    .next().show();

                ft.raise(evt.rowExpanded, { 'row': $row[0] });
            }
        };

        ft.removeRow = function (row) {
            var $row = (row.jquery) ? row : $(row);
            if ($row.hasClass(cls.detail)) {
                $row = $row.prev();
            }
            var $next = $row.next();
            if ($row.data('detail_created') === true) {
                //remove the detail row
                $next.remove();
            }
            $row.remove();

            //raise event
            ft.raise(evt.rowRemoved);
        };

        ft.appendRow = function (row) {
            var $row = (row.jquery) ? row : $(row);
            $(ft.table).find('tbody').append($row);

            //redraw the table
            ft.redraw();
        };

        ft.getColumnFromTdIndex = function (index) {
            /// <summary>Returns the correct column data for the supplied index taking into account colspans.</summary>
            /// <param name="index">The index to retrieve the column data for.</param>
            /// <returns type="json">A JSON object containing the column data for the supplied index.</returns>
            var result = null;
            for (var column in ft.columns) {
                if ($.inArray(index, ft.columns[column].matches) >= 0) {
                    result = ft.columns[column];
                    break;
                }
            }
            return result;
        };

        ft.createOrUpdateDetailRow = function (actualRow) {
            var $row = $(actualRow), $next = $row.next(), $detail, values = [];
            if ($row.data('detail_created') === true) return true;

            if ($row.is(':hidden')) return false; //if the row is hidden for some reason (perhaps filtered) then get out of here
            ft.raise(evt.rowDetailUpdating, { 'row': $row, 'detail': $next });
            $row.find('> td:hidden').each(function () {
                var index = $(this).index(), column = ft.getColumnFromTdIndex(index), name = column.name;
                if (column.ignore === true) return true;

                if (index in column.names) name = column.names[index];

                var bindName = $(this).attr("data-bind-name");
                if (bindName != null && $(this).is(':empty')) {
                    var bindValue = $('.' + cls.detailInnerValue + '[' + 'data-bind-value="' + bindName + '"]');
                    $(this).html($(bindValue).contents().detach());
                }
                var display;
                if (column.isEditable !== false && (column.isEditable || $(this).find(":input").length > 0)) {
                    if(bindName == null) {
                        bindName = "bind-" + $.now() + "-" + index;
                        $(this).attr("data-bind-name", bindName);
                    }
                    display = $(this).contents().detach();
                }
                if (!display) display = $(this).contents().clone(true, true);
                values.push({ 'name': name, 'value': ft.parse(this, column), 'display': display, 'group': column.group, 'groupName': column.groupName, 'bindName': bindName });
                return true;
            });
            if (values.length === 0) return false; //return if we don't have any data to show
            var colspan = $row.find('> td:visible').length;
            var exists = $next.hasClass(cls.detail);
            if (!exists) { // Create
                $next = $('<tr class="' + cls.detail + '"><td class="' + cls.detailCell + '"><div class="' + cls.detailInner + '"></div></td></tr>');
                $row.after($next);
            }
            $next.find('> td:first').attr('colspan', colspan);
            $detail = $next.find('.' + cls.detailInner).empty();
            opt.createDetail($detail, values, opt.createGroupedDetail, opt.detailSeparator, cls);
            $row.data('detail_created', true);
            ft.raise(evt.rowDetailUpdated, { 'row': $row, 'detail': $next });
            return !exists;
        };

        ft.raise = function (eventName, args) {

            if (ft.options.debug === true && $.isFunction(ft.options.log)) ft.options.log(eventName, 'event');

            args = args || { };
            var def = { 'ft': ft };
            $.extend(true, def, args);
            var e = $.Event(eventName, def);
            if (!e.ft) {
                $.extend(true, e, def);
            } //pre jQuery 1.6 which did not allow data to be passed to event object constructor
            $(ft.table).trigger(e);
            return e;
        };

        //reset the state of FooTable
        ft.reset = function() {
            var $table = $(ft.table);
            $table.removeData('footable_info')
                .data('breakpoint', '')
                .removeClass(cls.loading)
                .removeClass(cls.loaded);

            $table.find(opt.toggleSelector).unbind(trg.toggleRow).unbind('click.footable');

            $table.find('> tbody > tr').removeClass(cls.detailShow);

            $table.find('> tbody > tr.' + cls.detail).remove();

            ft.raise(evt.reset);
        };

        //Switch between row-detail and detail-show.
        ft.toggleInput = function (column) {
            var bindName = $(column).attr("data-bind-name");
            if(bindName != null) {
                var bindValue = $('.' + cls.detailInnerValue + '[' + 'data-bind-value="' + bindName + '"]');
                if(bindValue != null) {
                    if($(column).is(":visible")) {
                        if(!$(bindValue).is(':empty')) $(column).html($(bindValue).contents().detach());
                    } else if(!$(column).is(':empty')) {
                        $(bindValue).html($(column).contents().detach());
                    }
                }
            }
        };

        ft.init();
        return ft;
    }
})(jQuery, window);
})(jQuery);


//bootstrap-dropdown.js
/* ============================================================
 * bootstrap-dropdown.js v2.3.2
 * http://getbootstrap.com/2.3.2/javascript.html#dropdowns
 * ============================================================
 * Copyright 2013 Twitter, Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 * ============================================================ */


!function ($) {

  "use strict"; // jshint ;_;


 /* DROPDOWN CLASS DEFINITION
  * ========================= */

  var toggle = '[data-k-toggle=dropdown]'
    , Dropdown = function (element) {
        var $el = $(element).on('click.k-dropdown.data-api', this.toggle)
        $('html').on('click.k-dropdown.data-api', function () {
          $el.parent().removeClass('k-is-open')
        })
      }

  Dropdown.prototype = {

    constructor: Dropdown

  , toggle: function (e) {
      var $this = $(this)
        , $parent
        , isActive

      if ($this.is('.k-is-disabled, :disabled')) return

      $parent = getParent($this)

      isActive = $parent.hasClass('k-is-open')

      clearMenus()

      if (!isActive) {
        if ('ontouchstart' in document.documentElement) {
          // if mobile we we use a backdrop because click events don't delegate
          $('<div class="k-dropdown-backdrop"/>').insertBefore($(this)).on('click', clearMenus)
        }
        $parent.toggleClass('k-is-open')
      }

      $this.focus()

      return false
    }

  , keydown: function (e) {
      var $this
        , $items
        , $active
        , $parent
        , isActive
        , index

      if (!/(38|40|27)/.test(e.keyCode)) return

      $this = $(this)

      e.preventDefault()
      e.stopPropagation()

      if ($this.is('.k-is-disabled, :disabled')) return

      $parent = getParent($this)

      isActive = $parent.hasClass('k-is-open')

      if (!isActive || (isActive && e.keyCode == 27)) {
        if (e.which == 27) $parent.find(toggle).focus()
        return $this.click()
      }

      $items = $('[role=menu] li:not(.k-dropdown__divider):visible a', $parent)

      if (!$items.length) return

      index = $items.index($items.filter(':focus'))

      if (e.keyCode == 38 && index > 0) index--                                        // up
      if (e.keyCode == 40 && index < $items.length - 1) index++                        // down
      if (!~index) index = 0

      $items
        .eq(index)
        .focus()
    }

  }

  function clearMenus() {
    $('.k-dropdown-backdrop').remove()
    $(toggle).each(function () {
      getParent($(this)).removeClass('k-is-open')
    })
  }

  function getParent($this) {
    var selector = $this.attr('data-target')
      , $parent

    if (!selector) {
      selector = $this.attr('href')
      selector = selector && /#/.test(selector) && selector.replace(/.*(?=#[^\s]*$)/, '') //strip for ie7
    }

    $parent = selector && $(selector)

    if (!$parent || !$parent.length) $parent = $this.parent()

    return $parent
  }


  /* DROPDOWN PLUGIN DEFINITION
   * ========================== */

  var old = $.fn.kdropdown

  $.fn.kdropdown = function (option) {
    return this.each(function () {
      var $this = $(this)
        , data = $this.data('kdropdown')
      if (!data) $this.data('kdropdown', (data = new Dropdown(this)))
      if (typeof option == 'string') data[option].call($this)
    })
  }

  $.fn.kdropdown.Constructor = Dropdown


 /* DROPDOWN NO CONFLICT
  * ==================== */

  $.fn.kdropdown.noConflict = function () {
    $.fn.dropdown = old
    return this
  }


  /* APPLY TO STANDARD DROPDOWN ELEMENTS
   * =================================== */

  $(document)
    .on('click.k-dropdown.data-api', clearMenus)
    .on('click.k-dropdown.data-api', '.k-dropdown form', function (e) { e.stopPropagation() })
    .on('click.k-dropdown.data-api'  , toggle, Dropdown.prototype.toggle)
    .on('keydown.k-dropdown.data-api', toggle + ', [role=menu]' , Dropdown.prototype.keydown)

}(window.jQuery);



//bootstrap-tab.js
/* ========================================================
 * bootstrap-tab.js v2.3.2
 * http://getbootstrap.com/2.3.2/javascript.html#tabs
 * ========================================================
 * Copyright 2013 Twitter, Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 * ======================================================== */


!function ($) {

  "use strict"; // jshint ;_;


 /* TAB CLASS DEFINITION
  * ==================== */

  var Tab = function (element) {
    this.element = $(element)
  }

  Tab.prototype = {

    constructor: Tab

  , show: function () {
      var $this = this.element
        , $ul = $this.parent().parent('ul:not(.k-dropdown__menu)')
        , $tbody = $this.parent().parent().parent('tbody')
        , selector = $this.attr('data-target')
        , previous
        , $target
        , e

      if (!selector) {
        selector = $this.attr('href')
        selector = selector && selector.replace(/.*(?=#[^\s]*$)/, '') //strip for ie7
      }

      if ( $this.parent('li').hasClass('k-is-active') ) return
      if ( $this.parent().parent('tr').hasClass('k-is-active') ) return

      if ($this.parent('li')) {
          previous = $ul.find('.k-is-active:last a')[0]
      }

      if ($this.parent('td')) {
          previous = $tbody.find('.k-is-active:last a')[0]
      }

      e = $.Event('show', {
        relatedTarget: previous
      })

      $this.trigger(e)

      if (e.isDefaultPrevented()) return

      $target = $(selector)

      if ($this.parent('li')[0]) {
          this.activate($this.parent('li'), $ul)
      }
      if ($this.parent().parent('tr')[0]) {
          this.activate($this.parent().parent('tr'), $tbody)
      }

      this.activate($target, $target.parent(), function () {
        $this.trigger({
          type: 'shown'
        , relatedTarget: previous
        })
      })
    }

  , activate: function ( element, container, callback) {
      var $active = container.find('> .k-is-active')
        , transition = callback
            && $.support.transition
            && $active.hasClass('fade')

      function next() {
        $active
          .removeClass('k-is-active')
          .find('> .k-dropdown__menu > .k-is-active')
          .removeClass('k-is-active')

        element.addClass('k-is-active')

        if (transition) {
          element[0].offsetWidth // reflow for transition
          element.addClass('in')
        } else {
          element.removeClass('fade')
        }

        if ( element.parent('.k-dropdown__menu') ) {
          element.closest('li.k-dropdown').addClass('k-is-active')
        }

        callback && callback()
      }

      transition ?
        $active.one($.support.transition.end, next) :
        next()

      $active.removeClass('in')
    }
  }


 /* TAB PLUGIN DEFINITION
  * ===================== */

  var old = $.fn.tab

  $.fn.ktab = function ( option ) {
    return this.each(function () {
      var $this = $(this)
        , data = $this.data('ktab')
      if (!data) $this.data('ktab', (data = new Tab(this)))
      if (typeof option == 'string') data[option]()
    })
  }

  $.fn.ktab.Constructor = Tab


 /* TAB NO CONFLICT
  * =============== */

  $.fn.ktab.noConflict = function () {
    $.fn.ktab = old
    return this
  }


 /* TAB DATA-API
  * ============ */

  $(document).on('click.k-tab.data-api', '[data-k-toggle="tab"], [data-k-toggle="pill"]', function (e) {
    e.preventDefault()
    $(this).ktab('show')
  })

}(window.jQuery);


//bootstrap-tooltip.js
/* ===========================================================
 * bootstrap-tooltip.js v2.3.2
 * http://getbootstrap.com/2.3.2/javascript.html#tooltips
 * Inspired by the original jQuery.tipsy by Jason Frame
 * ===========================================================
 * Copyright 2013 Twitter, Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 * ========================================================== */


!function ($) {

  "use strict"; // jshint ;_;


 /* TOOLTIP PUBLIC CLASS DEFINITION
  * =============================== */

  var Tooltip = function (element, options) {
    this.init('ktooltip', element, options)
  }

  Tooltip.prototype = {

    constructor: Tooltip

  , init: function (type, element, options) {
      var eventIn
        , eventOut
        , triggers
        , trigger
        , i

      this.type = type
      this.$element = $(element)
      this.options = this.getOptions(options)
      this.enabled = true

      triggers = this.options.trigger.split(' ')

      for (i = triggers.length; i--;) {
        trigger = triggers[i]
        if (trigger == 'click') {
          this.$element.on('click.' + this.type, this.options.selector, $.proxy(this.toggle, this))
        } else if (trigger != 'manual') {
          eventIn = trigger == 'hover' ? 'mouseenter' : 'focus'
          eventOut = trigger == 'hover' ? 'mouseleave' : 'blur'
          this.$element.on(eventIn + '.' + this.type, this.options.selector, $.proxy(this.enter, this))
          this.$element.on(eventOut + '.' + this.type, this.options.selector, $.proxy(this.leave, this))
        }
      }

      this.options.selector ?
        (this._options = $.extend({}, this.options, { trigger: 'manual', selector: '' })) :
        this.fixTitle()
    }

  , getOptions: function (options) {
      options = $.extend({}, $.fn[this.type].defaults, this.$element.data(), options)

      if (options.delay && typeof options.delay == 'number') {
        options.delay = {
          show: options.delay
        , hide: options.delay
        }
      }

      return options
    }

  , enter: function (e) {
      var defaults = $.fn[this.type].defaults
        , options = {}
        , self

      this._options && $.each(this._options, function (key, value) {
        if (defaults[key] != value) options[key] = value
      }, this)

      self = $(e.currentTarget)[this.type](options).data(this.type)

      if (!self.options.delay || !self.options.delay.show) return self.show()

      clearTimeout(this.timeout)
      self.hoverState = 'in'
      this.timeout = setTimeout(function() {
        if (self.hoverState == 'in') self.show()
      }, self.options.delay.show)
    }

  , leave: function (e) {
      var self = $(e.currentTarget)[this.type](this._options).data(this.type)

      if (this.timeout) clearTimeout(this.timeout)
      if (!self.options.delay || !self.options.delay.hide) return self.hide()

      self.hoverState = 'out'
      this.timeout = setTimeout(function() {
        if (self.hoverState == 'out') self.hide()
      }, self.options.delay.hide)
    }

  , show: function () {
      var $tip
        , pos
        , actualWidth
        , actualHeight
        , placement
        , tp
        , e = $.Event('show')

      if (this.hasContent() && this.enabled) {
        this.$element.trigger(e)
        if (e.isDefaultPrevented()) return
        $tip = this.tip()
        this.setContent()

        if (this.options.animation) {
          $tip.addClass('fade')
        }

        placement = typeof this.options.placement == 'function' ?
          this.options.placement.call(this, $tip[0], this.$element[0]) :
          this.options.placement

        $tip
          .detach()
          .css({ top: 0, left: 0, display: 'block' })

        this.options.container ? $tip.appendTo(this.options.container) : $tip.insertAfter(this.$element)

        pos = this.getPosition()

        actualWidth = $tip[0].offsetWidth
        actualHeight = $tip[0].offsetHeight

        switch (placement) {
          case 'bottom':
            tp = {top: pos.top + pos.height, left: pos.left + pos.width / 2 - actualWidth / 2}
            break
          case 'top':
            tp = {top: pos.top - actualHeight, left: pos.left + pos.width / 2 - actualWidth / 2}
            break
          case 'left':
            tp = {top: pos.top + pos.height / 2 - actualHeight / 2, left: pos.left - actualWidth}
            break
          case 'right':
            tp = {top: pos.top + pos.height / 2 - actualHeight / 2, left: pos.left + pos.width}
            break
        }

        this.applyPlacement(tp, placement)
        this.$element.trigger('shown')
      }
    }

  , applyPlacement: function(offset, placement){
      var $tip = this.tip()
        , width = $tip[0].offsetWidth
        , height = $tip[0].offsetHeight
        , actualWidth
        , actualHeight
        , delta
        , replace

      $tip
        .offset(offset)
        .addClass(placement)
        .addClass('in')

      actualWidth = $tip[0].offsetWidth
      actualHeight = $tip[0].offsetHeight

      if (placement == 'top' && actualHeight != height) {
        offset.top = offset.top + height - actualHeight
        replace = true
      }

      if (placement == 'bottom' || placement == 'top') {
        delta = 0

        if (offset.left < 0){
          delta = offset.left * -2
          offset.left = 0
          $tip.offset(offset)
          actualWidth = $tip[0].offsetWidth
          actualHeight = $tip[0].offsetHeight
        }

        this.replaceArrow(delta - width + actualWidth, actualWidth, 'left')
      } else {
        this.replaceArrow(actualHeight - height, actualHeight, 'top')
      }

      if (replace) $tip.offset(offset)
    }

  , replaceArrow: function(delta, dimension, position){
      this
        .arrow()
        .css(position, delta ? (50 * (1 - delta / dimension) + "%") : '')
    }

  , setContent: function () {
      var $tip = this.tip()
        , title = this.getTitle()

      $tip.find('.k-tooltip__inner')[this.options.html ? 'html' : 'text'](title)
      $tip.removeClass('fade in top bottom left right')
    }

  , hide: function () {
      var that = this
        , $tip = this.tip()
        , e = $.Event('hide');


      // Bootstrap tooltips emit a "hide" event on tooltip trigger element and MooTools runs hide() on it
      // Make sure MooTools doesn't hide the tooltip trigger elements after hiding the tooltip box
      if (typeof window.MooTools !== 'undefined' && !this.mootools_compatible) {
          var mHide = window.Element.prototype.hide;
          window.Element.implement({
              hide: function() {
                  if ($(this).data('ktooltip')) {
                      return this;
                  }
                  mHide.apply(this, arguments);
              }
          });

          this.mootools_compatible = true;
      }

      this.$element.trigger(e)
      if (e.isDefaultPrevented()) return

      $tip.removeClass('in')

      function removeWithAnimation() {
        var timeout = setTimeout(function () {
          $tip.off($.support.transition.end).detach()
        }, 500)

        $tip.one($.support.transition.end, function () {
          clearTimeout(timeout)
          $tip.detach()
        })
      }

      $.support.transition && this.$tip.hasClass('fade') ?
        removeWithAnimation() :
        $tip.detach()

      this.$element.trigger('hidden')

      return this
    }

  , fixTitle: function () {
      var $e = this.$element
      if ($e.attr('title') || typeof($e.attr('data-original-title')) != 'string') {
        $e.attr('data-original-title', $e.attr('title') || '').attr('title', '')
      }
    }

  , hasContent: function () {
      return this.getTitle()
    }

  , getPosition: function () {
      var el = this.$element[0]
      return $.extend({}, (typeof el.getBoundingClientRect == 'function') ? el.getBoundingClientRect() : {
        width: el.offsetWidth
      , height: el.offsetHeight
      }, this.$element.offset())
    }

  , getTitle: function () {
      var title
        , $e = this.$element
        , o = this.options

      title = $e.attr('data-original-title')
        || (typeof o.title == 'function' ? o.title.call($e[0]) :  o.title)

      return title
    }

  , tip: function () {
      return this.$tip = this.$tip || $(this.options.template)
    }

  , arrow: function(){
      return this.$arrow = this.$arrow || this.tip().find(".k-tooltip__arrow")
    }

  , validate: function () {
      if (!this.$element[0].parentNode) {
        this.hide()
        this.$element = null
        this.options = null
      }
    }

  , enable: function () {
      this.enabled = true
    }

  , disable: function () {
      this.enabled = false
    }

  , toggleEnabled: function () {
      this.enabled = !this.enabled
    }

  , toggle: function (e) {
      var self = e ? $(e.currentTarget)[this.type](this._options).data(this.type) : this
      self.tip().hasClass('in') ? self.hide() : self.show()
    }

  , destroy: function () {
      this.hide().$element.off('.' + this.type).removeData(this.type)
    }

  }


 /* TOOLTIP PLUGIN DEFINITION
  * ========================= */

  var old = $.fn.tooltip

  $.fn.ktooltip = function ( option ) {
    return this.each(function () {
      var $this = $(this)
        , data = $this.data('ktooltip')
        , options = typeof option == 'object' && option
      if (!data) $this.data('ktooltip', (data = new Tooltip(this, options)))
      if (typeof option == 'string') data[option]()
    })
  }

  $.fn.ktooltip.Constructor = Tooltip

  $.fn.ktooltip.defaults = {
    animation: true
  , placement: 'top'
  , selector: false
  , template: '<div class="k-tooltip"><div class="k-tooltip__arrow"></div><div class="k-tooltip__inner"></div></div>'
  , trigger: 'hover focus'
  , title: ''
  , delay: 0
  , html: false
  , container: false
  }


 /* TOOLTIP NO CONFLICT
  * =================== */

  /*$.fn.tooltip.noConflict = function () {
    $.fn.tooltip = old
    return this
  }

  $.fn.ktooltip = $.fn.tooltip.noConflict();*/

}(window.jQuery);



//ui.initialize.js
var kodekitUI = typeof kodekitUI !== 'undefined' ? kodekitUI : {};

// Add js-enabled class to html element
document.documentElement.classList.add('k-js-enabled');



//ui.konami.js
/*
 * Konami-JS ~
 * :: Now with support for touch events and multiple instances for
 * :: those situations that call for multiple easter eggs!
 * Code: https://github.com/snaptortoise/konami-js
 * Examples: http://www.snaptortoise.com/konami-js
 * Copyright (c) 2009 George Mandis (georgemandis.com, snaptortoise.com)
 * Version: 1.4.5 (3/2/2016)
 * Licensed under the MIT License (http://opensource.org/licenses/MIT)
 * Tested in: Safari 4+, Google Chrome 4+, Firefox 3+, IE7+, Mobile Safari 2.2.1 and Dolphin Browser
 */

var Konami = function (callback) {
    var konami = {
        addEvent: function (obj, type, fn, ref_obj) {
            if (obj.addEventListener)
                obj.addEventListener(type, fn, false);
            else if (obj.attachEvent) {
                // IE
                obj["e" + type + fn] = fn;
                obj[type + fn] = function () {
                    obj["e" + type + fn](window.event, ref_obj);
                };
                obj.attachEvent("on" + type, obj[type + fn]);
            }
        },
        input: "",
        pattern: "38384040373937396665",
        load: function (link) {
            this.addEvent(document, "keydown", function (e, ref_obj) {
                if (ref_obj) konami = ref_obj; // IE
                konami.input += e ? e.keyCode : event.keyCode;
                if (konami.input.length > konami.pattern.length)
                    konami.input = konami.input.substr((konami.input.length - konami.pattern.length));
                if (konami.input == konami.pattern) {
                    konami.code(link);
                    konami.input = "";
                    e.preventDefault();
                    return false;
                }
            }, this);
            this.iphone.load(link);
        },
        code: function (link) {
            window.location = link
        },
        iphone: {
            start_x: 0,
            start_y: 0,
            stop_x: 0,
            stop_y: 0,
            tap: false,
            capture: false,
            orig_keys: "",
            keys: ["UP", "UP", "DOWN", "DOWN", "LEFT", "RIGHT", "LEFT", "RIGHT", "TAP", "TAP"],
            code: function (link) {
                konami.code(link);
            },
            load: function (link) {
                this.orig_keys = this.keys;
                konami.addEvent(document, "touchmove", function (e) {
                    if (e.touches.length == 1 && konami.iphone.capture == true) {
                        var touch = e.touches[0];
                        konami.iphone.stop_x = touch.pageX;
                        konami.iphone.stop_y = touch.pageY;
                        konami.iphone.tap = false;
                        konami.iphone.capture = false;
                        konami.iphone.check_direction();
                    }
                });
                konami.addEvent(document, "touchend", function (evt) {
                    if (konami.iphone.tap == true) konami.iphone.check_direction(link);
                }, false);
                konami.addEvent(document, "touchstart", function (evt) {
                    konami.iphone.start_x = evt.changedTouches[0].pageX;
                    konami.iphone.start_y = evt.changedTouches[0].pageY;
                    konami.iphone.tap = true;
                    konami.iphone.capture = true;
                });
            },
            check_direction: function (link) {
                x_magnitude = Math.abs(this.start_x - this.stop_x);
                y_magnitude = Math.abs(this.start_y - this.stop_y);
                x = ((this.start_x - this.stop_x) < 0) ? "RIGHT" : "LEFT";
                y = ((this.start_y - this.stop_y) < 0) ? "DOWN" : "UP";
                result = (x_magnitude > y_magnitude) ? x : y;
                result = (this.tap == true) ? "TAP" : result;

                if (result == this.keys[0]) this.keys = this.keys.slice(1, this.keys.length);
                if (this.keys.length == 0) {
                    this.keys = this.orig_keys;
                    this.code(link);
                }
            }
        }
    };

    typeof callback === "string" && konami.load(callback);
    if (typeof callback === "function") {
        konami.code = callback;
        konami.load();
    }

    return konami;
};



//ui.custom-file-input.js
/*
	By Osvaldas Valutis, www.osvaldas.info
	Available for use under the MIT License
*/

(function($) {

    $.fn.kfileinput = function() {

        return this.each(function () {
            var $input = $(this),
                data = $input.data('kfileinput');

            if (!data) {
                $input.data('kfileinput', true);

                var input    = $input[0],
					label	 = input.nextElementSibling,
                    labelVal = label.innerHTML;

                input.addEventListener('change', function( e )
                {
                    var fileName = '';
                    if( this.files && this.files.length > 1 )
                        fileName = ( this.getAttribute('data-multiple-caption') || '' ).replace( '{count}', this.files.length );
                    else
                        fileName = e.target.value.split( '\\' ).pop();

                    if( fileName )
                        label.querySelector('.k-file-input__files').innerHTML = fileName;
                    else
                        label.innerHTML = labelVal;
                });

                // Add class for drop hover
                input.ondragover = function(ev) { this.classList.add('k-has-drop-focus'); };
                input.ondragleave = function(ev) { this.classList.remove('k-has-drop-focus'); };
                input.ondragend = function(ev) { this.classList.remove('k-has-drop-focus'); };
                input.ondrop = function(ev) { this.classList.remove('k-has-drop-focus'); };

                // Firefox bug fix
                input.addEventListener('focus', function(){ input.classList.add('k-has-focus'); });
                input.addEventListener('blur', function(){ input.classList.remove('k-has-focus'); });
            }
        });
    };

})(kQuery);




//ui.tabbable.js
/*!
 * jQuery.tabbable 1.0 - Simple utility for selecting the next / previous ':tabbable' element.
 * https://github.com/marklagendijk/jQuery.tabbable
 *
 * Includes ':tabbable' and ':focusable' selectors from jQuery UI Core
 *
 * Copyright 2013, Mark Lagendijk
 * Released under the MIT license
 *
 */
(function($){

    /**
     * Focusses the next :focusable element. Elements with tabindex=-1 are focusable, but not tabable.
     * Does not take into account that the taborder might be different as the :tabbable elements order
     * (which happens when using tabindexes which are greater than 0).
     */
    $.focusNext = function(){
        selectNextTabbableOrFocusable(':focusable');
    };

    /**
     * Focusses the previous :focusable element. Elements with tabindex=-1 are focusable, but not tabable.
     * Does not take into account that the taborder might be different as the :tabbable elements order
     * (which happens when using tabindexes which are greater than 0).
     */
    $.focusPrev = function(){
        selectPrevTabbableOrFocusable(':focusable');
    };

    /**
     * Focusses the next :tabable element.
     * Does not take into account that the taborder might be different as the :tabbable elements order
     * (which happens when using tabindexes which are greater than 0).
     */
    $.tabNext = function(){
        selectNextTabbableOrFocusable(':tabbable');
    };

    /**
     * Focusses the previous :tabbable element
     * Does not take into account that the taborder might be different as the :tabbable elements order
     * (which happens when using tabindexes which are greater than 0).
     */
    $.tabPrev = function(){
        selectPrevTabbableOrFocusable(':tabbable');
    };

    function selectNextTabbableOrFocusable(selector){
        var selectables = $(selector);
        var current = $(':focus');
        var nextIndex = 0;
        if(current.length === 1){
            var currentIndex = selectables.index(current);
            if(currentIndex + 1 < selectables.length){
                nextIndex = currentIndex + 1;
            }
        }

        selectables.eq(nextIndex).focus();
    }

    function selectPrevTabbableOrFocusable(selector){
        var selectables = $(selector);
        var current = $(':focus');
        var prevIndex = selectables.length - 1;
        if(current.length === 1){
            var currentIndex = selectables.index(current);
            if(currentIndex > 0){
                prevIndex = currentIndex - 1;
            }
        }

        selectables.eq(prevIndex).focus();
    }

    /**
     * :focusable and :tabbable, both taken from jQuery UI Core
     */
    $.extend($.expr[ ':' ], {
        data: $.expr.createPseudo ?
            $.expr.createPseudo(function(dataName){
                return function(elem){
                    return !!$.data(elem, dataName);
                };
            }) :
            // support: jQuery <1.8
            function(elem, i, match){
                return !!$.data(elem, match[ 3 ]);
            },

        focusable: function(element){
            return focusable(element);
        },

        tabbable: function(element){
            var tabIndex = $.attr(element, 'tabindex'),
                isTabIndexNaN = isNaN(tabIndex);
            return ( isTabIndexNaN || tabIndex >= 0 ) && focusable(element);
        }
    });

    /**
     * focussable function, taken from jQuery UI Core
     * @param element
     * @returns {*}
     */
    function focusable(element){
        var map, mapName, img,
            nodeName = element.nodeName.toLowerCase(),
            isTabIndexNotNaN = !isNaN($.attr(element, 'tabindex'));
        if('area' === nodeName){
            map = element.parentNode;
            mapName = map.name;
            if(!element.href || !mapName || map.nodeName.toLowerCase() !== 'map'){
                return false;
            }
            img = $('img[usemap=#' + mapName + ']')[0];
            return !!img && visible(img);
        }
        return ( /input|select|textarea|button|object/.test(nodeName) ?
                !element.disabled :
                'a' === nodeName ?
                element.href || isTabIndexNotNaN :
                    isTabIndexNotNaN) &&
                // the element and all of its ancestors must be visible
            visible(element);

        function visible(element){
            return $.expr.filters.visible(element) && !$(element).parents().addBack().filter(function(){
                    return $.css(this, 'visibility') === 'hidden';
                }).length;
        }
    }
})(kQuery);


//ui.off-canvas-menu.js
/* @preserve
 * Off canvas menu
 * Copyright 2015 Robin Poort
 * http://www.robinpoort.com
 */

(function($) {

    $.offCanvasMenu = function(element, options) {

        var defaults = {
                menu: $(element),
                position: 'left',
                menuExpandedClass: 'k-show-left-menu',
                openedClass: 'k-is-opened',
                transitionClass: 'k-is-transitioning',
                noTransitionClass: 'k-no-transition',
                wrapper: $(element).parent(),
                container: $('.container'),
                menuToggle: [],
                expandedWidth: $(element).outerWidth(),
                offCanvasOverlay: 'k-off-canvas-overlay',
                offCanvasOverlayPosition: 'after',
                ariaControls: null,
                opacity: .75,
                onBeforeToggleOpen: function() {},
                onAfterToggleOpen: function() {},
                onBeforeToggleClose: function() {},
                onAfterToggleClose: function() {}
            },
            plugin = this;


        plugin.settings = {};

        plugin.init = function() {

            plugin.settings = $.extend({}, defaults, options);

            var menu = plugin.settings.menu,
                position = plugin.settings.position,
                menuExpandedClass = plugin.settings.menuExpandedClass,
                openedClass = plugin.settings.openedClass,
                transitionClass = plugin.settings.noTransitionClass,
                noTransitionClass = plugin.settings.noTransitionClass,
                wrapper = plugin.settings.wrapper,
                container = plugin.settings.container,
                menuToggle = plugin.settings.menuToggle,
                ariaControls = plugin.settings.ariaControls,
                expandedWidth = menu.outerWidth(),
                offCanvasOverlay = $('.' + plugin.settings.offCanvasOverlay),
                transitionDuration = Math.round(parseFloat(container.css('transition-duration')) * 1000),
                transitionElements = plugin.settings.transitionElements,
                languageDirection = $('html').attr('dir'),
                timeout;

            // Set proper menuExpandedClass if not set manually
            if ( position === 'right' && !options.menuExpandedClass ) {
                menuExpandedClass = 'k-show-right-menu';
            }

            // Set proper menuExpandedClass if not set manually
            if ( wrapper.is('body') ) {
                wrapper = $('html, body');
            }

            // Set transitionElements
            if ( plugin.settings.transitionElements == null) {
                transitionElements = container;
            }

            // Create overlay wrapper
            function addOverlay() {
                $.each(transitionElements, function() {
                    if ($(this).find('.' + plugin.settings.offCanvasOverlay)[0] == undefined) {
                        if ( plugin.settings.offCanvasOverlayPosition !== 'after' ) {
                            $(this).prepend('<div class="' + plugin.settings.offCanvasOverlay + '">');
                        } else {
                            $(this).append('<div class="' + plugin.settings.offCanvasOverlay + '">');
                        }
                        var newOverlay = $('.' + plugin.settings.offCanvasOverlay);
                        $.extend(offCanvasOverlay, newOverlay);
                    }
                });
            }

            addOverlay();

            function tabToggle(menu) {
                // When tabbing on toggle button
                menuToggle.bind('keydown', function(e) {
                    if (e.keyCode === 9 && wrapper.hasClass(menuExpandedClass) ) {
                        e.preventDefault();
                        if ( e.shiftKey ) {
                            menu.find(':tabbable').last().focus();
                        } else {
                            menu.find(':tabbable').first().focus();
                        }
                    }
                });

                // When tabbing on first tabbable menu item
                menu.find(':tabbable').first().bind('keydown', function(e) {
                    if (e.keyCode === 9 && wrapper.hasClass(menuExpandedClass) ) {
                        if ( e.shiftKey ) {
                            e.preventDefault();
                            menuToggle.focus();
                        }
                    }
                });

                // When tabbing on last tabbable menu item
                menu.find(':tabbable').last().bind('keydown', function(e) {
                    if (e.keyCode === 9 && wrapper.hasClass(menuExpandedClass) ) {
                        if ( !e.shiftKey ) {
                            e.preventDefault();
                            menuToggle.focus();
                        }
                    }
                });
            }

            function openMenu(menu) {
                // Clear the timeout when user clicks open menu
                clearTimeout(timeout);

                // Add class to body
                $('body').addClass(plugin.settings.transitionClass);

                // Function to run before toggling
                plugin.settings.onBeforeToggleOpen();

                addOverlay();

                // Set to expanded for accessibility
                menuToggle.attr({'aria-expanded': 'true'});

                // Add classes and CSS to the wrapper
                // All styling in CSS comes from this parent element
                wrapper.addClass(menuExpandedClass + ' ' + openedClass);

                // Enable tabbing within menu
                timeout = setTimeout(function() {
                    tabToggle(menu);

                    // Remove class from body
                    $('body').removeClass(plugin.settings.transitionClass);

                    // Function to run after toggling
                    plugin.settings.onAfterToggleOpen();
                }, transitionDuration);
            }

            function closeMenu() {
                // Clear the timeout when user clicks close menu
                clearTimeout(timeout);

                // Add class to body
                $('body').addClass(plugin.settings.transitionClass);

                // Function to run before toggling
                plugin.settings.onBeforeToggleOpen();

                // Set to collapsed for accessibility
                menuToggle.attr({'aria-expanded': 'false'});

                // Remove the expanded class to activate the transition
                wrapper.removeClass(menuExpandedClass);

                // Remove style and class when transition has ended, so the menu stays visible on closing
                timeout = setTimeout(function() {
                    wrapper.removeClass(openedClass);

                    // Remove class from body
                    $('body').removeClass(plugin.settings.transitionClass);

                    // Function to run after toggling
                    plugin.settings.onAfterToggleOpen();
                }, transitionDuration);
            }

            function toggleMenu(menu, event) {
                // Close other menu when opened
                if ( wrapper.is('[class*="'+openedClass+'"]') && !wrapper.is('[class*="'+openedClass+'"]') ) {
                    var brother = wrapper.find('button[class^="k-off-canvas-toggle"]').not(menuToggle);
                    brother.trigger('click');
                }
                // Decide wether to open or close the menu
                event.stopPropagation();
                var method = !wrapper.hasClass(menuExpandedClass) ? 'k-is-closed' : 'k-is-opened';
                if ( method === 'k-is-closed' ) { openMenu(menu); }
                if ( method === 'k-is-opened' ) { closeMenu(); }
            }

            // If we have a toggle button available
            if(menuToggle.length){

                // Set ARIA attributes
                menuToggle.attr({
                    'role': 'button',
                    'aria-controls': ariaControls,
                    'aria-expanded': 'false'
                });

                // Toggle button:
                menuToggle.off().click(function(event) {
                    if ( menuToggle.is(':visible') ) {
                        toggleMenu(menu, event);
                    }
                });

                // Close menu by clicking anywhere
                wrapper.click(function(event){
                    if ( wrapper.hasClass(menuExpandedClass) ) {
                        if ( event.target == $('.'+plugin.settings.offCanvasOverlay)[0] ) {
                            event.stopPropagation();
                            closeMenu();
                        }
                    }
                });

                // Close menu if esc keydown and menu is open and set focus to toggle button
                $(document).bind('keydown', function(event) {
                    if (event.keyCode === 27 && wrapper.hasClass(menuExpandedClass)) {
                        event.stopPropagation();
                        closeMenu();
                        menuToggle.focus();
                    }
                });
            }

            // Touch actions
            if ('ontouchstart' in document.documentElement) {
                wrapper.on('touchstart', onTouchStart);
                wrapper.on('touchmove', onTouchMove);
                wrapper.on('touchend', onTouchEnd);
            }

            // vars
            var started = null,
                start = {},
                deltaX,
                pageX,
                overlayOpacity,
                isScrolling = false;

            // Functions
            function currentPosition() {
                return position == 'left' ? menu.offset().left + expandedWidth
                    : menu.offset().left;
            }

            function inBounds(newPos) {
                return (position == 'left' && newPos >= -25 && newPos <= expandedWidth) ||
                    (position == 'right' && newPos >= -(expandedWidth) && newPos <= 25);
            }

            // Return if language == RTL
            if ( languageDirection != 'ltr' ) return;

            function onTouchStart(e) {

                if (!wrapper.hasClass(menuExpandedClass)) {
                    return;
                }

                // Set started to true (used by touchend)
                started = true;

                // Add class to body
                $('body').addClass(plugin.settings.transitionClass);

                // Get original starting point
                pageX = e.originalEvent.touches[0].pageX;

                // Setting the start object for 'move' and 'end'
                start = {
                    startingX: currentPosition(),
                    // get touch coordinates for delta calculations in onTouchMove
                    pageX: pageX,
                    pageY: e.originalEvent.touches[0].pageY
                };

                // reset deltaX
                deltaX = wrapper.position().left;

                // used for testing first onTouchMove event
                isScrolling = undefined;

                // Get the opacity of the overlay
                overlayOpacity = plugin.settings.opacity;

                // Add class to remove transition for 1-to-1 touch movement
                $.each(transitionElements, function () {
                    $(this).addClass(noTransitionClass);
                });
                $.each(offCanvasOverlay, function () {
                    $(this).addClass(noTransitionClass);
                });

                e.stopPropagation();

            }

            function onTouchMove(e) {

                if (!wrapper.hasClass(menuExpandedClass)) {
                    return;
                }

                deltaX = e.originalEvent.touches[0].pageX - start.pageX;

                // determine if scrolling test has run - one time test
                if (typeof isScrolling == 'undefined') {
                    isScrolling = !!(isScrolling || Math.abs(deltaX) < Math.abs(e.originalEvent.touches[0].pageY - start.pageY));
                }

                // if user is not trying to scroll vertically
                if (!isScrolling) {

                    // prevent native scrolling
                    e.preventDefault();

                    var newPos = position == 'left' ? start.startingX + deltaX
                        : deltaX - ($(window).width() - start.startingX);

                    var opacity = (overlayOpacity / expandedWidth) * Math.abs(newPos);

                    if (!inBounds(newPos))
                        return;

                    // translate immediately 1-to-1
                    $.each(transitionElements, function () {
                        if (!$(this).hasClass('k-title-bar--mobile')) {
                            $(this).css({
                                '-webkit-transform': 'translate(' + newPos + 'px, 0)',
                                '-moz-transform': 'translate(' + newPos + 'px, 0)',
                                '-ms-transform': 'translate(' + newPos + 'px, 0)',
                                '-o-transform': 'translate(' + newPos + 'px, 0)',
                                'transform': 'translate(' + newPos + 'px, 0)'
                            });
                        }
                    });
                    $.each(offCanvasOverlay, function () {
                        $(this).css('opacity', opacity);
                    });

                    e.stopPropagation();
                }
            }

            function onTouchEnd(e) {

                // Escape if invalid start:
                if (!started)
                    return;

                // Escape if Menu is closed
                if (!wrapper.hasClass(menuExpandedClass))
                    return;

                // Remove class from body
                $('body').removeClass(plugin.settings.transitionClass);

                var newPos = position == 'left' ? start.startingX + deltaX
                    : deltaX - ($(window).width() - start.startingX);

                // Converting to positive number
                var absNewPos = Math.abs(newPos);

                // if not scrolling vertically
                if (!isScrolling) {

                    $.each(transitionElements, function () {
                        container.removeAttr('style').removeClass(noTransitionClass);
                        $('.k-js-title-bar').removeAttr('style').removeClass(noTransitionClass);
                    });
                    $.each(offCanvasOverlay, function () {
                        $(this).removeAttr('style').removeClass(noTransitionClass);
                        if (plugin.settings.transitionElements !== undefined) {
                            plugin.settings.transitionElements.removeAttr('style').removeClass(noTransitionClass);
                        }
                    });

                    if (( position == 'left' && ( absNewPos <= (expandedWidth * 0.66) || newPos <= 0 ) ) ||
                        ( position == 'right' && ( absNewPos <= (expandedWidth * 0.66) || newPos >= 0 ) )) {
                        closeMenu();
                    } else {
                        openMenu(menu);
                    }
                }

                // Reset start object and starting variable:
                started = null;
                start = {};

                e.stopPropagation();
            }

        };

        plugin.init();

    };


    // add the plugin to the jQuery.fn object
    $.fn.offCanvasMenu = function(options) {
        // iterate through the DOM elements we are attaching the plugin to
        return this.each(function() {
            // if plugin has not already been attached to the element
            if (undefined == $(this).data('offCanvasMenu')) {
                // create a new instance of the plugin
                var plugin = new $.offCanvasMenu(this, options);
                // in the jQuery version of the element
                // store a reference to the plugin object
                $(this).data('offCanvasMenu', plugin);
            }
        });
    }

})(kQuery);



//ui.ajaxloading.js
/**
 * Sidebar off-canvas toggles
 */

(function($) {

    kodekitUI.ajaxloading = function() {

        var ajaxLink = $('[data-ajax-target]');
        if ( ajaxLink.length ) {

            $('.k-ui-container').on('click', ajaxLink, function(event) {

                // Variables
                var $target = event.target,
                    href = $target.href,
                    ajaxTarget = $($target).attr('data-ajax-target'),
                    activeClass = 'k-is-active';

                if ( !ajaxTarget ) return;

                event.preventDefault();
                event.stopPropagation();

                // Find the 'active' element
                var getActiveElement = function(element) {
                    for ( ; element && element !== document; element = element.parentNode ) {
                        if ( $(element).parent().find('.'+activeClass).length ) return element;
                    }
                    return null;
                };
                var $activeElement = getActiveElement($target);

                // Remove class from siblings and add class to current item
                $($activeElement).parent().children('.'+activeClass).removeClass(activeClass);
                $($activeElement).addClass(activeClass);

                // Load
                // warning: <script> will get stripped from content
                $('#'+ajaxTarget).load(href + ' #'+ajaxTarget+' > :first-child', function(responseTxt, statusTxt, xhr) {

                    // Success
                    if(statusTxt == "success") {

                        // Trigger close sidebar click when changing menu items
                        if ( $('.k-js-wrapper').hasClass('k-show-left-menu') ) {
                            $('.k-off-canvas-toggle--left').trigger('click');
                        }

                        // Flat text page values
                        var pageHead = responseTxt.split('<head>')[1].split('</head>')[0],
                            pageTitle = pageHead.split('<title>')[1].split('</title>')[0];

                        // Trigger loaded code
                        kodekitUI.loaded(responseTxt, statusTxt, xhr, pageHead, pageTitle);

                    }

                    // Error
                    if(statusTxt == "error") {
                        console.error("Error: " + xhr.status + ": " + xhr.statusText);
                    }

                });

            });

        }

    };

})(kQuery);



//ui.dragger.js
/**
 * Sidebar off-canvas toggles
 */

(function($) {

    var styleElement;

    function setCSS(css) {
        if (!styleElement) {
            styleElement = document.createElement('style');
            styleElement.type = 'text/css';
            styleElement.setAttribute('data-type', 'kodekitStyles');
            (document.head || document.getElementsByTagName('head')[0]).appendChild(styleElement);
        }

        // Add CSS to style element
        if (styleElement.styleSheet){
            styleElement.styleSheet.cssText += css;
        } else {
            styleElement.innerHTML += css;
        }
    }

    function readCookie(name) {
        var nameEQ = name + "=";
        var ca = document.cookie.split(';');
        for(var i=0;i < ca.length;i++) {
            var c = ca[i];
            while (c.charAt(0) === ' ') {
                c = c.substring(1,c.length);
            }
            if (c.indexOf(nameEQ) === 0) {
                return c.substring(nameEQ.length,c.length);
            }
        }
        return null;
    }

    function createCookie(name, value, days) {
        var expires;
        if (days) {
            var date = new Date();
            date.setTime(date.getTime()+(days*24*60*60*1000));
            expires = "; expires="+date.toGMTString();
        }
        else {
            expires = "";
        }
        document.cookie = name+"="+value+expires+"; path=/";
    }

    kodekitUI.dragger = function() {

        var middlepane = document.querySelector(".k-js-middlepane");
        if (middlepane !== null && document.querySelector('.k-pane-resizer') == undefined) {

            // If a cookie is set for middlepane
            var middlepaneWidthCookieValue = readCookie("kodekitUI.middlepanewidth");

            if (middlepaneWidthCookieValue !== null) {
                setCSS(
                    '@media screen and (min-width: 1024px) {' +
                    '.k-ui-container .k-content-area .k-content:not(:last-child) {' +
                    'min-width:'+middlepaneWidthCookieValue+'px;' +
                    'width:'+middlepaneWidthCookieValue+'px;' +
                    'max-width:'+middlepaneWidthCookieValue+'px;' +
                    '}' +
                    '}'
                );
            }

            var middlepaneResizer = document.createElement("div");
            middlepaneResizer.className = "k-pane-resizer";
            middlepane.appendChild(middlepaneResizer);
            middlepaneResizer.addEventListener("mousedown", initDrag, false);
            var startW, startWidth, newWidth, direction;
        }

        function initDrag(e) {
            startW = e.clientX;
            startWidth = parseInt(document.defaultView.getComputedStyle(middlepane).width, 10);
            direction = document.documentElement.getAttribute('dir') || 'ltr';
            document.documentElement.addEventListener("mousemove", doDrag, false);
            document.documentElement.addEventListener("mouseup", stopDrag, false);
        }

        function doDrag(e) {
            document.getElementsByClassName('k-ui-container')[0].classList.add("k-is-unresponsive");
            if ( direction == 'ltr' ) {
                newWidth = (startWidth + e.clientX - startW);
            } else {
                newWidth = (startWidth - (e.clientX - startW));
            }
            if (newWidth <= 221) {
                newWidth = 221;
            }
            middlepane.style.width = newWidth + "px";
            middlepane.style.minWidth = newWidth + "px";
            middlepane.style.maxWidth = newWidth + "px";
        }

        function stopDrag(e) {
            document.documentElement.removeEventListener("mousemove", doDrag, false);
            document.documentElement.removeEventListener("mouseup", stopDrag, false);
            document.getElementsByClassName('k-ui-container')[0].classList.remove("k-is-unresponsive");
            middlepane.removeAttribute('style');

            var width;

            if ( direction == 'ltr' ) {
                width = startWidth + e.clientX - startW;
            } else {
                width = (startWidth - (e.clientX - startW));
            }
            if (width <= 221) {
                width = 221;
            }

            createCookie("kodekitUI.middlepanewidth", width);
            setCSS(
                '@media screen and (min-width: 1024px) {' +
                '.k-ui-container .k-content-area .k-content:not(:last-child) {' +
                'min-width:'+width+'px;' +
                'width:'+width+'px;' +
                'max-width:'+width+'px;' +
                '}' +
                '}'
            );
            window.dispatchEvent(new Event('resize'));
        }
    };

})(kQuery);



//ui.gallery.js
/**
 * Gallery
 */

(function($) {

    kodekitUI.gallery = function() {

        var $gallery = $('.k-gallery');
        if ( $gallery.length ) {

            // variables
            var galleryItems = $gallery[0].querySelector('.k-gallery__items'),
                galleryMaxWidth = parseInt(((window.getComputedStyle(galleryItems, null).getPropertyValue('content')).split('"')[1]), 10),
                galleryEventTimeout;

            // Throttle window resize function for better performance
            var resizeThrottler = function() {
                if (!galleryEventTimeout) {
                    galleryEventTimeout = setTimeout(function() {
                        galleryEventTimeout = null; // Reset timeout
                        // Walk through all galleries
                        setWidth();
                    }, 200);
                }
            };

            // Set Width
            var setWidth = function() {
                var galleryWidth = parseFloat($gallery.width()),
                    items = Math.ceil(galleryWidth / galleryMaxWidth);
                $gallery.attr('data-gallery-items', items - 1);
            };


            // Run on default
            setWidth();

            // Run on window resize
            window.addEventListener( 'resize', resizeThrottler );

        }

    };

})(kQuery);



//ui.sidebartoggle.js
/**
 * Sidebar off-canvas toggles
 */

(function($) {

    kodekitUI.sidebarToggle = function() {

        if ($('.k-js-title-bar, .k-js-toolbar').length && $('.k-js-wrapper').length && $('.k-js-content').length) {

            // Vars
            var sidebar_left  = $('.k-js-sidebar-left'),
                sidebar_right = $('.k-js-sidebar-right');

            function addOffCanvasButton(element, position) {

                var toggle_button_content = element.attr('data-toggle-button-content') || '<span class="k-toggle-button-bar1"></span><span class="k-toggle-button-bar2"></span><span class="k-toggle-button-bar3"></span>';
                var toggle_button = '<div class="k-off-canvas-toggle-holder">' +
                    '<button class="k-off-canvas-toggle" type="button">' +
                    toggle_button_content +
                    '</button>' +
                    '</div>';

                // Variables
                var kContainer = '.k-ui-container',
                    container = element.closest(kContainer),
                    titlebar = container.find('.k-js-title-bar'),
                    toolbar = container.find('.k-js-toolbar'),
                    wrapper = container.find('.k-js-wrapper'),
                    content = container.find('.k-js-content'),
                    contentArea = container.find('.k-js-content-area'),
                    page = container.find('.k-js-page'),
                    component = container.find('.k-js-component'),
                    toggle = container.find('.k-off-canvas-toggle--' + position),
                    $toggle = $(toggle_button),
                    $toggleButton = null,
                    transitionElements;

                // Add proper class to toggle buttons
                $toggle.addClass('k-off-canvas-toggle-holder--' + position).children('button').addClass('k-off-canvas-toggle--' + position);

                var offcanvascontainer = content;
                transitionElements = content;
                if ( contentArea.length ) {
                    offcanvascontainer = contentArea;
                    transitionElements = contentArea;
                }

                // Add toggle buttons
                if (toggle.length === 0) {
                    if ( position == 'left' ) {
                        if ( titlebar.length) {
                            titlebar.prepend($toggle);
                        } else if (toolbar.length) {
                            toolbar.prepend($toggle);
                        }
                    } else if ( position == 'right') {
                        if ( toolbar.length) {
                            toolbar.append($toggle);
                        } else if (titlebar.length) {
                            titlebar.append($toggle);
                        }
                        transitionElements = component;
                    }

                    $toggleButton = $('.k-off-canvas-toggle--' + position);

                    // Initialize the offcanvas plugin
                    element.offCanvasMenu({
                        menuToggle: $toggleButton,
                        openedClass: 'k-is-opened-' + position,
                        wrapper: wrapper,
                        container: offcanvascontainer,
                        position: position,
                        offCanvasOverlay: 'k-off-canvas-overlay-' + position,
                        transitionElements: transitionElements,
                        onBeforeToggleOpen: function() {
                            if ( $('.k-show-subcontent-area').length ) {
                                $('.k-js-subcontent-toggle').trigger('click');
                            }
                        }
                    });
                }
            }

            if (sidebar_left.length) {
                // Add button for left sidebar
                $.each(sidebar_left, function() {
                    addOffCanvasButton($(this), 'left');
                });

                var sidebarLeftTree = $('.k-tree'),
                    sidebarLeftList = $('.k-list');

                if ( ( sidebarLeftTree.length || sidebarLeftList.length ) ) {
                    sidebarLeftTree.on('click', '.jqtree-title', function() {
                        if ( $('.k-js-wrapper').hasClass('k-is-opened-left') ) {
                            $('.k-off-canvas-toggle--left').trigger('click');
                        }
                    });
                    sidebarLeftList.on('click', 'a', function() {
                        if ( $('.k-js-wrapper').hasClass('k-is-opened-left') ) {
                            $('.k-off-canvas-toggle--left').trigger('click');
                        }
                    });
                }


            }

            if (sidebar_right.length) {

                // Add button for right sidebar
                $.each(sidebar_right, function() {
                    addOffCanvasButton($(this), 'right');
                });

                // Open right sidebar on selecting items in table
                // Only for tables with .k-js-with-sidebar class
                // Only apply to actual `<a>` elements
                $('.k-table-container table.k-js-with-sidebar').off().on('click', 'a', function(event) {

                    // stopPropagation for all links except for those with `.navigate` class
                    if ( !$(this).hasClass('navigate') ) {
                        event.stopPropagation();
                    }

                    // Return if subcontent is present
                    if ($(this).closest('.k-content').siblings('.k-subcontent').length) return;

                    // Only apply if parent is a `<td>` (so not a `<th>`)
                    if ($(this).parents('td').length > 0) {
                        $('.k-off-canvas-toggle--right').trigger('click');
                    }

                });

                // Open subcontent on clicking TD
                $('.k-table-container table.k-js-with-sidebar tbody').off().on('click', 'tr', function(event) {
                    // Return if click to select class is added to table
                    if ( $(this).closest('table').hasClass('k-js-click-to-select')) return;

                    // Return if subcontent is present
                    if ($(this).closest('.k-content').siblings('.k-subcontent').length) return;

                    // Return if target is anchor
                    if ( event.target.nodeName === 'A') return;
                    if ( event.target.nodeName === 'INPUT') return;

                    // Stop row select action
                    event.stopPropagation();

                    // Trigger click anchor
                    $(this).find('a').trigger('click');

                });
            }
        }
    };

})(kQuery);



//ui.scopebartoggles.js
/**
 * Filter and search toggle buttons in the scopebar
 */

(function($) {

    var eventsAttached = false;

    kodekitUI.scopebarToggles = function() {

        var $scopebar = $('.k-js-scopebar');
        if ($scopebar.length) {

            $.each($scopebar, function () {

                var $this = $(this),
                    $scopebarFilters = $this.find('.k-scopebar__item--filters'),
                    $scopebarSearch = $this.find('.k-scopebar__item--search'),
                    scopebarToggleClass = '.k-scopebar__item--toggle-buttons',
                    scopebarToggleButtonContainer = '<div class="k-scopebar__item k-scopebar__item--toggle-buttons"></div>';

                if (!$this.find(scopebarToggleClass).length) {
                    $this.prepend(scopebarToggleButtonContainer);
                }
                var toggleButtons = $this.find(scopebarToggleClass);

                if ($scopebarFilters.length && !$this.find('.k-toggle-scopebar-filters').length) {
                    toggleButtons.prepend('<button type="button" class="k-scopebar__button k-toggle-scopebar-filters k-js-toggle-filters">' +
                        '<span class="k-icon-filter" aria-hidden="true">' +
                        '<span class="k-visually-hidden">Filters toggle</span>' +
                        '<div class="k-js-filter-count k-scopebar__item-label k-scopebar__item-label--numberless"></div>' +
                        '</button>');
                }

                if ($scopebarSearch.length && !$this.find('.k-toggle-scopebar-search').length) {

                    toggleButtons.prepend('<button type="button" class="k-scopebar__button k-toggle-scopebar-search k-js-toggle-search">' +
                        '<span class="k-icon-magnifying-glass" aria-hidden="true">' +
                        '<span class="k-visually-hidden">Search toggle</span>' +
                        '<div class="k-js-search-count k-scopebar__item-label k-scopebar__item-label--numberless" style="display: none"></div>' +
                        '</button>');

                    if (toggleButtons.siblings('.k-scopebar__item--search').find('.k-search__field').val()) {
                        $('.k-js-search-count').show();
                    }
                }
            });

            if (!eventsAttached) {
                eventsAttached = true;

                // Toggle search
                $(document).on('click.koowa', '.k-js-toggle-filters', function() {
                    $(event.target).parents('.k-js-scopebar').find('.k-scopebar__item--filters').slideToggle('fast');
                });

                $(document).on('click.koowa', '.k-js-toggle-search', function() {
                    $(event.target).parents('.k-js-scopebar').find('.k-scopebar__item--search').slideToggle('fast');
                });
            }
        }

    };

})(kQuery);



//ui.subcontenttoggle.js
// Subcontent toggle

(function($) {

    kodekitUI.subcontentToggle = function() {

        // Sub content itself
        var $subcontent = $('.k-js-subcontent');

        // See if it exists
        if ($subcontent.length) {

            var $contentChild = $('.k-content-area__child'),
                subcontentButtonContent = $subcontent.attr('data-toggle-button-content') || '<span class="k-icon-chevron-left" aria-hidden="true"></span>',
                toggle_button = '<button type="button" class="k-button k-button--default k-subcontent-toggle k-js-subcontent-toggle" title="Subcontent toggle" aria-label="Subcontent toggle">' + subcontentButtonContent + '</button>',
                toggle = $contentChild.find('.k-js-subcontent-toggle'),
                $toggle = $(toggle_button),
                $toggleButton = null;

            // Append toggle button and overlay
            if ( toggle.length === 0 ) {
                $contentChild.prepend($toggle);
            }

            $toggleButton = $('.k-js-subcontent-toggle');

            // Off canvas
            $subcontent.offCanvasMenu({
                menuToggle: $toggleButton,
                menuExpandedClass: 'k-show-subcontent-area',
                openedClass: 'k-is-opened-subcontent',
                position: 'right',
                container: $contentChild,
                expandedWidth: '276',
                offCanvasOverlay: 'k-off-canvas-overlay-subcontent',
                offCanvasOverlayPosition: 'before',
                wrapper: $('.k-js-content-area')
            });


            // Open right sidebar on selecting items in table
            // Only for tables with .k-js-with-subcontent class
            // Only apply to actual `<a>` elements
            $('.k-table-container table.k-js-with-subcontent a').off().on('click', function (event) {
                // Only apply if parent is a `<td>` (so not a `<th>`)
                if ($(this).parents('td').length > 0) {
                    var target = $(this)[0].closest('.k-content-area__child');
                    var targetToggle = $(target).find('.k-js-subcontent-toggle');

                    // Wait at least 2 frames to make sure actions are not attached simultaneously
                    setTimeout(function () {
                        targetToggle.trigger('click');
                    }, 32);
                }
            });

            // Open subcontent on clicking TD
            $('.k-table-container table.k-js-with-subcontent tbody').off().on('click', 'tr', function (event) {
                // Return if click to select class is added to table
                if ($(this).closest('table').hasClass('k-js-click-to-select')) return;

                // Return if target is anchor
                if (event.target.nodeName === 'A') return;
                if (event.target.nodeName === 'INPUT') return;

                // Stop row select action
                event.preventDefault();
                event.stopPropagation();

                // Trigger click anchor (but wait for ajax)
                $(this).find('a').trigger('click');
            });

        }

    };


})(kQuery);



//ui.topnavigation.js
// Top navigation

(function($) {

    $.fn.ktopnavigation = function() {

        return this.each(function() {
            var $menu = $( this ),
                data = $menu.data('ktopnavigation');

            if (!data) {
                $menu.data('ktopnavigation', true);

                // Variables
                var $menuItem = $menu.find('> ul > li > a'),
                    menuClass = 'has-open-menu',
                    submenuClass = 'has-open-submenu',
                    menuContent = $menu.attr('data-toggle-button-content') || 'Menu';

                // Append toggle button
                if ($menu.parent().find('#k-js-top-navigation-toggle').length === 0) {
                    $menu.parent().append($('<button type="button" id="k-js-top-navigation-toggle" class="k-top-navigation-toggle" title="Menu toggle" aria-label="Menu toggle">'+menuContent+'</button>'));
                }

                // Off canvas
                $menu.offCanvasMenu({
                    menuToggle: $('#k-js-top-navigation-toggle'),
                    menuExpandedClass: 'k-show-top-menu',
                    openedClass: 'k-is-opened-top',
                    position: 'right',
                    container: $('.k-js-wrapper'),
                    expandedWidth: '276',
                    offCanvasOverlay: 'k-off-canvas-overlay-top',
                    wrapper: $('.k-ui-container')
                });

                // Open a menu item
                function openMenuItem($element) {
                    if ( $menu.hasClass(menuClass) && $(this).hasClass(submenuClass) ) {
                        closeMenu();
                    } else {
                        $('.' + submenuClass).removeClass(submenuClass);
                        $element.addClass(submenuClass);
                        $menu.addClass(menuClass);
                    }
                }

                // Close all items
                function closeMenu() {
                    $menu.removeClass(menuClass).find('.' + submenuClass).removeClass(submenuClass);
                }

                // Click a menu item
                // Parent items are not navigatable just like in any other OS
                // Add your own JS to make sure links are clickable anyway
                $menuItem.on('click', function(event) {
                    if (!$(this).next('ul').length) return;

                    event.preventDefault();
                    if ( $menu.hasClass(menuClass) && $(this).hasClass(submenuClass) ) {
                        closeMenu();
                    } else {
                        openMenuItem($(this));
                    }
                });

                // Click child item
                $menu.on('click', 'ul li ul li a', function() {
                    closeMenu();
                });

                // Hover a menu item
                $menuItem.on('mouseover', function(event) {
                    // Only on desktop
                    if ( $('.k-top-container').css('z-index') >= 11) {
                        event.preventDefault();
                        if ( $menu.hasClass(menuClass) ) {
                            $menu.find('.' + submenuClass).blur();
                            openMenuItem($(this));
                        }
                    }
                });

                // On clicking next to the menu
                $(document).mouseup(function(e) {
                    var $navigationList = $menu.children('ul');

                    // if the target of the click isn't the container nor a descendant of the container
                    if (!$navigationList.is(e.target) && $navigationList.has(e.target).length === 0)
                    {
                        if ( $menu.hasClass(menuClass) ) {
                            closeMenu();
                        }
                    }
                });

                // On ESC key
                $(document).keyup(function(e) {
                    if (e.keyCode === 27) {
                        closeMenu();
                    }
                });
            }

        });
    };

})(kQuery);



//ui.tabs-scroller.js
(function($) {

    var tabsOverflowClass = 'k-has-tabs-overflow',
        tabsOverflowLeftClass = 'k-has-tabs-left-overflow',
        tabsOverflowRightClass = 'k-has-tabs-right-overflow',
        tabsScrollAmount = 0.8,
        tabsAnimationSpeed = 400;

    // Calculate wether there is a scrollable area and apply classes accordingly
    function tabsCalculateScroll($scroller, $tabs, $tabsWrapper) {

        if (!$scroller.length) return;

        // Variables
        var tabsWidth = $tabs.outerWidth(),
            scrollerWidth = $scroller.innerWidth(),
            scrollLeft = $scroller.scrollLeft();

        // Show / hide buttons
        if (tabsWidth > scrollerWidth) {
            $tabsWrapper.addClass(tabsOverflowClass);
        } else {
            $tabsWrapper.removeClass(tabsOverflowClass);
        }

        // "Activate" left button
        if ((tabsWidth > scrollerWidth) && (scrollLeft > 0)) {
            $tabsWrapper.addClass(tabsOverflowLeftClass);
        }

        // "Activate" right button
        if ((tabsWidth > scrollerWidth)) {
            $tabsWrapper.addClass(tabsOverflowRightClass);
        }

        // "Deactivate" left button
        if ((tabsWidth <= scrollerWidth) || (scrollLeft <= 0)) {
            $tabsWrapper.removeClass(tabsOverflowLeftClass);
        }

        // "Deactivate" right button
        if ((tabsWidth <= scrollerWidth) || (scrollLeft >= (tabsWidth - scrollerWidth))) {
            $tabsWrapper.removeClass(tabsOverflowRightClass);
        }
    }


    // Calculate the amount of scrolling to do
    function calculateScroll(direction, $scroller, $tabs) {

        // Variables
        var tabsWidth = $tabs.outerWidth(),
            scrollerWidth = $scroller.innerWidth(),
            scrollLeft = $scroller.scrollLeft(),
            scroll;

        // Left button (scroll to right)
        if ( direction === 'prev') {
            scroll = scrollLeft - (scrollerWidth * tabsScrollAmount);
            if (scroll < 0 ) {
                scroll = 0;
            }
        }

        // Right button (scroll to left)
        if ( direction === 'next') {
            scroll = scrollLeft + (scrollerWidth * tabsScrollAmount);
            if (scroll > (tabsWidth - scrollerWidth) ) {
                scroll = tabsWidth - scrollerWidth;
            }
        }

        // Animate the scroll
        $scroller.animate({
            scrollLeft: scroll
        }, tabsAnimationSpeed);
    }

    // Scroll active tab into screen
    function scrollToTab(element, $scroller, $tabs) {
        if (element.parent('li').parent('ul').parent().hasClass('k-js-tabs-scroller')) {
            var positionLeft = element.parent().position().left,
                positionRight = positionLeft + element.parent().outerWidth(),
                parentPaddingLeft = parseInt($tabs.css('padding-left'), 10),
                parentPaddingRight = parseInt($tabs.css('padding-right'), 10),
                scrollerOffset = $scroller.scrollLeft(),
                scrollerWidth = $scroller.innerWidth(),
                scroll;

            // When item falls of on the right side
            if ( positionRight > (scrollerOffset + scrollerWidth) ) {
                scroll = scrollerOffset + ((positionRight - (scrollerWidth + scrollerOffset)) + (parentPaddingRight * 2));
            }

            // When item falls of on the left side
            if ( positionLeft < scrollerOffset ) {
                scroll = scrollerOffset - ((scrollerOffset - positionLeft) + (parentPaddingLeft * 2));
            }

            // Animate the scroll
            $scroller.animate({
                scrollLeft: scroll
            }, tabsAnimationSpeed);
        }
    }


    $.fn.ktabscroller = function() {
        return this.each(function() {
            var $scroller = $(this),
                data = $scroller.data('ktabscroller');

            if (!data) {
                $scroller.data('ktabscroller', true);

                // Variables
                var $tabs = $scroller.find('.k-js-tabs'),
                    $tabsWrapper = $scroller.parent('.k-js-tabs-wrapper'),
                    resizeTimer;

                // Append buttons
                if (!$tabsWrapper.children('.k-tabs-scroller-prev').length) {
                    $tabsWrapper.prepend('<button type="button" class="k-tabs-scroller-prev"><span class="k-icon-chevron-left"></span><span class="k-visually-hidden">Scroll left</span></button>');
                }
                if (!$tabsWrapper.children('.k-tabs-scroller-next').length) {
                    $tabsWrapper.append('<button type="button" class="k-tabs-scroller-next"><span class="k-icon-chevron-right"></span><span class="k-visually-hidden">Scroll right</span></button>');
                }

                // Run 250ms after document ready
                // 1. To make sure tabs are loaded
                // 2. To display users that tabs are scrollable
                setTimeout(function() {
                    tabsCalculateScroll($scroller, $tabs, $tabsWrapper);

                    $tabsWrapper.on('click', '.k-tabs-scroller-prev', function() {
                        calculateScroll('prev', $scroller, $tabs);
                    });
                    $tabsWrapper.on('click', '.k-tabs-scroller-next', function() {
                        calculateScroll('next', $scroller, $tabs);
                    });

                    // Scroll to active tab after buttons have loaded
                    setTimeout(function() {
                        scrollToTab($scroller.find('.k-is-active a'), $scroller, $tabs);
                    }, tabsAnimationSpeed);
                }, 200);

                // When clicking tabs
                $tabsWrapper.on('click', 'li a', function() {
                    scrollToTab($(this), $scroller, $tabs);
                });

                // Run on scrolling the tab container
                $scroller.on('scroll', function() {
                    // Throttle
                    clearTimeout(resizeTimer);
                    resizeTimer = setTimeout(function() {
                        tabsCalculateScroll($scroller, $tabs, $tabsWrapper);
                    }, 200);
                });

                // Run on window resize
                $(window).on('resize', function() {
                    // Throttle
                    clearTimeout(resizeTimer);
                    resizeTimer = setTimeout(function() {
                        tabsCalculateScroll($scroller, $tabs, $tabsWrapper);
                    }, 200);
                });
            }
        });
    };

})(kQuery);



//ui.main.js
(function($) {


    /**
     * Footable
     */
    kodekitUI.initializeFootable = function() {
        if ($.fn.footable) {
            $('.k-js-responsive-table').removeClass('footable footable-loaded').footable({
                toggleSelector: '.footable-toggle',
                breakpoints: {
                    phone: 400,
                    tablet: 600,
                    desktop: 800
                }
            });
        }
    };


    /**
     * Select 2
     */
    kodekitUI.initializeSelect2 = function() {
        if ($.fn.select2) {
            $('.k-js-select2').select2({
                theme: "bootstrap"
            });
        }
    };


    /**
     * Datepicker
     */
    kodekitUI.initializeDatepicker = function datepicker() {
        if ($.fn.kdatepicker) {
            $('.k-js-datepicker').kdatepicker();
        }
    };


    /**
     * Magnific popup
     */
    kodekitUI.initializeModal = function() {
        if ($.fn.magnificPopup) {
            $('.k-js-image-modal').magnificPopup({type: 'image'});
            $('.k-js-inline-modal').magnificPopup({type: 'inline'});
            $('.k-js-iframe-modal').magnificPopup({type: 'iframe'});
        }

    };


    /**
     * Tooltip
     */

    kodekitUI.initializeTooltip = function() {
        if ($.fn.ktooltip) {
            $('.k-js-tooltip').ktooltip({
                animation: true,
                placement: 'top',
                delay: {show: 200, hide: 50},
                container: '.k-ui-container'
            });
        }

    };

    kodekitUI.initializeNavigation = function() {
        $('.k-js-top-navigation').ktopnavigation();
    };

    kodekitUI.initializeFileinput = function() {
        $('.k-js-file-input').kfileinput();
    };

    kodekitUI.initializeTabscroller = function() {
        $('.k-js-tabs-scroller').ktabscroller();
    };

    /**
     * Load functions
     *
     * Quick function to run all functions
     * Use on:
     * - Page load
     * - AJAX change
     * - On other DOM changes when needed
     */
    if (typeof kodekitUI.loadFunctions === 'undefined') {
        kodekitUI.loadFunctions = function() {

            /**
             * Local functions
             */
            kodekitUI.initializeFootable();
            kodekitUI.initializeSelect2();
            kodekitUI.initializeDatepicker();
            kodekitUI.initializeModal();
            kodekitUI.initializeTooltip();
            kodekitUI.initializeNavigation();
            kodekitUI.initializeFileinput();
            kodekitUI.initializeTabscroller();

            /**
             * Global kodekitUI functions
             */
            kodekitUI.sidebarToggle();
            kodekitUI.scopebarToggles();
            kodekitUI.subcontentToggle();
            kodekitUI.gallery();
            kodekitUI.dragger();
        };
    }


    $(document).ready(function () {

        /**
         * Konami
         * Not needed to reload since we're targeting html element which won't change
         */
        new Konami(function() {
            $('html, .k-ui-container').css({
                'font-family': 'Comic Sans MS'
            });
        });


        /**
         * Window resize
         */
        var resizeTimer,
            resizeClass = 'k-is-resizing';

        $(window).on('resize', function() {

            // Add class to body when resizing so we can add styling to the page
            $('body').addClass(resizeClass);

            // Throttle
            clearTimeout(resizeTimer);
            resizeTimer = setTimeout(function() {

                // Remove the class when resize is done
                $('body').removeClass(resizeClass);

            }, 200);
        });


        /**
         * Tab change
         * Run code on tab change
         */
        $('a[data-k-toggle="tab"]').on('shown', function (e) {
            kodekitUI.initializeFootable();
        });


        /**
         * Run functions DOM loaded
         */
        kodekitUI.loadFunctions();

        /**
         * Load "ajaxloading" only once to make sure events are not fire multiple times
         */
        kodekitUI.ajaxloading();
        kodekitUI.loaded = function(responseTxt, statusTxt, xhr, pageHead, pageTitle) {
            kodekitUI.loadFunctions();

        };


    });

})(kQuery);



//kquery.unset.js
window.jQuery = globalCacheForjQueryReplacement;
globalCacheForjQueryReplacement = undefined;

