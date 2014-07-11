<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2007 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa for the canonical source repository
 */

defined('KOOWA') or die; ?>

<?php
$document = JFactory::getDocument();
$document->addScript('/media/koowa/com_koowa/js/debugger.js');
?>

<title content="replace"><?= @translate('Error').' '.$code.' - '. KHttpResponse::$status_messages[$code];; ?></title>

<link type="text/css" rel="stylesheet" href="/media/koowa/com_koowa/css/debugger.css" media="" attribs="" type="text/css" />


<div id="error_page">

    <h1 class="page_header">
        <span class="page_header__exception"><?= $exception ?></span>
        <span class="page_header__code">[<?= $code ?>]</span>
    </h1>
    <div class="page_message">
        <div class="page_message__text"><?= $message ?></div>
        <div id="page_data__button" class="page_message__button">Show page data</div>
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