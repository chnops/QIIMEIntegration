<?php

namespace Models\Scripts\Parameters;
use \Models\Scripts\ScriptException;

class TextArgumentParameter extends DefaultParameter {
	private $expectedPattern;
	public function __construct($name, $defaultValue, $expectedPattern) {
		$this->name = $name;
		$this->value = $defaultValue;
		$this->expectedPattern = $expectedPattern;
	}

	public function isValueValid($value) {
		if (!$value) {
			return true;
		}
		$match = preg_match($this->expectedPattern, $value);
		if ($match === false) {
			throw new ScriptException("Unable to verify input pattern.");
		}
		return $match;
	}
}
