<?php

namespace Models\Scripts;

class OldFileParameter extends DefaultParameter {
	private $options = array("dbOne", "dbTwo");
	public function renderForForm() {
		$output = "<label for=\"{$this->name}\">{$this->name}<select name=\"{$this->name}\">\n";
		foreach ($this->options as $option) {
			$output .= "<option value=\"{$option}\">{$option}</option>\n";
		}
		$output .= "</select></label>\n";
		return $output;
	}
}
