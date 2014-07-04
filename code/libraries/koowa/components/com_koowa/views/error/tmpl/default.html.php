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

<script type="text/javascript">

    /*! apollo.js v1.7.0 | (c) 2014 @toddmotto | https://github.com/toddmotto/apollo */
    !function(n,t){"function"==typeof define&&define.amd?define(t):"object"==typeof exports?module.exports=t:n.apollo=t()}(this,function(){"use strict";var n,t,s,e,o={},c=function(n,t){"[object Array]"!==Object.prototype.toString.call(n)&&(n=n.split(" "));for(var s=0;s<n.length;s++)t(n[s],s)};return"classList"in document.documentElement?(n=function(n,t){return n.classList.contains(t)},t=function(n,t){n.classList.add(t)},s=function(n,t){n.classList.remove(t)},e=function(n,t){n.classList.toggle(t)}):(n=function(n,t){return new RegExp("(^|\\s)"+t+"(\\s|$)").test(n.className)},t=function(t,s){n(t,s)||(t.className+=(t.className?" ":"")+s)},s=function(t,s){n(t,s)&&(t.className=t.className.replace(new RegExp("(^|\\s)*"+s+"(\\s|$)*","g"),""))},e=function(e,o){(n(e,o)?s:t)(e,o)}),o.hasClass=function(t,s){return n(t,s)},o.addClass=function(n,s){c(s,function(s){t(n,s)})},o.removeClass=function(n,t){c(t,function(t){s(n,t)})},o.toggleClass=function(n,t){c(t,function(t){e(n,t)})},o});

    // Disabling bootstrap with JS for now
    document.styleSheets[0].disabled = true;

    // Get all elements
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

    function callback () {
        var trace = document.getElementById('trace_element');
        var viewportOffset1 = trace.getBoundingClientRect();
        var top = viewportOffset1.top;
        var error = document.getElementById('codes_container');
        var viewportOffset2 = error.getBoundingClientRect();
        var reset = viewportOffset2.top;

        var tracewidth = document.getElementById('trace_container').offsetWidth;

        var source0 = document.getElementById('source0');
        var viewportOffset3 = source0.getBoundingClientRect();
        var top3 = viewportOffset3.top;


        // add remove sticky class
        if ( reset >= 0 ) {
            apollo.removeClass(trace, 'sticky');
            trace.style.width = tracewidth + "px";
        } else if ( top <= 0) {
            apollo.addClass(trace, 'sticky');
        } else {
            console.log(top);
        }

        // set width
        if ( apollo.hasClass(trace, 'sticky') ) {
            trace.style.width = tracewidth + "px";
        }






        var tester = getElementsStartsWithId('source');

        var i=0;

        tester.forEach(function(entry, i) {

            var entrynumber = entry.id;
            var currentnumber = entrynumber.replace('source', '');

            //console.log(currentnumber);

            var target = 'trace__item--' + currentnumber;

            var targetprev = 'trace__item--' + [i==0?tester.length-1:i-1];
            var targetnext = 'trace__item--' + [i==tester.length-1?0:i+1];

            var realtarget = document.getElementById(target);
            var realtargetprev = document.getElementById(targetprev);
            var realtargetnext = document.getElementById(targetnext);

            //console.log(realtarget);


            //console.log(realtarget);

            var item = entry.getBoundingClientRect();
            var item2 = item.top;


            if ( item2 <= 0 ) {
                apollo.addClass(realtarget, 'active');
                apollo.removeClass(realtargetprev, 'active');
                apollo.removeClass(realtargetnext, 'active');
            } else {
                apollo.removeClass(realtarget, 'active');
            }




            // Scroller dic scrolling
            var elemtop = realtarget.offsetTop;
            var elemheight = realtarget.offsetHeight;
            var scrolleroffset = trace.scrollTop;
            var windowheight = Math.max(document.documentElement.clientHeight, window.innerHeight || 0)


            if ( elemtop+elemheight >= windowheight+scrolleroffset && apollo.hasClass(realtarget, 'active') ) {
                trace.scrollTop=elemtop-(windowheight/2);
            } else if ( elemtop < scrolleroffset && apollo.hasClass(realtarget, 'active') ) {
                console.log(realtarget);
            }



            realtarget = null;
            i+=1;
        });



    }






    function clicker() {
        var traceclicks = getElementsStartsWithId('trace__item');

        var a=0;

        traceclicks.forEach(function(entry) {

            var number = a;

            entry.onclick = function() {

                var me = entry.id;

                document.location='#source'+number;return false;
            }

            a+=1;
        });
    }







    window.onload = function() {
        callback();
        clicker();
    }

    window.onscroll = function() {
        callback();
    }

    window.onresize = function() {
        callback();
    }


//    var handler = window;
//
//
//     if (window.addEventListener) {
//     addEventListener('load', handler, false);
//     addEventListener('scroll', handler, false);
//     addEventListener('resize', handler, false);
//     } else if (window.attachEvent)  {
//     attachEvent('onload', handler);
//     attachEvent('onscroll', handler);
//     attachEvent('onresize', handler);
//     }


//    document.documentElement.className = document.documentElement.className + ' js';
//    function toggleElement(elem)
//    {
//        var disp;
//        elem = document.getElementById(elem);
//
//        if (elem.style && elem.style['display']) {
//            // Only works with the "style" attr
//            disp = elem.style['display'];
//        }
//        else if (elem.currentStyle) {
//            // For MSIE, naturally
//            disp = elem.currentStyle['display'];
//        }
//        else if (window.getComputedStyle) {
//            // For most other browsers
//            disp = document.defaultView.getComputedStyle(elem, null).getPropertyValue('display');
//        }
//
//        // Toggle the state of the "display" style
//        elem.style.display = disp == 'block' ? 'none' : 'block';
//        return false;
//    }
</script>



<style type="text/css">
    /* New */
    body {
        margin: 0;
        padding: 0;
        font-family: Helvetica, Arial, sans-serif;
        background: #E2E2E2;
    }

    .sticky {
        position: fixed;
        top: 0;
        left: 0;
        height: 100%;
        overflow: auto;
    }

    body * {
        -webkit-box-sizing: border-box;
        -moz-box-sizing: border-box;
        box-sizing: border-box;
    }

    .header {
        margin: 0;
        padding: 15px;
        background: #DA2121;
        color: #fff;
        font-size: 1.5em;
        font-weight: normal;
    }
    .header span {
        float: right;
    }

    .message {
        background: #3F3F3F;
        color: #dddddd;
        padding: 15px;
    }

    .additional_data {
        float: right;
    }

    .message,
    .the_error__header {
        font-family: Monaco,Menlo,Consolas,"Courier New",monospace;
        font-size: 14px;
        font-weight: normal;
    }

    .the_error .the_error__header  {
        background: #1488cc;
        color: #fff;
        padding: 10px 15px;
    }

    .linenumber {
        color: #FFF83D;
    }

    .trace {
        margin: 0;
        padding: 0;
        list-style: decimal;
    }

    pre.source { overflow: auto; word-wrap: break-word; margin: 0; padding: 15px 0; background: #061F27; border: none; line-height: 1.5em; color: #E1DCC7; border-radius: 0; }
    pre.source span.line { display: block; padding: 3px 15px; }
    pre.source span.highlight { background: #0B374C; }
    pre.source span.line span.number { color: #2e76c5; }

    .trace, .codes {
        display: block;
        float: left;
        margin: 0;
    }

    .trace {
        width: 33%;
        min-height: 10px;
        overflow: auto;
        word-wrap: break-word;
        list-style: none;
        background: #E2E2E2;
    }

    .trace .trace__item {
        counter-increment: trace-counter -1;
        position: relative;
        background: #fff;
        margin-bottom: 1px;
        padding: 15px;
        cursor: pointer;
        -webkit-transition: all .05s ease-in-out;
           -moz-transition: all .05s ease-in-out;
            -ms-transition: all .05s ease-in-out;
             -o-transition: all .05s ease-in-out;
                transition: all .05s ease-in-out;
    }

    .trace__item:hover {
        background: #FFF83D;
    }

    .trace__item.active {
        background: #2089C9;
        color: #fff;
    }

    .trace .trace__item:before {
        content: counter(trace-counter);
        position: absolute;
        top: 10px;
        right: 10px;
        font-size: 12px;
    }

    .codes {
        width: 67%;
        padding: 20px;
        background: #F3F3F3;
    }
</style>


<div class="error_container">

    <h1 class="header">
        <?= $exception ?> <span>[<?= $code ?>]</span>
    </h1>
    <div class="message">
        <?= $message ?>
        <div class="additional_data">Show additional data</div>
    </div>

    <div class="content">

        <div class="the_error">
            <div class="the_error__header">
                <span class="file"><?= $file ?></span>:<span class="linenumber"><?= $line ?></span>
            </div>
            <div class="the_error_code">
                <?= @helper('debug.source', array('file' => $file, 'line' => $line)) ?>
            </div>
        </div>

        <?php foreach (@helper('debug.trace', array('trace' => $trace)) as $i => $step): endforeach; ?>

        <div id="trace_container" class="trace" style="counter-reset: trace-counter <?php echo $i+2; ?>">
            <div id="trace_element">
                <?php foreach (@helper('debug.trace', array('trace' => $trace)) as $i => $step): ?>
                    <div id="trace__item--<?= $i; ?>" class="trace__item">
                        <p>
                            <strong>
                                <?= $step['function'] ?>(<?php if ($step['args']): $args_id = 'args'.$i; ?><?php endif ?>)
                            </strong>
                            <span class="file">
                            <?php if ($step['file']): $source_id = 'source'.$i; ?>
                                <?= @helper('debug.path', array('file' => $step['file'])) ?>:<?= $step['line'] ?>
                            <?php else: ?>
                                {<?= 'PHP internal call' ?>}
                            <?php endif ?>
                        </span>
                        </p>
                    </div>
                    <?php unset($args_id, $source_id); ?>
                <?php endforeach ?>
            </div>
        </div>

        <div id="codes_container" class="codes">
            <?php foreach (@helper('debug.trace', array('trace' => $trace)) as $i => $step): ?>
            <?php if ($step['file']): $source_id = 'source'.$i; ?>
            <div id="<?= $source_id ?>" class="the_error">

                <strong>
                    <?= $step['function'] ?>(<?php if ($step['args']): $args_id = 'args'.$i; ?><?php endif ?>)
                </strong>


                <div class="the_error__header">
                    <span class="file"><?= @helper('debug.path', array('file' => $step['file'])) ?></span>:<span class="linenumber"><?= $step['line'] ?></span>
                </div>
                <?php if (isset($source_id)): ?>
                    <pre class="source collapsed"><code><?= $step['source'] ?></code></pre>
                <?php endif ?>
            </div>
            <?php else: ?>
                {<?= 'PHP internal call' ?>}
            <?php endif ?>


            <?php if ($step['args']): $args_id = 'args'.$i; ?><?php endif ?>
            <?php if (isset($args_id)): ?>
                <div id="<?= $args_id ?>" class="collapsed">
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
            <hr />

            <?php unset($args_id, $source_id); ?>
            <?php endforeach ?>

        </div>


    </div>
</div>