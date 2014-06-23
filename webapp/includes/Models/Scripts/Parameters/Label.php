<?php

namespace Models\Scripts\Parameters;

class Label extends DefaultParameter {
	public function __construct($value) {
		$this->setValue($value);
	}

	public function renderForOperatingSystem() {
		return "";
	}
	public function renderForForm($disabled, \Models\Scripts\ScriptI $script) {
		return "<p><strong>{$this->value}</strong></p>\n";
	}

	public function acceptInput(array $input) {
		return;
	}

	public function renderFormScript($formJsVar, $disabled) {
		return "";
	}
}
