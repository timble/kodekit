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

<style src="media://koowa/com_koowa/css/debugger.css" />
<script src="media://koowa/com_koowa/js/debugger.js" />

<!--[if IE 8]>
<div class="old-ie">
<![endif]-->

<div id="error_page">

    <h1 class="page_header">
        <span class="page_header__exception"><?= $exception ?></span>
        <span class="page_header__code">[<?= $code ?>]</span>
    </h1>

    <div class="page_message">
        <div class="page_message__text"><?= $message ?></div>
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
                <a id="trace__item--<?= $i; ?>" class="trace__item" data-scroll href="#source<?= $i ?>">
                    <span class="trace__item__header">
                        <?= $step['function'] ?>(<?php if ($step['args']): $args_id = 'args'.$i; ?><?php endif ?>)
                    </span>
                    <span class="trace__item__file">
                        <?php if ($step['file']): $source_id = 'source'.$i; ?>
                            <?= @helper('debug.path', array('file' => $step['file'])) ?>:<span class="linenumber"><?= $step['line'] ?></span>
                        <?php else: ?>
                            {<?= 'PHP internal call' ?>}
                        <?php endif ?>
                    </span>
                </a>
                <?php unset($args_id, $source_id); ?>
                <?php endforeach ?>
            </div>
        </div>

        <div id="codes_container" class="codes_container" style="counter-reset: source-counter <?php echo $num+2; ?>">
            <?php foreach (@helper('debug.trace', array('trace' => $trace)) as $i => $step): ?>
            <?php if ($step['file']): $source_id = 'source'.$i; ?>
            <div id="<?= $source_id ?>" class="codes_container__item">
                <h3>
                    <?= $step['function'] ?>(<?php if ($step['args']): $args_id = 'args'.$i; ?><?php endif ?>)
                </h3>
                <div class="error_container">
                    <div class="error_container__header">
                        <span class="file"><?= @helper('debug.path', array('file' => $step['file'])) ?></span>:<span class="linenumber"><?= $step['line'] ?></span>
                    </div>
                    <?php if (isset($source_id)): ?>
                    <div class="error_container__code">
                        <pre class="source_wrap"><code class="hljs php"><?= $step['source'] ?></code></pre>
                    </div>
                    <?php endif ?>
                </div>
                <?php if ($step['args']): $args_id = 'args'.$i; ?><?php endif ?>
                <?php if (isset($args_id)): ?>
                <div id="<?= $args_id ?>" class="args">
                    <h4>Arguments</h4>
                    <table cellspacing="0">
                        <?php foreach ($step['args'] as $name => $arg): ?>
                            <tr>
                                <td><code><?= $name ?></code></td>
                                <td><pre class="arguments"><?= @helper('debug.dump', array('value' => $arg)) ?></pre></td>
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

<!--[if IE 8]>
</div>
<![endif]-->