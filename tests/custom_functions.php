<?php
require_once '../vendor/autoload.php';

$script = 'var substring = sub("Hello world", 1, 4);';
$script .= PHP_EOL;
$script .= 'dump(&substring);';

$functions = [];
$parser = new \onembsite\onembcode\Parser($script, $functions);
$parser->parse();