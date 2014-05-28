<?php

namespace Models\Scripts\Parameters;
use \Models\Scripts\ScriptException;

class DefaultParameter implements ParameterI {
	protected $name;
	protected $value;

	public function __construct($name, $value) {
		$this->name = $name;
		$this->value = $value;
	}

	public function renderForOperatingSystem() {
		if ($this->value) {
			$separator = (strlen($this->name) == 2) ? " " : "=";
			return $this->name . $separator . "'" . $this->value . "'";
		}
		return "";
	}
	public function renderForForm($disabled) {
		$disabledString = ($disabled) ? " disabled" : "";
		return "<label for=\"{$this->name}\">{$this->name}<input type=\"text\" name=\"{$this->name}\" value=\"{$this->value}\"{$disabledString}/></label>";
	}
	public function setValue($value) {
		if (!$this->isValueValid($value)) {
			throw new ScriptException("An invalid value was provided for the parameter: {$this->name}");
		}
		$this->value = $value;
	}
	public function getValue() {
		return $this->value;
	}
	public function setName($name) {
		$this->name = $name;
	}
	public function getName() {
		return $this->name;
	}
	public function isValueValid($value) {
		return true;
	}

	public function acceptInput(array $input) {
		if (isset($input[$this->name])) {
			$this->setValue($input[$this->name]);
		}
	}
}
