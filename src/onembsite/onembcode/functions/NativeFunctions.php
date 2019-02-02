<?php

	namespace onembsite\onembcode\functions;

	class NativeFunctions {
		
		/**
		 * Private helper function
		 */
		 
		private static function sub_string($string, $start = 0, $end = -1) {
			if (!HelperFunctions::is_type($start, "integer") && !is_numeric($start)) {
				HelperFunctions::invalid_argument_type("integer", gettype($start));
			}
			
			if (!HelperFunctions::is_type($end, "integer") && !is_numeric($end)) {
				HelperFunctions::invalid_argument_type("integer", gettype($end));
			}
			
			// Convert strings into integers
			$start = $start + 0;
			$end = $end + 0;
			
			if ($end == -1) {
				$end = \strlen($string);
			}
			
			/*
			 * The 3rd argument in substr() is the number of
			 * elements to slice, not the position to stop
			 * selecting elements. To fix this, I'm subtracting
			 * $start from $end to simulate the functionality of
			 * substr().
			 *
			 */
			return substr($string, $start, $end - $start);
		}
		 
		private static function sub_array($array, $start = 0, $end = -1) {
			if (!HelperFunctions::is_type($start, "integer") && !is_numeric($start)) {
				HelperFunctions::invalid_argument_type("integer", gettype($start));
			}
			
			if (!HelperFunctions::is_type($end, "integer") && !is_numeric($end)) {
				HelperFunctions::invalid_argument_type("integer", gettype($end));
			}
			
			$start = $start + 0;
			$end = $end + 0;
			
			if ($end == -1) {
				$end = \count($array);
			}
			
			/*
			 * The 3rd argument in array_slice() is the number of
			 * elements to slice, not the position to stop
			 * selecting elements. To fix this, I'm subtracting
			 * $start from $end to simulate the functionality of
			 * substr().
			 *
			 */
			return array_slice($array, $start, $end - $start);
		}
		
		
		
		/**
		 * Functions available publicly through 1mbcode
		 */
		 
		public static function sub($string_or_array, $start = 0, $end = -1) {
			
			$type = @gettype($string_or_array);
			
			switch ($type) {
				case "string":
					return self::sub_string($string_or_array, @$start, @$end);
					break;
					
				case "array":
					return self::sub_array($string_or_array, @$start, @$end);
					break;
					
				default:
					HelperFunctions::invalid_argument_type(["string", "array"], $type);
					break;
			}
			
		}
		
	}
	
?>