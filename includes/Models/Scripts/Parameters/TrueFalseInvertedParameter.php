<?php
/*
 * Copyright (C) 2014 Aaron Sharp
 * Released under GNU GENERAL PUBLIC LICENSE Version 3, 29 June 2007
 */

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
