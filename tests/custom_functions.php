<?php
require_once '../vendor/autoload.php';

$total_time = 0;
$startTime = microtime(true);

$script = 'var subString = sub("hello world", 0, 8);';
$script .= PHP_EOL;
$script .= 'print(&subString);';

$functions = [];
$parser = new \onembsite\onembcode\Parser($script, $functions);
$parser->parse();