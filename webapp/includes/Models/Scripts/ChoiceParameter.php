<?php

namespace Models\Scripts;

class ChoiceParameter extends DefaultParameter {
	private $options = array("one", "two");
	public function renderForForm() {
		$output = "<label for=\"{$this->name}\">{$this->name}<select name=\"{$this->name}\">\n";
		foreach ($this->options as $option) {
			$output .= "<option value=\"{$option}\">{$option}</option>\n";
		}
		$output .= "</select></label>\n";
	}
}
