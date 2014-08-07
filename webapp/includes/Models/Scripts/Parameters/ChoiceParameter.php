<?php

namespace Models\Scripts\Parameters;

class ChoiceParameter extends DefaultParameter {
	private $options = array();
	
	public function __construct($name, $defaultValue, array $options) {
		$this->name = $name;
		$this->value = $defaultValue;
		$this->options = $options;
	}

	public function renderForForm($disabled, \Models\Scripts\ScriptI $script) {
		$disabledString = ($disabled) ? " disabled" : "";
		$output = "<label for=\"{$this->name}\">{$this->name} <a class=\"param_help\" id=\"{$this->getJsVar($script->getJsVar())}\">&amp;</a>
			<select name=\"{$this->name}\"{$disabledString}>\n";
		foreach ($this->options as $option) {
			$selected = ($this->value == $option) ? " selected" : "";
			$output .= "<option value=\"{$option}\"{$selected}>{$option}</option>\n";
		}
		$output .= "</select></label>\n";
		return $output;
	}
	
	public function isValueValid($value) {
		if (!$value) {
			return true;
		}
		return in_array($value, $this->options, $strict = true);
	}

	public function setOptions(array $options) {
		$this->options = $options;
	}
	public function getOptions() {
		return $this->options;
	}
}
