<?php

require_once __DIR__.'/../code/libraries/koowa/libraries/koowa.php';

Koowa::getInstance();

$username = $argv[1];
$secret   = $argv[2];

echo KObjectManager::getInstance()->getObject('http.token')->setSubject($username)->sign($secret);