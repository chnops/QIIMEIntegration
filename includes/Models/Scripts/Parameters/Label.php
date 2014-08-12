<?php
/*
 * Copyright (C) 2014 Aaron Sharp
 * Released under GNU GENERAL PUBLIC LICENSE Version 3, 29 June 2007
 */

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
