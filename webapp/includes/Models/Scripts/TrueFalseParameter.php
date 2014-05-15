<?php

namespace Models\Scripts;

class TrueFalseParameter extends DefaultParameter {
	public function renderForOperatingSystem() {
		if ($this->value) {
			return $this->name;
		}
		else {
			return "";
		}
	}
	public function renderForForm() {
		return "<label for=\"{$this->name}\"><input type=\"checkbox\" name=\"{$this->name}\"/> {$this->name}</label>";
	}
}
