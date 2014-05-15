<?php

namespace Models\Scripts;

class FileParameter implements ParameterI {
	public function renderForForm() {
		return "<label for=\"{$this->name}\">{$this->name}<input type=\"text\" name=\"{$this->name}\" value=\"{$this->value}\"/></label>";
	}
}
