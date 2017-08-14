<?php
/**
 * The extremely beautiful code below will output PHP array elements that are ready to copy-paste.
 *
 * Instructions:
 * - Run the script like this: `php generate-phpstorm-metadata.php > ~/phpstorm-metadata.txt`
 * - Copy the output into .phpstorm.meta.php, below the 'automatically generated' comment in the file
 */

$directories = [
    __DIR__.'/../code'
];

$skip_classes = ['Kodekit'];
$skip_identifiers = [];

/*
 * ------
 */
$classes = [];

foreach ($directories as $directory) {
    getClassesInDirectory($directory, $classes);
}

sort($classes);

foreach ($classes as $class) {
	$class = trim($class);

	if (in_array($class, $skip_classes)) {
	    continue;
    }

    $identifier = strtolower(preg_replace('/(?<=\\w)([A-Z])/', '.\\1', $class));

    $map[$identifier] = '\\Kodekit\\Library\\'.$class;
    $map['lib:'.$identifier] = '\\Kodekit\\Library\\'.$class;
}

ksort($map);

foreach ($map as $i => $cls) {
    if (in_array($i, $skip_identifiers)) {
        continue;
    }

    echo "\t\t\t'$i' => $cls::class,\n";
}

function getClassesInDirectory($directory, &$classes)
{
    $it = new RecursiveDirectoryIterator($directory);
    $it = new RecursiveIteratorIterator($it, RecursiveIteratorIterator::LEAVES_ONLY);
    $it = new RegexIterator($it, '(\.' . preg_quote('php') . '$)');

    $p = '#(?<!abstract )class\s+([A-Za-z0-9_\-]+)\s+(?:extends|\{)#';

    foreach ($it as $file) {
        $contents = file_get_contents($file);
        preg_match($p, $contents, $matches);
        if ($matches) {
            $classes[] = $matches[1];
        }
    }
}
