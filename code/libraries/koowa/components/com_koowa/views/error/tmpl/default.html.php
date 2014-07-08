<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2007 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa for the canonical source repository
 */

defined('KOOWA') or die; ?>

<title content="replace"><?= @translate('Error').' '.$code.' - '. KHttpResponse::$status_messages[$code];; ?></title>

<style type="text/css">
    /* New */
    body {
        margin: 0;
        padding: 0;
        font-family: Helvetica, Arial, sans-serif;
        background: #E2E2E2;
        color: #444444;
    }

    body * {
        -webkit-box-sizing: border-box;
        -moz-box-sizing: border-box;
        box-sizing: border-box;
    }


    .file strong {
        white-space: nowrap;
    }

    /* Sticky class for sidebar */
    .sticky {
        position: fixed;
        top: 0;
        left: 0;
    }


    /* Page header & message */
    .page_header,
    .page_message {
        display: table;
        width: 100%;
        margin: 0;
        padding: 0;
        font-size: 1em;
        font-weight: normal;
    }

    .page_header__exception,
    .page_header__code,
    .page_message__text,
    .page_message__button {
        display: table-cell;
        padding: 20px;
    }

    .page_header {
        background: #DA2121;
        color: #fff;
        text-shadow: -1px -1px 0 #a81010;
        box-shadow: 0 3px 3px rgba(0,0,0,.2);
        position: relative;
        z-index: 2;
    }
    @media screen and (min-width: 600px) {
        .page_header {
            font-size: 1.5em;
        }
    }

    .page_header__exception,
    .page_message__text {
        word-wrap: break-word;
        word-break: break-all;
    }

    .page_header__code,
    .page_message__button {
        text-align: right;
        padding-left: 0;
    }

    .page_message {
        background: #3F3F3F;
        color: #dddddd;
        text-shadow: -1px -1px 0 #2e2b2b;
        position: relative;
        z-index: 1;
    }

    .page_message__button {
        cursor: pointer;
    }

    .page_message,
    .error_container__header,
    .trace__item p {
        font-family: Monaco,Menlo,Consolas,"Courier New",monospace;
        font-size: 13px;
        font-weight: normal;
        line-height: 1.5em;
        word-wrap: break-word;
    }


    .linenumber {
        color: #FFF83D;
    }

    .trace_container {
        margin: 0;
        padding: 0;
        list-style: decimal;
    }

    pre.source { overflow: auto; word-wrap: break-word; margin: 0 0 20px 0; padding: 15px 0; background: #061F27; border: none; line-height: 1.5em; color: #E1DCC7; border-radius: 0; }
    pre.source span.line { display: block; padding: 3px 15px; }
    pre.source span.highlight { background: #0B374C; }
    pre.source span.line span.number { color: #2e76c5; }

    .trace_container, .codes_container {
        display: block;
        float: left;
        margin: 0;
    }

    .trace_container {
        width: 33%;
        min-height: 10px;
        overflow: auto;
        word-wrap: break-word;
        list-style: none;
        background: #E2E2E2;
    }

    .trace_container .trace__item {
        counter-increment: trace-counter -1;
        position: relative;
        background: #fff;
        color: #444;
        margin-bottom: 1px;
        padding: 20px;
        cursor: pointer;
        line-height: 1.5em;
        -webkit-transition: all .05s ease-in-out;
        -moz-transition: all .05s ease-in-out;
        -ms-transition: all .05s ease-in-out;
        -o-transition: all .05s ease-in-out;
        transition: all .05s ease-in-out;
    }

    .trace__item strong {
        color: #1488CC;
        font-weight: bold;
    }

    .trace__item p {
        margin: 0;
    }

    .trace__item h3 {
        color: #1488CC;
        font-weight: bold;
        margin: 0 0 5px 0;
        font-size: 16px;
    }

    .trace__item.active_trace_item strong,
    .trace__item.active_trace_item h3 {
        color: #FFF83D;
    }

    .trace__item:hover {
        background: #FFF83D;
    }

    .trace__item.active_trace_item {
        background: #2089C9;
        color: #fff;
    }

    .error_container {
        padding: 20px;
        border-top: 1px solid transparent;
        border-bottom: 1px solid transparent;
        position: relative;
        word-wrap: break-word;
        -webkit-transition: all .05s ease-in-out;
        -moz-transition: all .05s ease-in-out;
        -ms-transition: all .05s ease-in-out;
        -o-transition: all .05s ease-in-out;
        transition: all .05s ease-in-out;
    }


    .error_container.active_source_item {
        background: #fff;
        border-top-color: #ccc;
        border-bottom-color: #ccc;
    }
    .error_container.active_source_item:before {
        display: block;
        content: " ";
        position: absolute;
        width: 4px;
        top: -1px;
        bottom: -1px;
        left: -1px;
        background: #2089C9;
    }

    #the_error .error_container {
        padding: 0;
        border: none;
    }

    #the_error pre.source {
        margin-bottom: 0;;
    }

    .trace_container .trace__item:before {
        content: counter(trace-counter);
        position: absolute;
        top: 8px;
        right: 8px;
        font-size: 12px;
        color: #888;
    }
    .trace_container .trace__item.active_trace_item:before {
        color: #A4D3EE;
    }

    .error_container h3 {
        counter-increment: source-counter -1;
        cursor: pointer;
    }

    .error_container h3:before {
        content: counter(source-counter) ". ";
    }

    .codes_container {
        width: 67%;
        background: #F3F3F3;
        position: relative;
        border-left: 1px solid #ddd;
    }

    h3 {
        margin: 0 0 20px 0;
        padding: 0;
        color: #1488CC;
    }

    h4 {
        margin: 0 0 20px 0;
        padding: 0;
        color: #1488CC;
    }

    table {
        width: 100%;
        border-collapse: collapse;
    }

    table td {
        margin: 1px;
        background: #fff;
        border: 1px solid #F3F3F3;
        padding: 10px;
    }
    table td code {
        color: #a10070;
    }

    table td code, table td pre {
        margin: 0;
    }


.page_data {
    background: #ddd;
    z-index: 10;
    display: none;
    padding: 30px;
    height: auto;
}

.visible {
    display: block !important;
}

.visible pre.source,
.visible div.args {
    display: block !important;
}

.visible .error_container__header {
    background: #1488cc;
    color: #fff;
    padding: 10px 20px;
}

.trace_container {
    display: none;
}
.codes_container {
    width: 100%;
}
pre.source,
div.args {
    display: none;
}

#the_error .error_container .error_container__header  {
    background: #1488cc;
    color: #fff;
    padding: 10px 20px;
}

#the_error pre.source {
    display: block;
}


@media screen and (min-width: 600px) {
    .trace_container {
        display: block;
    }
    .codes_container {
        width: 67%;
    }
    .error_container .error_container__header  {
        background: #1488cc;
        color: #fff;
        padding: 10px 20px;
    }
    pre.source,
    div.args {
        display: block;
    }
    .error_container h3 {
        cursor: default;
    }
}


</style>

<script type="text/javascript">

    // @TODO: fix page so below scripts are not needed anymore
    function removeafterfix() {
        // Disabling bootstrap CSS file with JS for now
        document.styleSheets[0].disabled = true;
        // Deleting end of message with JS for now
        var message = document.getElementsByClassName('page_message__text');
        var temp = message.length;
        // Iterating
        while(temp--) {
            var html = message[temp].innerHTML.split(' in ').shift();
            message[temp].innerHTML = html;
        }
    }

    /*! apollo.js v1.7.0 | (c) 2014 @toddmotto | https://github.com/toddmotto/apollo */
    !function(n,t){"function"==typeof define&&define.amd?define(t):"object"==typeof exports?module.exports=t:n.apollo=t()}(this,function(){"use strict";var n,t,s,e,o={},c=function(n,t){"[object Array]"!==Object.prototype.toString.call(n)&&(n=n.split(" "));for(var s=0;s<n.length;s++)t(n[s],s)};return"classList"in document.documentElement?(n=function(n,t){return n.classList.contains(t)},t=function(n,t){n.classList.add(t)},s=function(n,t){n.classList.remove(t)},e=function(n,t){n.classList.toggle(t)}):(n=function(n,t){return new RegExp("(^|\\s)"+t+"(\\s|$)").test(n.className)},t=function(t,s){n(t,s)||(t.className+=(t.className?" ":"")+s)},s=function(t,s){n(t,s)&&(t.className=t.className.replace(new RegExp("(^|\\s)*"+s+"(\\s|$)*","g"),""))},e=function(e,o){(n(e,o)?s:t)(e,o)}),o.hasClass=function(t,s){return n(t,s)},o.addClass=function(n,s){c(s,function(s){t(n,s)})},o.removeClass=function(n,t){c(t,function(t){s(n,t)})},o.toggleClass=function(n,t){c(t,function(t){e(n,t)})},o});

    // Get all elements with certain ID
    function getElementsStartsWithId( id ) {
        var children = document.body.getElementsByTagName('*');
        var elements = [], child;
        for (var i = 0, length = children.length; i < length; i++) {
            child = children[i];
            if (child.id.substr(0, id.length) == id)
                elements.push(child);
        }
        return elements;
    }

    // Vars
    var stackwidth = 0;
    var scrollamount = 0;

    // Give this a name
    function callback () {

        // Check viewport width
        var viewportwidth = Math.max(document.documentElement.clientWidth, window.innerWidth || 0);

        if ( viewportwidth >= 600 ) {

            // Setting variables
            var trace = document.getElementById('trace_wrapper');
            var top = trace.getBoundingClientRect().top;
            var codes_container_top = document.getElementById('codes_container').getBoundingClientRect().top;
            var codeheight = document.getElementById('codes_container').offsetHeight;
            var currentstackwidth = document.getElementById('trace_container').offsetWidth;
            var source_elements = getElementsStartsWithId('source');
            var i=0;
            var traceheight = trace.offsetHeight;
            var viewportheight = Math.max(document.documentElement.clientHeight, window.innerHeight || 0);


            var divideby = (codeheight+viewportheight)/traceheight;
            var amount = codes_container_top/divideby;
            var amount2 = -(traceheight-viewportheight);

            var relativescroll = viewportheight/3;


            if ( codes_container_top <= -relativescroll && (amount2-(relativescroll)) <= amount ) {

                scrollamount = (codes_container_top/divideby)+(relativescroll);

                if ( scrollamount > 0 ) { scrollamount = 0 }

                trace.style.top = scrollamount + 'px';
            }
            else if ( codes_container_top < -relativescroll ) {

                if ( amount2 > 0 ) { amount2 = 0 }

                trace.style.top = amount2 + 'px';
            }





            // add & remove sticky class
            if ( codes_container_top >= 0 && apollo.hasClass(trace, 'sticky') ) {
                apollo.removeClass(trace, 'sticky');
                trace.style.width = currentstackwidth + "px";

                console.error('Triggering unsticky');

            } else if ( top < 0 && !apollo.hasClass(trace,'sticky') ) {
                apollo.addClass(trace, 'sticky');

                console.error('Triggering sticky');
            }

            // keep width on resize if trace stack is sticky
            if ( currentstackwidth != stackwidth ) {
                trace.style.width = currentstackwidth + "px";
                stackwidth = document.getElementById('trace_container').offsetWidth;

                console.error('Triggering adjust width');
            }

            // Iterate through each source_element
            source_elements.forEach(function(entry, i) {

                // Settings vars
                var target = 'trace__item--' + i;
                var realtarget = document.getElementById(target);
                var itemtop = entry.getBoundingClientRect().top;
                var itemheight = entry.offsetHeight;
                var elemtop = realtarget.offsetTop;
                var elemheight = realtarget.offsetHeight;
                var scrolleroffset = trace.scrollTop;
                var windowheight = Math.max(document.documentElement.clientHeight, window.innerHeight || 0);

                // Add & remove active classes
                if ( itemtop < relativescroll && itemtop+itemheight > scrolleroffset+relativescroll && !apollo.hasClass(entry, 'active_source_item') ) {

                    var allsources = document.getElementsByClassName('active_source_item');
                    var as = allsources.length;
                    // Iterating
                    while(as--) {
                        // Setting vars
                        var target = document.getElementById(allsources[as].id);
                        apollo.removeClass(target, 'active_source_item');
                    }

                    var alltraces = document.getElementsByClassName('active_trace_item');
                    var as = alltraces.length;
                    // Iterating
                    while(as--) {
                        // Setting vars
                        var target = document.getElementById(alltraces[as].id);
                        apollo.removeClass(target, 'active_trace_item');
                    }

                    apollo.addClass(entry, 'active_source_item');
                    apollo.addClass(realtarget, 'active_trace_item');

                    console.error(realtarget, 'Triggering classedit');

                } else if ( itemtop > relativescroll && apollo.hasClass(entry, 'active_source_item') ) {
                    apollo.removeClass(entry, 'active_source_item');
                    apollo.removeClass(realtarget, 'active_trace_item');

                    console.error('triggering finalclass');
                }

                i+=1;
            });
        }
    }


    // On clicking active elements
    function clicker() {
        var traceclicks = getElementsStartsWithId('trace__item');

        traceclicks.forEach(function(entry, num) {

            entry.onclick = function() {

                var no1 = 'source' + num;

                var amount = document.getElementById(no1);

                amount.scrollIntoView(true);

            }
        });
    }

    // On clicking active elements
    function page_data() {
        document.getElementById('page_data__button').onclick = function() {

            apollo.toggleClass(document.getElementById('page_data'), 'visible');

        }
    }


    // More clicking
    function click_on_units() {
        var source_elements = getElementsStartsWithId('source');

        source_elements.forEach(function(entry, num) {

            entry.onclick = function() {

                apollo.toggleClass(entry, 'visible');

            }
        });
    }

    window.onload = function() {
        callback();
        clicker();
        page_data();
        click_on_units();
        removeafterfix();
    }

    window.onscroll = function() {
        callback();
    }

    window.onresize = function() {
        callback();
    }

</script>

<div class="error_page">

    <h1 class="page_header">
        <span class="page_header__exception"><?= $exception ?></span>
        <span class="page_header__code">[<?= $code ?>]</span>
    </h1>
    <div class="page_message">
        <div class="page_message__text"><?= $message ?></div>
        <div class="page_message__button" id="page_data__button">Show page data</div>
    </div>

    <div id="page_data" class="page_data">
        <h2>Server/Request Data</h2>
        <table class="data-table">
            <thead>
                <tr>
                    <td class="data-table-k">Key</td>
                    <td class="data-table-v">Value</td>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>DOCUMENT_ROOT</td>
                    <td>/demo/dev/whoops/examples</td>
                </tr>
                <tr>
                    <td>REMOTE_ADDR</td>
                    <td>127.0.0.1</td>
                </tr>
                <tr>
                    <td>REMOTE_PORT</td>
                    <td>42317</td>
                </tr>
                <tr>
                    <td>SERVER_SOFTWARE</td>
                    <td>PHP 5.4.6-1ubuntu1.2 Development Server</td>
                </tr>
                <tr>
                    <td>SERVER_PROTOCOL</td>
                    <td>HTTP/1.1</td>
                </tr>
                <tr>
                    <td>SERVER_NAME</td>
                    <td>localhost</td>
                </tr>
                <tr>
                    <td>SERVER_PORT</td>
                    <td>8080</td>
                </tr>
                <tr>
                    <td>REQUEST_URI</td>
                    <td>/example-silex.php</td>
                </tr>
                <tr>
                    <td>REQUEST_METHOD</td>
                    <td>GET</td>
                </tr>
                <tr>
                    <td>SCRIPT_NAME</td>
                    <td>/example-silex.php</td>
                </tr>
                <tr>
                    <td>SCRIPT_FILENAME</td>
                    <td>/demo/dev/whoops/examples/example-silex.php</td>
                </tr>
                <tr>
                    <td>PHP_SELF</td>
                    <td>/example-silex.php</td>
                </tr>
                <tr>
                    <td>HTTP_HOST</td>
                    <td>localhost:8080</td>
                </tr>
                <tr>
                    <td>HTTP_CONNECTION</td>
                    <td>keep-alive</td>
                </tr>
                <tr>
                    <td>HTTP_ACCEPT</td>
                    <td>text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8</td>
                </tr>
                <tr>
                    <td>HTTP_USER_AGENT</td>
                    <td>Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.22 (KHTML, like Gecko) Ubuntu Chromium/25.0.1364.160
                        Chrome/25.0.1364.160 Safari/537.22
                    </td>
                </tr>
                <tr>
                    <td>HTTP_ACCEPT_ENCODING</td>
                    <td>gzip,deflate,sdch</td>
                </tr>
                <tr>
                    <td>HTTP_ACCEPT_LANGUAGE</td>
                    <td>en-US,en;q=0.8</td>
                </tr>
                <tr>
                    <td>HTTP_ACCEPT_CHARSET</td>
                    <td>ISO-8859-1,utf-8;q=0.7,*;q=0.3</td>
                </tr>
                <tr>
                    <td>REQUEST_TIME_FLOAT</td>
                    <td>1365585072.0011</td>
                </tr>
                <tr>
                    <td>REQUEST_TIME</td>
                    <td>1365585072</td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="page_content">

        <div id="the_error">
            <div class="error_container">
                <div class="error_container__header">
                    <span class="file"><?= $file ?></span>:<span class="linenumber"><?= $line ?></span>
                </div>
                <div class="error_container_code">
                    <?= @helper('debug.source', array('file' => $file, 'line' => $line)) ?>
                </div>
            </div>
        </div>

        <?php foreach (@helper('debug.trace', array('trace' => $trace)) as $num => $step): endforeach; // Getting the total amount ?>

        <div id="trace_container" class="trace_container" style="counter-reset: trace-counter <?php echo $num+2; ?>">
            <div id="trace_wrapper">
                <?php foreach (@helper('debug.trace', array('trace' => $trace)) as $i => $step): ?>
                <div id="trace__item--<?= $i; ?>" class="trace__item">
                    <h3>
                        <?= $step['function'] ?>(<?php if ($step['args']): $args_id = 'args'.$i; ?><?php endif ?>)
                    </h3>
                    <p class="file">
                        <?php if ($step['file']): $source_id = 'source'.$i; ?>
                            <?= @helper('debug.path', array('file' => $step['file'])) ?>:<strong><?= $step['line'] ?></strong>
                        <?php else: ?>
                            {<?= 'PHP internal call' ?>}
                        <?php endif ?>
                    </p>
                </div>
                <?php unset($args_id, $source_id); ?>
                <?php endforeach ?>
            </div>
        </div>

        <div id="codes_container" class="codes_container" style="counter-reset: source-counter <?php echo $num+2; ?>">
            <?php foreach (@helper('debug.trace', array('trace' => $trace)) as $i => $step): ?>
            <?php if ($step['file']): $source_id = 'source'.$i; ?>
            <div id="<?= $source_id ?>" class="error_container">
                <h3>
                    <?= $step['function'] ?>(<?php if ($step['args']): $args_id = 'args'.$i; ?><?php endif ?>)
                </h3>
                <div class="error_container__header">
                    <span class="file"><?= @helper('debug.path', array('file' => $step['file'])) ?></span>:<span class="linenumber"><?= $step['line'] ?></span>
                </div>
                <?php if (isset($source_id)): ?>
                <div class="error_container__code">
                    <pre class="source"><code><?= $step['source'] ?></code></pre>
                </div>
                <?php endif ?>

                <?php if ($step['args']): $args_id = 'args'.$i; ?><?php endif ?>
                <?php if (isset($args_id)): ?>
                <div class="args" id="<?= $args_id ?>">
                    <h4>Arguments</h4>
                    <table cellspacing="0">
                        <?php foreach ($step['args'] as $name => $arg): ?>
                            <tr>
                                <td><code><?= $name ?></code></td>
                                <td><pre><?= @helper('debug.dump', array('value' => $arg)) ?></pre></td>
                            </tr>
                        <?php endforeach ?>
                    </table>
                </div>
                <?php endif ?>
            </div>
            <?php else: ?>
            {<?= 'PHP internal call' ?>}
            <?php endif ?>
            <?php unset($args_id, $source_id); ?>
            <?php endforeach ?>
        </div>
    </div>
</div>