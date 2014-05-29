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
		$output = "<label for=\"{$this->name}\">{$this->name}<table style=\"border-left:1px solid\">";
		$tableRows = array(
			array("value" => "", "checked" => false, "body" => "None"),
			array("value" => $this->default->getName(), "checked" => false, "body" => $this->default->renderForForm($disabled)),
			array("value" => $this->alternative->getName(), "checked" => false, "body" => $this->alternative->renderForForm($disabled)),

		);
		if (!$this->value) {
			$tableRows[0]['checked'] = true;
		}
		else if ($this->value == $this->default->getName()) {
			$tableRows[1]['checked'] = true;
		}
		else if ($this->value == $this->alternative->getName()) {
			$tableRows[2]['checked'] = true;
		}
		foreach ($tableRows as $row) {
			$output .= "<tr><td><input type=\"radio\" name=\"{$this->name}\" value=\"{$row['value']}\"{$disabledString}";
			$output .= ($row['checked']) ? " checked" : "";
			$output .= "/></td><td>{$row['body']}</td></tr>";
		}
		$output .= "</table></label>";
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
