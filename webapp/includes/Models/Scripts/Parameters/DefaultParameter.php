<?php

namespace Models\Scripts\Parameters;
use \Models\Scripts\ScriptException;

class DefaultParameter implements ParameterI {
	protected $name;
	protected $value;

	private $isAlwaysRequired = false;
	private $requiringTriggers = array();

	private $isEverExcluded = false;
	private $allowingTriggers = array();

	public function __construct($name, $value) {
		$this->name = $name;
		$this->value = $value;
	}

	public function renderForOperatingSystem() {
		if ($this->value) {
			$separator = (strlen($this->name) == 2) ? " " : "=";
			return $this->name . $separator . escapeshellarg($this->value);
		}
		return "";
	}
	public function renderForForm($disabled) {
		$disabledString = ($disabled) ? " disabled" : "";
		return "<label for=\"{$this->name}\">{$this->name}<input type=\"text\" name=\"{$this->name}\" value=\"{$this->value}\"{$disabledString}/></label>";
	}
	public function setValue($value) {
		if (!$this->isValueValid($value)) {
			throw new ScriptException("An invalid value was provided for the parameter: {$this->name}");
		}
		$this->value = $value;
	}
	public function getValue() {
		return $this->value;
	}
	public function setName($name) {
		$this->name = $name;
	}
	public function getName() {
		return $this->name;
	}
	public function isValueValid($value) {
		return true;
	}

	public function acceptInput(array $input) {
		if (!isset($input[$this->name]) || !$input[$this->name]) {
			if ($this->isAlwaysRequired) {
				throw new ScriptException("A required parameter was not found: {$this->name}");
			}

			foreach ($this->requiringTriggers as $trigger) {
				$triggerParam = $trigger['parameter'];
				$triggerValue = $trigger['value'];
				if (!$triggerValue) {
					$isRequired = isset($input[$triggerParam->getName()]);
				}
				else {
					$isRequired = $input[$triggerParam->getName()] == $triggerValue;
				}
				if ($isRequired) {
					throw new ScriptException("A required parameter was not found: {$this->name} (required by {$triggerParam->getName()})");	
				}
			}
		}
		else {
			if ($this->isEverExcluded) {
				$allowed = false;
				$errorMessage = (empty($this->allowingTriggers)) ? 
					"The parameter {$this->name} is not allowed" :
					"The parameter {$this->name} is only allowed when:";

				foreach ($this->allowingTriggers as $trigger) {
					$triggerParam = $trigger['parameter'];
					$triggerValue = $trigger['value'];
					if ($triggerValue) {
						$errorMessage .= "<br/>&nbsp;{$triggerParam->getName()} is set to {$triggerValue}";
						if ($triggerValue == $input[$triggerParam->getName()]) {
							$allowed = true;
						}
					}
					else {
						$errorMessage .= "<br/>&nbsp;{$triggerParam->getName()} is set";
						if (isset($input[$triggerParam->getName()]) && ($input[$triggerParam->getName()])) {
							$allowed = true;
						}
					}
				}

				if (!$allowed) {
					throw new ScriptException($errorMessage);
				}
			}
			$this->setValue($input[$this->name]);
		}
	}

	public function requireIf(ParameterI $trigger = null, $value = "") {
		if (!$trigger) {
			$this->isAlwaysRequired = true;
			return;
		}
		$this->requiringTriggers[] = array ("parameter" => $trigger, "value" => $value);
	}
	public function excludeButAllowIf(ParameterI $trigger = null, $value = "") {
		$this->isEverExcluded = true;
		if ($trigger) {
			$this->allowingTriggers[] = array("parameter" => $trigger, "value" => $value);
		}
	}
	public function linkTo(ParameterI $parameter) {
		$eitherOr = new EitherOrParameter($this, $parameter);
		return $eitherOr;
	}
}
