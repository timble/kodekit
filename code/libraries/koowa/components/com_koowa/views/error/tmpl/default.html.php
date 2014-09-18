<?php
/**
 * Nooku Framework - http://nooku.org/framework
 *
 * @copyright   Copyright (C) 2007 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/nooku/nooku-framework for the canonical source repository
 */

defined('KOOWA') or die; ?>

<title content="replace"><?= translate('Error').' '.$code.' - '. KHttpResponse::$status_messages[$code]; ?></title>

<ktml:style src="media://koowa/com_koowa/css/debugger.css" />
<ktml:script src="media://koowa/com_koowa/js/debugger.js" />
<ktml:script src="media://koowa/com_koowa/js/dumper.js" />

<script data-inline type="text/javascript">
// Remove all classes from html and body
document.body.className = ''; document.documentElement.className = '';
</script>

<!--[if IE 8]>
<div class="old-ie">
<![endif]-->

<div id="error_page">
    <div class="error_page__head">
        <h1 class="page_header">
            <a href="#error_page" class="page_header__exception"><?= $exception ?><? if($level !== false) : ?> | <?= $level ?><? endif ?></a>
            <span class="page_header__code">[<?= $code ?>]</span>
        </h1>
        <div class="page_message">
            <div class="page_message__text"><?= $message ?></div>
        </div>
        <div id="the_error">
            <div class="error_container">
                <div class="error_container__header">
                    <?= helper('debug.path', array('file' => $file)) ?>:<span class="linenumber"><?= $line ?></span>
                </div>
                <div class="error_container_code">
                    <?= helper('debug.source', array('file' => $file, 'line' => $line)) ?>
                </div>
            </div>
        </div>
    </div>
    <div class="page_content">
        <? $trace_steps = helper('debug.trace', array('trace' => $trace)); ?>
        <div id="trace_container" class="trace_container" style="counter-reset: trace-counter <?= count($trace_steps)+1; ?>">
            <div id="trace_wrapper">
                <? foreach ($trace_steps as $i => $step): ?>
                <a id="trace__item--<?= $i; ?>" class="trace__item" href="#source<?= $i ?>">
                    <span class="trace__item__header">
                        <?= $step['function'] ?>(<? if ($step['args']): $args_id = 'args'.$i; ?><? endif ?>)
                    </span>
                </a>
                <? unset($args_id, $source_id); ?>
                <? endforeach ?>
            </div>
        </div>
        <div id="codes_container" class="codes_container" style="counter-reset: source-counter <?= count($trace_steps)+1; ?>">
            <? foreach (helper('debug.trace', array('trace' => $trace)) as $i => $step): ?>
            <? if ($step['file']): $source_id = 'source'.$i; ?>
            <div id="<?= $source_id ?>" class="codes_container__item">
                <div class="codes_container__content">
                    <h3>
                        <?= $step['function'] ?>(<? if ($step['args']): $args_id = 'args'.$i; ?><? endif ?>)
                    </h3>
                    <div class="error_container">
                        <div class="error_container__header">
                            <span class="file"><?= helper('debug.path', array('file' => $step['file'])) ?></span>:<span class="linenumber"><?= $step['line'] ?></span>
                        </div>
                        <? if (isset($source_id)): ?>
                        <div class="error_container__code">
                            <pre class="source_wrap"><code class="hljs php"><?= $step['source'] ?></code></pre>
                        </div>
                        <? endif ?>
                    </div>
                    <? if ($step['args']): $args_id = 'args'.$i; ?><? endif ?>
                    <? if (isset($args_id)): ?>
                    <div id="<?= $args_id ?>" class="args">
                        <h4>Arguments</h4>
                        <div class="arguments_wrapper">
                            <table cellspacing="0">
                                <? foreach ($step['args'] as $name => $arg): ?>
                                    <tr>
                                        <td width="1"><code><?= $name ?></code></td>
                                        <td><pre class="arguments"><?= helper('debug.dump', array('value' => $arg, 'object_depth' => $i ? 1 : 4)) ?></pre></td>
                                    </tr>
                                <? endforeach ?>
                            </table>
                        </div>
                    </div>
                    <? endif ?>
                </div>
            </div>
            <? else: ?>
            {<?= 'PHP internal call' ?>}
            <? endif ?>
            <? unset($args_id, $source_id); ?>
            <? endforeach ?>
            <div class="page_data">
                - That's it! -
            </div>
        </div>
    </div>
</div>

<!--[if IE 8]>
</div>
<![endif]-->