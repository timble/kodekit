<!DOCTYPE html>
<html class="koowa-html" xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <link rel="stylesheet"
          href="<?php echo KObjectManager::getInstance()->getObject('request')->getBaseUrl('site'); ?>/media/koowa/com_koowa/css/bootstrap.min.css"
          type="text/css"/>
    <link rel="stylesheet"
          href="<?php echo KObjectManager::getInstance()->getObject('request')->getBaseUrl('site'); ?>/templates/system/css/general.css"
          type="text/css"/>
    <jdoc:include type="head" />
</head>
<body class="koowa koowa_template">
<!--[if lte IE 8 ]>
<div class="old-ie"> <![endif]-->
<div class="koowa_template_container">
    <jdoc:include type="message" />
    <jdoc:include type="component" />
</div>
<!--[if lte IE 8 ]></div><![endif]-->
</body>
</html>