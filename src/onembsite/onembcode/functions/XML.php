<?php

	namespace onembsite\onembcode\functions;

	class XML {
		public static function parse($xml = "") {
			if (!HelperFunctions::is_type($xml, "string")) {
				HelperFunctions::invalid_argument_type("string", gettype($xml));
			}
			
			$parsed = @new SimpleXMLElement($xml, LIBXML_NOCDATA);
			$parsed_array = @json_decode(json_encode((array) $parsed), true);
			$parsed_array = array(@$parsed->getName() => $parsed_array);
			
			if (!$parsed_array) {
				throw new \Exception("RuntimeError: Invalid XML data provided.");
			}
			
			return $parsed_array;
		}
		
		
		public static function create($array = array()) {
			if (!HelperFunctions::is_type($array, "array")) {
				HelperFunctions::invalid_argument_type("array", gettype($array));
			}
			
			$xml = @new SimpleXMLElement('<root/>');
			@array_walk_recursive($array, array ($xml, 'addChild'));
			$serialised = $xml->asXML();
			
			if (!$serialised) {
				throw new \Exception("RuntimeError: Could not convert array to XML");
			}
			
			return $serialised;
		}
	}
	
?>