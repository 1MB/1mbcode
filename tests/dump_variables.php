<?php
require_once './vendor/autoload.php';

$script = 'var str_test = "hey";var int_test = 500;var float_test = 100.37;var array_test = {"name": "test"}; var func_test = add(1, 2);var nested_test = &array_test.name;';

$functions = [];
$functions['add'] = function($one, $two) { return (int)$one + (int)$two; };

$parser = new \onembsite\onembcode\Parser($script, $functions);
$startTime = microtime(true);
$parser->parse();
echo "Compile time is: ". (microtime(true) - $startTime) ." seconds" . PHP_EOL;
//output: Compile time is: 0.002863883972168 seconds (PHP 7.0, Windows 10 Pro Edition)