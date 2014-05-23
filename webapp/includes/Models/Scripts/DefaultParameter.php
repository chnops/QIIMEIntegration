<?php

namespace Models\Scripts;

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
	public function renderForForm() {
		return "<label for=\"{$this->name}\">{$this->name}<input type=\"text\" name=\"{$this->name}\" value=\"{$this->value}\"/></label>";
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
}
