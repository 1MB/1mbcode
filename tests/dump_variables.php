<?php
require_once '../vendor/autoload.php';

$script = 'var str_test = "hey";var int_test = 500;var float_test = 100.37;var array_test = {"name": "test"}; var func_test = add(1, 2);var nested_test = &array_test.name;var bool_test = true; var test_assignment = 1 + 4;';
$script .= 'var func_test = add(1, 2);';

$script .= 'print(1 + 4);';

$script .= 'var assignment = 1 + 4;';
$script .= PHP_EOL;
$script .= 'print(&assignment);';

$functions = [];
$functions['add'] = function($one, $two) { return (int)$one + (int)$two; };

$parser = new \onembsite\onembcode\Parser($script, $functions);
$startTime = microtime(true);
$parser->parse();
echo "Compile time is: ". (microtime(true) - $startTime) ." seconds" . PHP_EOL;
//output: Compile time is: 0.004626989364624 seconds (PHP 7.3, Windows 10 Pro Edition)