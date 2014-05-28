<?php

namespace Models\Scripts\Parameters;

class EitherOrParameter extends DefaultParameter {
	protected $default;
	protected $alternative;

	public function __construct($default, $alternative) {
		$this->default = $default;
		$this->alternative = $alternative;
		$this->name = "__{$default->getName()}__{$alternative->getName()}__";
	}

	public function renderForOperatingSystem() {
		if ($this->value == $this->default->getName()) {
			return $this->default->renderForOperatingSystem();
		}
		if ($this->value == $this->alternative->getName()) {
			return $this->alternative->renderForOperatingSystem();
		}
		return "";
	}
	public function renderForForm($disabled) {
		$disabledString = ($disabled) ? " disabled" : "";
		$defaultSelected = " checked";
		$alternativeSelected = "";
		if ($this->value == $this->alternative->getName()) {
			$defaultSelected = "";
			$alternativeSelected = " checked";
		}
		$output = "<label for=\"{$this->name}\">{$this->name}<table>
			<tr><td><input type=\"radio\" name=\"{$this->name}\" value=\"{$this->default->getName()}\"{$defaultSelected}{$disabledString}/></td><td>{$this->default->renderForForm($disabled)}</td></tr>
			<tr><td><input type=\"radio\" name=\"{$this->name}\" value=\"{$this->alternative->getName()}\"{$alternativeSelected}{$disabledString}/></td><td>{$this->alternative->renderForForm($disabled)}</td></tr>
			</table></label>";
		return $output;
	}
	public function isValueValid($value) {
		if ($this->default->getName() == $value) {
			return true;
		}
		if ($this->alternative->getName() == $value) {
			return true;
		}
		return false;
	}

	public function getDefault() {
		return $this->default;
	}
	public function getAlternative() {
		return $this->alternative;
	}
}
