<?php

namespace Models\Scripts\Parameters;
use \Models\Scripts\ScriptException;

class EitherOrParameter extends DefaultParameter {
	protected $default;
	protected $alternative;

	public function __construct($default, $alternative) {
		$this->default = $default;
		$this->alternative = $alternative;
		$this->name = "__{$default->getName()}__{$alternative->getName()}__";
	}

	public function renderForOperatingSystem() {
		if (!$this->value) {
			return "";
		}
		if ($this->value == $this->default->getName()) {
			return $this->default->renderForOperatingSystem();
		}
		if ($this->value == $this->alternative->getName()) {
			return $this->alternative->renderForOperatingSystem();
		}
	}

	public function renderForForm($disabled) {
		$disabledString = ($disabled) ? " disabled" : "";
		$checkedArray = array('','','');
		if (!$this->value) {
			$checkedArray[0] = " checked";
		}
		else if ($this->value == $this->default->getName()) {
			$checkedArray[1] = " checked";
		}
		else if ($this->value == $this->alternative->getName()) {
			$checkedArray[2] = " checked";
		}

		$output = "<table class=\"either_or\"><tr><td colspan=\"2\"><label for=\"{$this->name}\">{$this->default->getName()} or {$this->alternative->getName()} <a onclick=\"paramHelp('{$this->name}');\">&amp;</a><br/>";
		$output .= "<input type=\"radio\" name=\"{$this->name}\" value=\"\"{$checkedArray[0]}{$disabledString}>Neither</label></td></tr>";
		$output .= "<tr>" . 
			"<td><label for=\"{$this->name}\"><input type=\"radio\" name=\"{$this->name}\" value=\"{$this->default->getName()}\"{$checkedArray[1]}{$disabledString}>{$this->default->renderForForm()}</label></td>" . 
			"<td><label for=\"{$this->name}\"><input type=\"radio\" name=\"{$this->name}\" value=\"{$this->alternative->getName()}\"{$checkedArray[2]}{$disabledString}>{$this->alternative->renderForForm()}</label></td>" . 
			"</tr></table>";
		return $output;
	}
	public function renderFormScript($formJsVar, $disabled) {
		if ($disabled) {
			return "";
		}
		$code = parent::renderFormScript($formJsVar, $disabled);
		$jsVar = $this->getJsVar($formJsVar);
		return $code . "\tmakeEitherOr({$jsVar});{$jsVar}.change();\n";
	}
	public function isValueValid($value) {
		if (!$value) {
			return true;
		}
		if ($this->default->getName() == $value) {
			return true;
		}
		if ($this->alternative->getName() == $value) {
			return true;
		}
		return false;
	}

	public function getAlternativeValue() {
		if (!$this->value) {
			return "";
		}
		if ($this->value == $this->default->getName()) {
			return $this->alternative->getName();
		}
		return $this->default->getName();
	}

	public function acceptInput(array $input) {
		parent::acceptInput($input);
		if (!isset($input[$this->name]) || !$input[$this->name]) {
			$this->setValue("");
			return;
		}

		$this->setValue($input[$this->name]);
		if (!isset($input[$this->value])) {
			throw new ScriptException("Since {$this->name} is set to {$this->value}, that parameter must be specified.");
		}
		if (isset($input[$this->getAlternativeValue()])) {
			throw new ScriptException("Since {$this->name} is set to {$this->value}, {$this->getAlternativeValue()} is not allowed.");
		}
		$this->default->setValue($input[$this->getValue()]);
	}
}
