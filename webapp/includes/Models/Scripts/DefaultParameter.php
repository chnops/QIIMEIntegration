<?php

namespace Models\Scripts;

class DefaultParameter implements ParameterI {
	protected $name;
	protected $value;
	protected $isRequired;

	public function __construct($name, $value, $isRequired = false) {
		$this->name = $name;
		$this->value = $value;
		$this->isRequired = $isRequired;
	}
	public function renderForOperatingSystem() {
		return $this->name . " " . $this->value;
	}
	public function renderForForm() {
		return "<label for=\"$this->name\">{$this->name}<input type=\"text\" name=\"{$this->name}\" value=\"{$this->value}\"/></label>";
	}
	public function setValue($value) {
		$this->value = $this->setValue;
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
	public function setIsRequired($isRequired) {
		$this->isRequired = $isRequired;
	}
	public function isRequired() {
		return $this->isRequired;
	}
}
