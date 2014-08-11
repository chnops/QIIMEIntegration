<?php

namespace Models\Scripts\Parameters;

class TrueFalseInvertedParameter extends TrueFalseParameter {
	public function __construct($name) {
		$this->name = $name;
		$this->value = true;
	}
	public function renderForOperatingSystem() {
		if (!$this->value) {
			return $this->name;
		}
		return "";
	}
}
