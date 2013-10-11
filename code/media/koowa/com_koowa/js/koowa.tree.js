/**
 * Koowa jqTree wrapper
 *
 * Customized instance of jqTree to render a list of categories in a tree structure.
 * It deals with turning a flat list into a hierarchy structure that jqTree understands.
 *
 * @copyright	Copyright (C) 2007 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa for the canonical source repository
 * @requires    Koowa.Class, jqTree plugin
 */

(function($){

    Koowa.Tree = Koowa.Class.extend({

        initialize: function(element, options){

            this.setOptions(options);

            this.element = $(element);

            //For scrollTo to work, needs to be position:relative;
            this.element.css('position', 'relative');

            this.attachHandlers();

            this.options.data = this.parseData(this.options.data);

            //shortcut for accessing tree apis, like this: selected = this.tree('getSelectedNode')
            this.tree = $.proxy(this.element.tree, this.element);

            // Instantiate the jqTree plugin with the parsed options
            this.tree(this.options);
        },

        getDefaults: function(){

            var self = this,
                defaults = {
                    data: [], //Default empty value to avoid errors when there are no items yet
                    autoOpen: 0, //Auto open just "All Categories" by default, this value is the nesting level not the node id
                    useContextMenu: false, //This allows us to right-click menu items again
                    onCreateLi: function(node, $li){ //Method for customizing <li> markup

                        /**
                         * Adds a title attribute for the full title in case of overflow.
                         * Wraps the inner elements of the <li> with a <a> tag to apply nav list styling from bootstrap
                         * on each nav element.
                         * Doing this also increases click area to the entire row, instead of a smaller element just
                         * around the title.
                         * The href="#" gives each element cursor:pointer styling.
                         * Click handler makes sure clicking the outer <a> element selects the node, instead of doing nothing.
                         */

                        $li.find('.jqtree-element').attr('title', node.name).wrap($('<a />', {
                            href: '#',
                            on: {
                                click: function(event){
                                    //If event target is .jqtree-element then the following code isn't needed
                                    if(!$(event.target).is('.jqtree-element')) {
                                        event.preventDefault(); //Prevent '#' added to the url, causing a scroll to top
                                        event.stopPropagation(); //Prevent bubbling up to the click handler in jqTree

                                        // Pass null to deselect, selectNode API never deselects when passing a node
                                        self.tree('selectNode', self.tree('getSelectedNode') !== node ? node : null);
                                    }
                                }
                            }
                        }));

                        if(node.isFolder()) {
                            // states variable is for easy toggling on the click event
                            var states = [
                                    {
                                        triangle: ['icon-triangle-right', '&#x25ba;'], folder: 'icon-folder-close'
                                    },
                                    {
                                        triangle: ['icon-triangle-down', '&#x25bc;'], folder: 'icon-folder-open'
                                    }
                                ],
                                state = states[node.is_open ? 1 : 0],
                                triangle = $('<i />', {
                                    'class': state.triangle[0], //Either icon-triangle-right or icon-triangle-down
                                    html: state.triangle[1], //The html entity code for either a down or right arrow
                                    on: {
                                        click: function(event){
                                            // making sure that select.node doesn't fire when clicking the open/close toggler
                                            event.preventDefault();
                                            event.stopPropagation();

                                            // display or hide children items and fire the tree.open or tree.close event
                                            self.element.tree('toggle', node);

                                            // toggle classes and html on the triangle, and folder icon
                                            var state = states[node.is_open ? 1 : 0], old = states[node.is_open ? 0 : 1];
                                            triangle.removeClass(old.triangle[0]).addClass(state.triangle[0]).html(state.triangle[1]);

                                            triangle.closest('a').find('.'+old.folder).removeClass(old.folder).addClass(state.folder);
                                        }
                                    }
                                });
                            // prepend the toggler triangle and the folder icon to the title
                            $li.find('.jqtree-title').prepend('<i class="'+state.folder+'"></i> ').prepend(triangle);
                        } else {
                            // prepend the folder icon, and an empty space for the triangle so the indendation is correct
                            $li.find('.jqtree-title').prepend('<i class="icon-folder-close"></i> ').prepend('<i class="icon-triangle-hide"></i>');
                        }

                        // Generates indentation for each list item according to nesting level.
                        for (var i = 0; i < node.level; ++i) {
                            $li.find('.jqtree-title').prepend('<i class="icon-whitespace"></i> ');
                        }
                    }
                };

                return defaults;
        },

        setOptions: function(options){

            this.options = $.extend(true, {}, this.getDefaults(), options);

            return this;
        },

        /* Selects a node while opening all parent nodes, requiring the node to have a 'path' property in its data */
        selectNode: function(node, element){

            // the -1 value is the root node
            var nodes = [-1], state = element.tree('getState');

            // fetch parent node ids from path variable, map the array with parseInt to ensure each array item is an integer
            nodes.push.apply(nodes, node.path.split('/').map(function(item){return parseInt(item, 10)}));

            state.selected_node = node.id; // setting current selected node state to the new node
            state.open_nodes.push.apply(state.open_nodes, nodes); // set the root node, parent nodes and current node as open nodes

            // Using setState instead of selectNode allows selecting a node without firing tree.select
            element.tree('setState', state);
        },

        /**
         * Customizable parse data method
         *
         * Customize this method if the structure is a bit non-standard, like DOClink in DOCman
         * or if you need to wrap all nodes in a root node, like the DOCman documents view categories sidebar tree.
         *
         * The following code sample shows how to create a root node like seen in DOCman:
         * return [{
         *      label: 'All Categories',
         *      id: -1, //negative 1 used as jqTree isn't optimized to deal with zero integer ids, methods like selectNode fail
         *      children: this._parseData(list)
         *  }];
         */
        parseData: function(list){
            return this._parseData(list);
        },

        /**
         * Internal parse data method
         *
         * Only customize this method if you know what you're doing and if it's impossible to control the data format
         * that's passed to the script during initialization.
         */
        /* Parses a flat list with parent ids into an hierarchial object structure, reused by Doclink.Tree in doclink.js */
        _parseData: function(list){

            var data = [], index = {}, // 'data' is an hierarchial list while 'index' is flat and used to lookup parents
                offset = false; // the level offset, used to handle cases where the top node got a higher 'level' value than 0 to correct the indentation

            $.each(list, function(key,item){

                index[item.id] = item; // always add the item to the lookup index
                var parent = item.parent_node || item.parent; // complex trees have non-integer ids stored in parent_node

                //Get the offset from the first node, most of the time the offset is zero
                if(offset === false) {
                    offset = parseInt(item.level, 10) - 1;
                }
                //Only run this math when the offset is bigger than zero
                if(offset > 0) {
                    item.level = Math.max(item.level - offset, 0);
                }

                if(item.parent == 0 || !index.hasOwnProperty(parent)) {
                    data.push(item); // top level items are added directly to the new list or if orphan
                } else {
                    if(!index[parent].hasOwnProperty('children')) index[parent].children = [];
                    // changing items in 'index' changes the items in 'data' as they're not deep cloned
                    index[parent].children.push(item);
                }
            });

            //Return the data inside a 'root' node to replicate legacy mootree behavior
            return data;
        },

        // create a params object from a querystring
        unserialize: function (query) {
            var pair, params = {};
            query = query.replace(/^\?/, '').split(/&/);
            for (pair in query) {
                if(query.hasOwnProperty(pair)) {
                    pair = query[pair].split('=');
                    params[decodeURIComponent(pair[0])] = decodeURIComponent(pair[1]);
                }
            }
            return params;
        },

        /**
         * Internal event handlers for customizing the jqTree markup and behavior to integrate better with bootstrap.
         *
         * DO NOT Customize this method to add your own events, use attachHandlers instead
         */
        _attachHandlers: function(){

            this.element.bind({
                'tree.select': // The select event happens when a node is clicked
                    function(event) {
                        if(event.node) { // When event.node is null, it's actually a deselect event
                            //Style the clicked element
                            $(this).find('.active').removeClass('active').find('[class^=icon-folder]').removeClass('icon-white');
                            $(this).find('.jqtree-selected').addClass('active').find('[class^=icon-folder]').addClass('icon-white');
                        }
                    },
                'tree.init':
                    function() {
                        // .sidebar-nav needed for bootstrap styling to apply
                        $(this).find('ul.jqtree-tree').addClass('sidebar-nav');
                    },
                'tree.refresh': //Refreshes reset the html, and happen on events like setState
                    function() {
                        $(this).find('ul.jqtree-tree').addClass('sidebar-nav'); // .sidebar-nav needed for bootstrap styling to apply
                        $(this).find('.jqtree-selected').addClass('active').find('[class^=icon-folder]').addClass('icon-white');
                    }
            });

        },

        /**
         * Attach event handlers for jQtree events for behaviors and interactivity
         *
         * @link http://mbraak.github.io/jqTree/#events
         */
        attachHandlers: function(){

            this._attachHandlers(); // @NOTE Attach needed events, remember to call this if you customize this method

        }
    });
}(window.jQuery));