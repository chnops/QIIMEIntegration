<?php

namespace Models\Scripts\Parameters;
use \Models\Scripts\ScriptException;

class EitherOrParameter extends DefaultParameter {
	protected $selected;
	protected $notSelected;

	public function __construct($default, $alternative) {
		$this->selected = $default;
		$this->notSelected = $alternative;
		$this->name = "__{$default->getName()}__{$alternative->getName()}__";
		$this->value = $default->getName();
	}

	public function renderForOperatingSystem() {
		if ($this->value) {
			return $this->selected->renderForOperatingSystem();
		}
		return "";
	}
	public function renderForForm($disabled) {
		$disabledString = ($disabled) ? " disabled" : "";
		$output = "<label for=\"{$this->name}\">{$this->name}<table>
			<tr><td><input type=\"radio\" name=\"{$this->name}\" value=\"{$this->selected->getName()}\" selected{$disabledString}/></td><td>{$this->selected->renderForForm($disabled)}</td></tr>
			<tr><td><input type=\"radio\" name=\"{$this->name}\" value=\"{$this->notSelected->getName()}\"{$disabledString}/></td><td>{$this->notSelected->renderForForm($disabled)}</td></tr>
			</table></label>";
		return $output;
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

	public function acceptInput(array $input) {
		if (!isset[$input[$this->name]) {
			$this->setValue("");
			return;
		}

		$this->setValue($input[$this->name]);
		if (!isset($input[$this->selected->getName()])) {
			throw new ScriptException("Since {$this->name} is set to {$this->value}, that parameter must be specified.");
		}
		if (isset($input[$this->nonSelected->getName()])) {
			throw new ScriptException("Since {$this->name} is set to {$this->value}, {$this->notSelected->getName()} is not allowed.");
		}
		$this->selected->setValue($input[$this->getValue()]);
	}
}
