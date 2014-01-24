<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2007 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa for the canonical source repository
 */

defined('KOOWA') or die; ?>

<title content="replace"><?= @translate('Error').' '.$code.' - '. KHttpResponse::$status_messages[$code];; ?></title>

<script type="text/javascript">
    document.documentElement.className = document.documentElement.className + ' js';
    function toggleElement(elem)
    {
        var disp;
        elem = document.getElementById(elem);

        if (elem.style && elem.style['display']) {
            // Only works with the "style" attr
            disp = elem.style['display'];
        }
        else if (elem.currentStyle) {
            // For MSIE, naturally
            disp = elem.currentStyle['display'];
        }
        else if (window.getComputedStyle) {
            // For most other browsers
            disp = document.defaultView.getComputedStyle(elem, null).getPropertyValue('display');
        }

        // Toggle the state of the "display" style
        elem.style.display = disp == 'block' ? 'none' : 'block';
        return false;
    }
</script>

<style type="text/css">
    .error { background: #ddd; font-size: 1em; font-family:sans-serif; text-align: left; color: #111; }
    .error h1,
    .error h2 { margin: 0; padding: 1em 1em 0.5em; font-size: 1em; font-weight: normal; background: #DA2121; color: #fff; }
    .error h1 a,
    .error h2 a { color: #fff; }
    .error h2 { background: #222; }
    .error h3 { margin: 0; padding: 0.4em 0 0; font-size: 1em; font-weight: normal; }
    .error p { margin: 0; padding: 0.2em 0; }
    .error a { color: #1b323b; }
    .error pre { overflow: auto; white-space: pre-wrap; }
    .error table { width: 100%; display: block; margin: 0 0 0.4em; padding: 0; border-collapse: collapse; background: #fff; }
    .error table td { border: solid 1px #ddd; text-align: left; vertical-align: top; padding: 0.4em; }
    .error div.content { padding: 0.4em 1em 1em; overflow: hidden; }
    .error pre.source { margin: 0 0 1em; padding: 0.4em; background: #fff; border: dotted 1px #b7c680; line-height: 1.2em; }
    .error pre.source span.line { display: block; }
    .error pre.source span.highlight { background: #f0eb96; }
    .error pre.source span.line span.number { color: #666; }
    .error ol.trace { display: block; margin: 0 0 0 2em; padding: 0; list-style: decimal; }
    .error ol.trace li { margin: 0; padding: 0; }
    .js .collapsed { display: none; }
</style>


<div class="error">
    <h1><span class="type"><?= $exception ?> [<?= $code ?>]</span></h1>
    <h1><span class="message"><?= $message ?></span></h1>
    <div class="content">
        <p><span class="file"><?= $file ?>:<?= $line ?></span></p>
        <?= @helper('debug.source', array('file' => $file, 'line' => $line)) ?>
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
                    <?php if (isset($source_id)): ?>
                        <pre id="<?= $source_id ?>" class="source collapsed"><code><?= $step['source'] ?></code></pre>
                    <?php endif ?>
                </li>
                <?php unset($args_id, $source_id); ?>
            <?php endforeach ?>
        </ol>
    </div>
</div>