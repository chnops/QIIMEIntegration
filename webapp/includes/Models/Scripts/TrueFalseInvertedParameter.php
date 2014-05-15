<?php

namespace Models\Scripts;

class TrueFalseInvertedParameter extends DefaultParameter {
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
}
