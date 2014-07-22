<?php

namespace Models\Scripts\Parameters;
use \Models\Scripts\ScriptException;

class DefaultParameter implements ParameterI {
	protected $name;
	protected $value;

	private $isAlwaysRequired = false;
	private $requiringTriggers = array();
	private $dismissingTriggers = array();

	private $isEverExcluded = false;
	private $allowingTriggers = array();
	private $excludingTriggers = array();

	private $isATrigger = false;

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
	public function renderForForm($disabled, \Models\Scripts\ScriptI $script) {
		$disabledString = ($disabled) ? " disabled" : "";
		return "<label for=\"{$this->name}\">{$this->name} <a class=\"param_help\" id=\"{$this->getJsVar($script->getJsVar())}\">&amp;</a>
			<input type=\"text\" name=\"{$this->name}\" value=\"{$this->value}\"{$disabledString}/></label>";
	}
	public function renderFormScript($formJsVar, $disabled) {
		if ($disabled) {
			return "";
		}
		$parameterJsVar = $this->getJsVar($formJsVar);
		$code = "var {$parameterJsVar} = {$formJsVar}.find(\"[name={$this->name}]\");";
		
		if ($this->isAlwaysRequired) {
			$code .= "requireParam({$parameterJsVar});";
		}
		if ($this->isATrigger()) {
			$code .= "makeTrigger({$parameterJsVar});";
		}
		if (!empty($this->allowingTriggers) || !empty($this->requiringTriggers) ||
			!empty($this->excludingTriggers)) {
			$code .= "makeDependent({$parameterJsVar});";
		}
		$triggersUnique = array();
		$relationshipCode = "";
		foreach ($this->allowingTriggers as $allowingTrigger) {
			$triggerJsVar = $allowingTrigger['parameter']->getJsVar($formJsVar);
			$triggersUnique[] = $triggerJsVar;

			$triggerName = $allowingTrigger['parameter']->getName();
			$triggerValue = $allowingTrigger['value'];
			if ($triggerValue === false) {
				$triggerValue = "false";
			}
			else if ($triggerValue === true) {
				$triggerValue = "true";
			}
			else {
				$triggerValue = "'{$triggerValue}'";
			}
			$relationshipCode .= "{$parameterJsVar}.allowOn('{$triggerName}', {$triggerValue});";
		}
		foreach ($this->requiringTriggers as $requiringTrigger) {
			$triggerJsVar = $requiringTrigger['parameter']->getJsVar($formJsVar);
			$triggersUnique[] = $triggerJsVar;

			$triggerName = $requiringTrigger['parameter']->getName();
			$triggerValue = $requiringTrigger['value'];
			if ($triggerValue === false) {
				$triggerValue = "false";
			}
			else if ($triggerValue === true) {
				$triggerValue = "true";
			}
			else {
				$triggerValue = "'{$triggerValue}'";
			}
			$relationshipCode .= "{$parameterJsVar}.requireOn('{$triggerName}', {$triggerValue});";
		}
		foreach ($this->excludingTriggers as $excludingTrigger) {
			$triggerJsVar = $excludingTrigger['parameter']->getJsVar($formJsVar);
			$triggersUnique[] = $triggerJsVar;

			$triggerName = $excludingTrigger['parameter']->getName();
			$triggerValue = $excludingTrigger['value'];
			if ($triggerValue === false) {
				$triggerValue = "false";
			}
			else if ($triggerValue === true) {
				$triggerValue = "true";
			}
			else {
				$triggerValue = "'{$triggerValue}'";
			}
			$relationshipCode .= "{$parameterJsVar}.excludeOn('{$triggerName}', {$triggerValue});";
		}
		$triggersUnique = array_unique($triggersUnique);
		foreach ($triggersUnique as $trigger) {
			$code .= "{$parameterJsVar}.listenTo({$trigger});";
		}

		return $code . $relationshipCode . "\n";
	}
	public function getJsVar($formJsVar) {
		return $formJsVar . "_" . preg_replace("/-/", "_", preg_replace("/--/", "", $this->name));
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
			$this->setValue(false);

			$errorMessage = "";
			if ($this->isAlwaysRequired) {
				$errorMessage = "A required parameter was not found: {$this->name}";
			}
			else {
				foreach ($this->requiringTriggers as $trigger) {
					$triggerParam = $trigger['parameter'];
					$triggerValue = $trigger['value'];

					if ($triggerValue === false) {
						$isRequired = !isset($input[$triggerParam->getName()]);
					}
					else if ($triggerValue === true) {
						$isRequired = isset($input[$triggerParam->getName()]);
					}
					else {
						$isRequired = ($input[$triggerParam->getName()] == $triggerValue);
					}
	
					if ($isRequired) {
						$errorMessage = ((!$errorMessage) ? "A required parameter was not found: {$this->name}" : "") .
						   	"<br/>&nbsp;- required by {$triggerParam->getName()}";
					}
				}
			}
			if ($errorMessage) {
				$isDismissed = false;
				foreach ($this->dismissingTriggers as $trigger) {
					$triggerParam = $trigger['parameter'];
					$triggerValue = $trigger['value'];

					if ($triggerValue === false) {
						if (!isset($input[$triggerParam->getName()])) {
							$isDismissed = true;
						}
					}
					else if ($triggerValue === true) {
						if (isset($input[$triggerParam->getName()])) {
							$isDismissed = true;
						}
					}
					else {
						if ($input[$triggerParam->getName()] == $triggerValue) {
							$isDismissed = true;
						}
					}
				}
				if (!$isDismissed) {
					throw new ScriptException($errorMessage);
				}
			}
		}
		else {
			$this->setValue($input[$this->name]);
			if ($this->isEverExcluded) {
				$isAllowed = false;
				$errorMessage = (empty($this->allowingTriggers)) ? 
					"The parameter {$this->name} is not allowed" :
					"The parameter {$this->name} is only allowed when:";

				foreach ($this->allowingTriggers as $trigger) {
					$triggerParam = $trigger['parameter'];
					$triggerValue = $trigger['value'];

					if ($triggerValue === false) {
						$errorMessage .= "<br/>&nbsp;{$triggerParam->getName()} is not set";
						if (!isset($input[$triggerParam->getName()])) {
							$isAllowed = true;
						}
					}
					else if($triggerValue === true) {
						$errorMessage .= "<br/>&nbsp;{$triggerParam->getName()} is set to anything";
						if (isset($input[$triggerParam->getName()]) && ($input[$triggerParam->getName()])) {
							$isAllowed = true;
						}
					}
					else {
						$errorMessage .= "<br/>&nbsp;{$triggerParam->getName()} is set to {$triggerValue}";
						if ($triggerValue == $input[$triggerParam->getName()]) {
							$isAllowed = true;
						}
					}
				}

				$errorMessageExtended = "";
				foreach ($this->excludingTriggers as $trigger) {
					$triggerParam = $trigger['parameter'];
					$triggerValue = $trigger['value'];

					if ($triggerValue === false) {
						$errorMessageExtended .= "<br/>&nbsp;{$triggerParam->getName()} is not set";
						if (!isset($input[$triggerParam->getName()])) {
							$isAllowed = false;
						}
					}
					else if ($triggerValue === true) {
						$errorMessageExtended .= "<br/>&nbsp;{$triggerParam->getName()} is set to anything";
						if (isset($input[$triggerParam->getName()])) {
							$isAllowed = false;
						}
					}
					else {
						$errorMessageExtended .= "<br/>&nbsp;{$triggerParam->getName()} is set to {$triggerValue}";
						if ($triggerValue == $input[$triggerParam->getName()]) {
							$isAllowed = false;
						}
					}
				}
				if ($errorMessageExtended) {
					$errorMessage .= (empty($this->allowingTriggers)) ? 
						" when:" :
						"However, it is not allowed when:";
					$errorMessage .= $errorMessageExtended;
				}

				if (!$isAllowed) {
					throw new ScriptException($errorMessage);
				}
			}
		}
	}

	public function requireIf(ParameterI $trigger = NULL, $value = true) {
		if (!$trigger) {
			$this->isAlwaysRequired = true;
			return;
		}
		$this->requiringTriggers[] = array ("parameter" => $trigger, "value" => $value);
		$trigger->isATrigger(true);
	}
	public function dismissIf(ParameterI $trigger, $value = true) {
		$this->dismissingTriggers[] = array("parameter" => $trigger, "value" => $value);
		$trigger->isATrigger(true);
	}
	public function excludeButAllowIf(ParameterI $trigger = NULL, $value = true) {
		$this->isEverExcluded = true;
		if ($trigger) {
			$this->allowingTriggers[] = array("parameter" => $trigger, "value" => $value);
			$trigger->isATrigger(true);
		}
	}
	public function excludeIf(ParameterI $trigger = NULL, $value = true) {
		$this->isEverExcluded = true;
		$this->excludingTriggers[] = array("parameter" => $trigger, "value" => $value);
		$trigger->isATrigger(true);
	}
	public function linkTo(ParameterI $parameter, $displayName = "") {
		$eitherOr = new EitherOrParameter($this, $parameter, $displayName);
		return $eitherOr;
	}
	public function isATrigger($isIt = -1) {
		if ($isIt === -1) {
			return $this->isATrigger;
		}
		if ($isIt) {
			$this->isATrigger = true;
		}
		else {
			$this->isATrigger = false;
		}
	}
}
