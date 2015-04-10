/**
 * Koowa jqTree wrapper
 *
 * Customized instance of jqTree to render a list of categories in a tree structure.
 * It deals with turning a flat list into a hierarchy structure that jqTree understands.
 *
 * @copyright	Copyright (C) 2007 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		https://github.com/nooku/nooku-framework for the canonical source repository
 * @requires    Koowa.Class, jqTree plugin
 */

(function($){

    Koowa.Tree = Koowa.Class.extend({

        initialize: function(element, options){

            this.setOptions(options);

            this.element = $(element);

            if(this.options.onBeforeInitialize) this.options.onBeforeInitialize.call(this);

            // For scrollTo to work, needs to be position:relative;
            $(window).load($.proxy(function(){
                if(this.element.css('position') != 'relative') {
                    this.element.css('position', 'relative');
                    //Warn that a css optimization should be done
                    //if(window.console) console.warn('The element:', this.element, 'should have position:relative applied by css');
                }
            }, this));

            this.attachHandlers();

            if(this.options.data && this.options.data.length) this.options.data = this.parseData(this.options.data);

            // Shortcut for accessing tree apis, like this: selected = this.tree('getSelectedNode')
            this.tree = $.proxy(this.element.tree, this.element);

            // Instantiate the jqTree plugin with the parsed options
            this.tree(this.options);

            if(this.options.onAfterInitialize) this.options.onAfterInitialize.call(this);
        },

        getDefaults: function(){

            var self = this,
                defaults = {
                    selected: null, //The node id of a selected node on init, if any
                    data: [], //Default empty value to avoid errors when there are no items yet
                    autoOpen: 0, //Auto open just "All Categories" by default, this value is the nesting level not the node id
                    useContextMenu: false, //This allows us to right-click menu items again
                    toggler: [{triangle: ['icon-triangle-right', '&#x25ba;'], folder: 'koowa_icon--folder'},//Styling options for toggler
                              {triangle: ['icon-triangle-down', '&#x25bc;'], folder: 'koowa_icon--folder_open'}],
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
                            /**
                             * Using javascript:void() instead of # to avoid issues with url routers using # as Root node
                             */
                            href: 'javascript:void(0)',
                            on: {
                                click: function(event){
                                    event.preventDefault(); //Prevent '#' added to the url, causing a scroll to top
                                    event.stopPropagation(); //Prevent bubbling up to the click handler in jqTree

                                    // Pass null to deselect, selectNode API never deselects when passing a node
                                    self.tree('selectNode', node);
                                }
                            }
                        }));

                        if(node.isFolder()) {
                            // states variable is for easy toggling on the click event
                            var states = self.options.toggler,
                                state = states[node.is_open ? 1 : 0],
                                text = 'folder' + (node.is_open ? '' : ' open'),
                                triangle = $('<i />', {
                                    'class': 'icon-toggler '+state.triangle[0], //Either icon-triangle-right or icon-triangle-down
                                    html: state.triangle[1], //The html entity code for either a down or right arrow
                                    on: {
                                        click: function(event){
                                            // making sure that select.node doesn't fire when clicking the open/close toggler
                                            event.preventDefault();
                                            event.stopPropagation();

                                            // display or hide children items and fire the tree.open or tree.close event
                                            self.element.tree('toggle', node);
                                        }
                                    }
                                });
                            // prepend the toggler triangle and the folder icon to the title
                            $li.find('.jqtree-title').prepend('<span class="'+state.folder+'"><i>'+text+'</i></span> ').prepend(triangle);
                        } else {
                            // prepend the folder icon, and an empty space for the triangle so the indendation is correct
                            $li.find('.jqtree-title').prepend('<span class="'+self.options.toggler[0].folder+'"><i>folder</i></span> ').prepend('<i class="icon-triangle-hide"></i>');
                        }

                        /**
                         * Generates indentation for each list item according to nesting level.
                         * @TODO the node.getLevel() property lookup isn't cached, submit patch on the jqTree github
                         */
                        var level = node.getLevel();
                        for (var i = 1; i < level; ++i) {
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
         * Customize this method if the structure is a bit non-standard
         * or if you need to wrap all nodes in a root node
         *
         * The following code sample shows how to create a root node:
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
         *
         * @param list array
         *
         * The data objects in the list are required to have the following properties:
         * @property string|integer id          Required to be unique, integer or string is optional
         * @property string|integer parent      Integer or string is optional, zero when no parent
         * @property string         path        Containing parent ids descending left to right separated by '/'
         * @property string         label       The text to be displayed
         *
         * Nodes can contain any custom data properties passed to it, except the following reserved properties:
         * @property array      children
         * @property Element    element
         * @property string     name
         * @property Node       tree
         *
         * Simple data sample with unique ids:
         *  [
         *      {id: 1, parent: 0, path: '', label: 'Blog'},
         *      {id: 2, parent: 1, path: '1', label: 'News'},
         *      {id: 3, parent: 2, path: '1/2', label: 'Nooku Code Jam'},
         *      {id: 4, parent: 2, path: '1/2', label: 'Nooku Framework'},
         *      {id: 5, parent: 0, path: '', label: 'Tutorials'}
         *  ]
         *
         *  Advanced data sample with non-unique ids:
         *  [
         *      {id: 's1', section_id: 1, parent: 0, path: '', label: 'Blog'},
         *      {id: 's1c1', section_id: 1, category_id: 1, parent: 's1', path: 's1', label: 'News'},
         *      {id: 's1c2', section_id: 1, category_id: 2, parent: 's1c1', path: 's1/s1c1', label: 'Nooku Code Jam'},
         *      {id: 's1c3', section_id: 1, category_id: 3, parent: 's1c1', path: 's1/s1c1', label: 'Nooku Framework'},
         *      {id: 's2', section_id: 2, parent: 0, path: '', label: 'Tutorials'}
         *  ]
         */
        _parseData: function(list){

            var data = [], index = {}; // 'data' is an hierarchial list while 'index' is flat and used to lookup parents

            $.each(list, function(key,item){

                index[item.id] = item; // always add the item to the lookup index

                if(item.parent == 0 || !index.hasOwnProperty(item.parent)) {
                    data.push(item); // top level items are added directly to the new list or if orphan
                } else {
                    if(!index[item.parent].hasOwnProperty('children')) index[item.parent].children = [];
                    // changing items in 'index' changes the items in 'data' as they're not deep cloned
                    index[item.parent].children.push(item);
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

            var options = this.options, self = this, states = options.toggler;

            this.element.bind({
                'tree.select': // The select event happens when a node is clicked
                    function(event) {
                        $(this).find('.active').removeClass('active');
                        if(event.node) { // When event.node is null, it's actually a deselect event
                            //Style the clicked element
                            $(this).find('.jqtree-selected').addClass('active');
                        }
                    },
                'tree.open':
                    function(event) {
                        // toggle classes and html on the triangle, and folder icon
                        var node = event.node, state = states[1], old = states[0], triangle = $(node.element).children('a').find('.icon-toggler');
                        triangle.removeClass(old.triangle[0]).addClass(state.triangle[0]).html(state.triangle[1]);

                        triangle.closest('a').find('.'+old.folder).removeClass(old.folder).addClass(state.folder);
                    },
                'tree.close':
                    function(event) {
                        // toggle classes and html on the triangle, and folder icon
                        var node = event.node, state = states[0], old = states[1], triangle = $(node.element).children('a').find('.icon-toggler');
                        triangle.removeClass(old.triangle[0]).addClass(state.triangle[0]).html(state.triangle[1]);

                        triangle.closest('a').find('.'+old.folder).removeClass(old.folder).addClass(state.folder);
                    },
                'tree.init':
                    function() {
                        // .sidebar-nav needed for bootstrap styling to apply
                        $(this).find('ul.jqtree-tree').addClass('sidebar-nav');

                        // If a node should be preselected on init, select it right away
                        if(options.selected) {
                            var node = $(this).tree('getNodeById', options.selected);
                            $(this).tree('selectNode', node);
                            self.selectNode(node, $(this));
                        }
                    },
                'tree.refresh': //Refreshes reset the html, and happen on events like setState
                    function() {
                        $(this).find('ul.jqtree-tree').addClass('sidebar-nav'); // .sidebar-nav needed for bootstrap styling to apply
                        $(this).find('.jqtree-selected').addClass('active');
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

        },
        /**
         * Scroll the node and its children into view with a scroll transition
         * @param node      Node instance
         * @param viewport  HTMLElement with the scroll overflow, usually this.element
         * @param duration  Duration of animation, defaults to 900ms
         */
        scrollIntoView: function(node, viewport, duration){
            var element = $(node.element),
                viewportHeight = viewport.height(),
                viewportWidth = viewport.width(),
                offsetTop = element[0].offsetTop,
                height = element.height(),
                width = element.width(),
                scrollTo = Math.min(offsetTop, (offsetTop - viewportHeight) + height),
                animate = {};

            /**
             * Only scrolls if the element is out of bounds either from the top or bottom, with priority on the bottom
             */
            if(scrollTo > viewport.scrollTop()) {
                animate.scrollTop = scrollTo;
            } else if (offsetTop < viewport.scrollTop()) {
                animate.scrollTop = offsetTop;
            }

            /**
             * Support animating a horizontal scrollbar, if necessary
             */
            if(viewportWidth < width) {
                var title = element.find('.jqtree-title').filter(":first"),
                    whitespace = title.find('.icon-whitespace').last(),
                    scrollToLeft = whitespace.length ? whitespace[0].offsetLeft : 0;

                animate.scrollLeft = scrollToLeft;
            }

            if(animate.scrollTop || animate.scrollLeft) {
                clearTimeout(this._scroll_into_view);
                this._scroll_into_view = setTimeout(function(){
                    viewport.animate(animate, duration||900);
                }.bind(this), duration||900);

            }
        }
    });
}(window.kQuery));