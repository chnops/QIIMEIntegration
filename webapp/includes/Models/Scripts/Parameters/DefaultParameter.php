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

	public function getJsVar($formJsVar) {
		return $formJsVar . "_" . preg_replace("/-/", "_", preg_replace("/--/", "", $this->name));
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

	public function acceptInput(array $input) {
		if (!isset($input[$this->name]) || !$input[$this->name]) {
			$this->setValue(false);

			if ($this->isAlwaysRequired()) {
				throw new ScriptException("The parameter {$this->name} is required");
			}

			$activeDismissers= $this->getActiveTriggers($this->dismissingTriggers, $input);
			if (empty($activeDismissers)) {
				$activeRequirers = $this->getActiveTriggers($this->requiringTriggers, $input);
				if($activeRequirers) {
					$errorMessage = "The parameter {$this->name} is required when:";
					foreach ($activeRequirers as $requirer) {
						$errorMessage .= "<br/>&nbsp;- {$requirer['parameter']->getName()} is set to {$requirer['value']}";
					}
					throw new ScriptException($errorMessage);
				}
			}
		}
		else {
			$this->setValue($input[$this->name]);
			if ($this->isEverExcluded()) {
				$activeExcluders = $this->getActiveTriggers($this->getExcludingTriggers(), $input);
				if (!empty($activeExcluders)) {
					$errorMessage = "The parameter {$this->name} cannot be used when:";
					foreach($activeExcluders as $excluder) {
						$errorMessage .= "<br/>&nbsp;- {$excluder['parameter']->getName()} is set to {$excluder['value']}";
					}
					throw new ScriptException($errorMessage);
				}
				$activeAllowers = $this->getActiveTriggers($this->getAllowingTriggers(), $input);
				if (empty($activeAllowers)) {
					$errorMessage = "The parameter {$this->name} can only be used when:";
					foreach($this->getAllowingTriggers() as $potentialAllower) {
						$errorMessage .= "<br/>&nbsp;- {$potentialAllower['parameter']->getName()} is set to {$potentialAllower['value']}";
					}
					throw new ScriptException($errorMessage);
				}
			}
		}
	}

	public function getActiveTriggers(array $allTriggers, array $input) {
		$activeTriggers = array();
		foreach ($allTriggers as $trigger) {
			$triggerParam = $trigger['parameter'];
			$triggerValue = $trigger['value'];

			if ($triggerValue === false && !isset($input[$triggerParam->getName()])) {
					$activeTriggers[] = $trigger;
			}
			else if ($triggerValue === true && isset($input[$triggerParam->getName()])) {
					$activeTriggers[] = $trigger;
			}
			else if (isset($input[$triggerParam->getName()]) && ($input[$triggerParam->getName()] === $triggerValue)) {
					$activeTriggers[] = $trigger;
			}
		}
		return $activeTriggers;
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
	public function excludeIf(ParameterI $trigger, $value = true) {
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
	public function isAlwaysRequired() {
		return $this->isAlwaysRequired;
	}
	public function setIsAlwaysRequired($isIt) {
		if ($isIt) {
			$this->isAlwaysRequired = true;
		}
		else {
			$this->isAlwaysRequired = false;
		}
	}

	public function getRequiringTriggers() {
		return $this->requiringTriggers;
	}
	public function setRequiringTriggers(array $triggers) {
		$this->requiringTriggers = $triggers;
	}

	public function getDismissingTriggers() {
		return $this->dismissingTriggers;
	}
	public function setDismissingTriggers(array $triggers) {
		$this->dismissingTriggers = $triggers;
	}

	public function isEverExcluded() {
		return $this->isEverExcluded;
	}
	public function setIsEverExcluded($isIt) {
		if ($isIt) {
			$this->isEverExcluded = true;
		}
		else {
			$this->isEverExcluded = false;
		}
	}

	public function getAllowingTriggers() {
		return $this->allowingTriggers;
	}
	public function setAllowingTriggers(array $triggers) {
		$this->allowingTriggers = $triggers;
	}

	public function getExcludingTriggers() {
		return $this->excludingTriggers;
	}
	public function setExcludingTriggers(array $triggers) {
		$this->excludingTriggers = $triggers;
	}
}
