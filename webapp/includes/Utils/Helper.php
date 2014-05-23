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
}
