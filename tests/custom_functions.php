<?php
require_once '../vendor/autoload.php';

<<<<<<< HEAD
$script = 'var substring = sub("Hello world", 1, 4);';
$script .= PHP_EOL;
$script .= 'dump(&substring);';
=======
$total_time = 0;
$startTime = microtime(true);

$script = 'var subString = sub("hello world", 0, 8);';
$script .= PHP_EOL;
$script .= 'print(&subString);';
>>>>>>> master

$functions = [];
$parser = new \onembsite\onembcode\Parser($script, $functions);
$parser->parse();