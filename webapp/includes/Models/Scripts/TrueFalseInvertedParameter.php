<?php

namespace Models\Scripts;

class TrueFalseInvertedParameter extends DefaultParameter {
	public function __construct($name, $value, $isRequired = false) {
		$this->name = $name;
		$this->value = true;
		$this->isRequired = false;
	}
	public function renderForOperatingSystem() {
		if (!$this->value) {
			return $this->name;
		}
		else {
			return "";
		}
	}
	public function renderForForm() {
		return "<label for=\"{$this->name}\"><input type=\"checkbox\" name=\"{$this->name}\" checked/> {$this->name}</label>";
	}
	public function setValue($value) {
		if ($value) {
			$this->value = false;
		}
		else {
			$this->value = true;
		}
	}
}
