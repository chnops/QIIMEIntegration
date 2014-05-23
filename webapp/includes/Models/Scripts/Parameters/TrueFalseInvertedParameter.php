<?php

namespace Models\Scripts\Parameters;

class TrueFalseInvertedParameter extends DefaultParameter {
	public function __construct($name) {
		$this->name = $name;
		$this->value = true;
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
		$checked = ($this->value) ? " checked" : "";
		return "<label for=\"{$this->name}\"><input type=\"checkbox\" name=\"{$this->name}\"{$checked}/> {$this->name}</label>";
	}
}
