<?php

namespace Utils;

class Helper {
	public static function getHelper() {
		return new Helper();
	}

	public function categorizeArray(array $rawArray, $categoryField, $fieldToKeep = "") {
		$formattedArray = array();
		foreach ($rawArray as $element) {
			$category = $element[$categoryField];
			if (!isset($formattedArray[$category])) {
				$formattedArray[$category] = array();
			}
			if ($fieldToKeep) {
				$element = $element[$fieldToKeep];
			}
			$formattedArray[$category][] = $element;
		}
		return $formattedArray;
	}

	public function htmlentities($string) {
		if ($string === 0) {
			return 0;
		}
		else if (!$string) {
			return "";
		}

		if (defined('ENT_SUBSTITUE')) {
			$stringEsc = htmlentities($string, ENT_QUOTES | ENT_HTML5 | ENT_SUBSTITUTE);
		}
		else {
			$stringEsc = htmlentities($string, ENT_QUOTES | ENT_IGNORE);
		}
		if (!$stringEsc) {
			throw new \Exception("Problem with html special char escaping; dropped whole string");
		}
		return $stringEsc;
	}
}
