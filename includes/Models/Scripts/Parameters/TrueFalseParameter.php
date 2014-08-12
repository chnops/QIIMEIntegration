<?php
/*
 * Copyright (C) 2014 Aaron Sharp
 * Released under GNU GENERAL PUBLIC LICENSE Version 3, 29 June 2007
 */

namespace Models\Scripts\Parameters;

class TrueFalseParameter extends DefaultParameter {
	public function __construct($name) {
		$this->name = $name;
		$this->value = false;
	}
	public function renderForOperatingSystem() {
		if ($this->value) {
			return $this->name;
		}
		return "";
	}
	public function renderForForm($disabled, \Models\Scripts\ScriptI $script) {
		$disabledString = ($disabled) ? " disabled" : "";
		$checked = ($this->value) ? " checked" : "";
		return "<label for=\"{$this->name}\"><input type=\"checkbox\" name=\"{$this->name}\"{$checked}{$disabledString}/> {$this->name}
			<a class=\"param_help\" id=\"{$this->getJsVar($script->getJsVar())}\">&amp;</a></label>";
	}

	public function acceptInput(array $input) {
		parent::acceptInput($input);
		if (!isset($input[$this->name])) {
			$this->setValue(false);
		}
		else {
			$this->setValue(true);
		}
	}
}
