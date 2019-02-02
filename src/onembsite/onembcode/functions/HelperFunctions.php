<?php

	namespace onembsite\onembcode\functions;

	class HelperFunctions {
		 
		public static function is_type($variable, $type) {
			return @gettype($variable) == $type;
		}
		
		public static function invalid_argument_type($expected, $received) {
			$expected_string = "";
			
			switch (gettype($expected)) {
				case "string":
					$expected_string = "`{$expected}`";
					break;
					
				case "array":
					$count = count($expected);
					
					for ($i = 0; $i < $count; $i++) {
						if ($i != 0 && $i != $count - 1) $expected_string .= ", ";
						if ($i == $count - 1)  $expected_string .= " or ";
						
						$expected_string .= "`{$expected[$i]}`";
					}
					
					break;
					
				default:
					throw new \Exception("RuntimeError: Invalid argument passed to function.");
					break;
			}
			
			
			throw new \Exception("RuntimeError: Invalid argument passed to function. Expected " . $expected_string . " but received `" . $received . "`");
		}
	}
	
?>