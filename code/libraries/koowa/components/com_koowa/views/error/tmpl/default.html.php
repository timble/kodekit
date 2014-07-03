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
    }

    .error * {
        -webkit-box-sizing: border-box;
        -moz-box-sizing: border-box;
        box-sizing: border-box;
    }

    .error .header {
        margin: 0;
        padding: 15px;
        background: #DA2121;
        color: #fff;
        font-size: 1.5em;
        font-weight: normal;
    }
    .error .header span {
        float: right;
    }

    .error .message {
        background: #3F3F3F;
        color: #dddddd;
        padding: 15px;
    }

    .error .additional_data {
        float: right;
    }

    .error .message,
    .error .the_error__header {
        font-family: Monaco,Menlo,Consolas,"Courier New",monospace;
        font-size: 14px;
        font-weight: normal;
    }

    .the_error .the_error__header  {
        background: #1488cc;
        color: #fff;
        padding: 10px 15px;
    }

    .error .linenumber {
        color: #FFF83D;
    }

    .error .trace {
        margin: 0;
        padding: 0;
        list-style: decimal;
    }

    .error pre.source { margin: 0; padding: 15px 0; background: #061F27; border: none; line-height: 1.5em; color: #E1DCC7; border-radius: 0; }
    .error pre.source span.line { display: block; padding: 3px 15px; }
    .error pre.source span.highlight { background: #0B374C; }
    .error pre.source span.line span.number { color: #2e76c5; }

    .trace, .codes {
        display: block;
        float: left;
        margin: 0;
    }

    .trace {
        width: 33%;
        overflow: auto;
        word-wrap: break-word;
        background: #fff;
    }

    .codes {
        width: 67%;
        padding: 20px;
        background: #F3F3F3;
    }

    /* Old */
    .error { background: #ddd; font-size: 1em; text-align: left; color: #111; }
    .error h2 { background: #222; }
    .error h3 { margin: 0; padding: 0.4em 0 0; font-size: 1em; font-weight: normal; }
    .error p { margin: 0; padding: 0.2em 0; }
    .error a { color: #1b323b; }
    .error pre { overflow: auto; white-space: pre-wrap; }
    .error table { width: 100%; display: block; margin: 0 0 0.4em; padding: 0; border-collapse: collapse; background: #fff; }
    .error table td { border: solid 1px #ddd; text-align: left; vertical-align: top; padding: 0.4em; }

    .js .collapsed { display: none; }
</style>


<div class="error">

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


        <ol class="trace" reversed>
            <?php foreach (@helper('debug.trace', array('trace' => $trace)) as $i => $step): ?>
                <li>
                    <p>
                        <?= $step['function'] ?>(<?php if ($step['args']): $args_id = 'args'.$i; ?><a href="#<?= $args_id ?>" onclick="return toggleElement('<?= $args_id ?>')"><?= 'arguments' ?></a><?php endif ?>)
                        &raquo;
                        <span class="file">
                            <?php if ($step['file']): $source_id = 'source'.$i; ?>
                                <a href="#<?= $source_id ?>" onclick="return toggleElement('<?= $source_id ?>')"><?= @helper('debug.path', array('file' => $step['file'])) ?>:<?= $step['line'] ?></a>
                            <?php else: ?>
                                {<?= 'PHP internal call' ?>}
                            <?php endif ?>
                        </span>
                    </p>
                </li>
                <?php unset($args_id, $source_id); ?>
            <?php endforeach ?>
        </ol>

        <div class="codes">
            <?php foreach (@helper('debug.trace', array('trace' => $trace)) as $i => $step): ?>
            <div class="the_error">
                <div class="the_error__header">
                    <?php if ($step['file']): $source_id = 'source'.$i; ?>
                        <span class="file"><?= @helper('debug.path', array('file' => $step['file'])) ?></span>:<span class="linenumber"><?= $step['line'] ?></span>
                    <?php else: ?>
                        {<?= 'PHP internal call' ?>}
                    <?php endif ?>
                </div>

                <?php if (isset($source_id)): ?>
                    <pre id="<?= $source_id ?>" class="source collapsed"><code><?= $step['source'] ?></code></pre>
                <?php endif ?>
            </div>

            <?php if ($step['args']): $args_id = 'args'.$i; ?><?php endif ?>
            <?php if ($step['file']): $source_id = 'source'.$i; ?><?php endif ?>
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

            <?php unset($args_id, $source_id); ?>
            <?php endforeach ?>
        </div>


    </div>
</div>