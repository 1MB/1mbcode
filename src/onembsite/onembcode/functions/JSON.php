<?php

	class JSON {
		public static function parse($json = "") {
			if (!HelperFunctions::is_type($json, "string")) {
				HelperFunctions::invalid_argument_type("string", gettype($json));
			}
			
			$parsed = @json_decode($json, true);
			
			if (!$parsed) {
				throw new \Exception("RuntimeError: Invalid JSON data provided.");
			}
			
			return $parsed;
		}
		
		
		public static function create($array = array(), $prettify = false) {
			if (!HelperFunctions::is_type($array, "array")) {
				HelperFunctions::invalid_argument_type("array", gettype($array));
			}
			
			if ($prettify == true) {
				$serialised = @json_encode($array, JSON_PRETTY_PRINT);
			} else {
				$serialised = @json_encode($array);
			}
			
			if (!$serialised) {
				throw new \Exception("RuntimeError: Could not convert array to JSON");
			}
			
			return $serialised;
		}
	}
	
?>