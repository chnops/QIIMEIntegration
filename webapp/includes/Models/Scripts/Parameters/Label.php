<?php

namespace Models\Scripts\Parameters;

class Label extends DefaultParameter {
	public function __construct($value) {
		$this->setValue($value);
	}

	public function renderForOperatingSystem() {
		return "";
	}
	public function renderForForm() {
		return $this->value;
	}
}
