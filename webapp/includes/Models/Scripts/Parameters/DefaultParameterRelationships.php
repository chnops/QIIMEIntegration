<?php

namespace Models\Scripts\Parameters;

class DefaultParameterRelationships implements ParameterRelationshipsI {
	// TODO allow parameter or array
	
	private $allParameters = array();

	private $alwaysExcluded = array();
	public function excludeParam(ParameterI $parameter) {
		$this->allParameters[$parameter->getName()] = $parameter;
		$this->alwaysExcluded[] = $parameter->getName();
	}
	private $alwaysRequired = array();
	public function requireParam(ParameterI $parameter) {
		$this->allParameters[$parameter->getName()] = $parameter;
		$this->alwaysRequired[] = $parameter->getName();
	}

	private $defaults = array();
	public function addDefaultForParam(ParameterI $parameter, $default) {
		$this->allParameters[$parameter->getName()] = $parameter;
		$this->defaults[$parameter->getName()] = $default;
	}
	public function incorporateDefaults($values) {
		foreach ($this->defaults as $argName => $default) {
			if (!isset($values[$argName])) {
				$values[$argName] = $default;
			}
		}
		foreach ($this->links as $link) {
			$eitherOr = $link[0];
			$defaultName = $link[1];
			$alternativeName = $link[2];
			if (isset($values[$defaultName])) {
			   	if (isset($values[$alternativeName])) {
					$this->linkViolations[] = "You can choose either {$defaultName} or {$alternativeName}; you cannot have both.";
				}
				else {
					$eitherOr->setValue($defaultName);
					$eitherOr->getDefault()->setValue($values[$defaultName]);
				}
			}
			else if (isset($values[$alternativeName])) {
				$eitherOr->setValue($alternativeName);
				$eitherOr->getAlternative()->setValue($values[$alternativeName]);
			}
			unset($values[$defaultName]);
			unset($values[$alternativeName]);
		}
		return $values;
	}

	private $triggers = array();
	private $usuallyExcluded = array();
	private $conditionalAllowers = array();
	public function allowParamIf(ParameterI $allowed, ParameterI $allower, $value) {
		$this->allParameters[$allowed->getName()] = $allowed;
		$this->allParameters[$allower->getName()] = $allower;

		$this->triggers[] = $allower->getName();
		$this->usuallyExcluded[] = $allowed->getName();
		$this->conditionalAllowers[$allower->getName()][$value][] = $allowed->getName();
	}

	private $usuallyOptional = array();
	private $conditionalRequirers = array();
	public function requireParamIf(ParameterI $required, ParameterI $requirer, $value) {
		$this->allParameters[$required->getName()] = $required;
		$this->allParameters[$requirer->getName()] = $requirer;

		$this->triggers[] = $requirer->getName();
		$this->usuallyOptional[] = $required->getName();
		$this->conditionalRequirers[$requirer->getname()][$value][] = $required->getName();
	}

	private $links = array();
	private $linkViolations = array();
	public function linkParams(ParameterI $default, ParameterI $alternative) {
		$eitherOr = new EitherOrParameter($default, $alternative);
		$this->allParameters[$eitherOr->getName()] = $eitherOr;
		$this->links[] = array($eitherOr, $default->getName(), $alternative->getName());
		return $eitherOr;
	}

	public function makeOptional(array $parameters) {
		$this->allParameters = array_merge($this->allParameters, $parameters);
		return $this->allParameters;
	}

	public function getSortedParameters() {
		$sortedParameters = array();

		// excluded parameters first
		foreach ($this->alwaysExcluded as $name) {
			$sortedParameters[$name] = $this->allParameters[$name];
			unset($this->allParameters[$name]);
		}

		$sortedParameters[] = new Label("<p><strong>Required Parameters</strong></p>");
		foreach ($this->alwaysRequired as $name) {
			$sortedParameters[$name] = $this->allParameters[$name];
			unset($this->allParameters[$name]);
		}
		foreach ($this->usuallyOptional as $paramName) {
			$sortedParameters[] = new Label("<div for=\"{$paramName}\">{$paramName} is now required</div>");
		}

		$sortedParameters[] = new Label("<p><strong>Optional Parameters</strong></p>");
		$triggers = array_unique($this->triggers);
		asort($triggers);
		foreach ($triggers as $trigger) {
			$sortedParameters[$trigger] = $this->allParameters[$trigger];
			unset($this->allParameters[$trigger]);

			if (isset($this->conditionalRequirers[$trigger])) {
				$values = $this->conditionalRequirers[$trigger];
				foreach ($values as $value => $associatedParameterNames) {
					foreach ($associatedParameterNames as $parameterName) {
						$sortedParameters[$parameterName] = $this->allParameters[$parameterName];
						unset($this->allParameters[$parameterName]);
					}
				}
			}

			if (isset($this->conditionalAllowers[$trigger])) {
				$values = $this->conditionalAllowers[$trigger];
				foreach ($values as $value => $associatedParameterNames) {
					foreach ($associatedParameterNames as $parameterName) {
						if (isset($this->allParameters[$parameterName])) {
							$sortedParameters[$parameterName] = $this->allParameters[$parameterName];
							unset($this->allParameters[$parameterName]);
						}
					}
				}
			}
		}
		$this->triggers = $triggers;

		// follow up with the rest of the optional ones
		foreach ($this->allParameters as $name => $parameter) {
			$sortedParameters[$name] = $parameter;
		}
		$this->allParameters = $sortedParameters;
		return $this->allParameters;
	}

	public function getViolations($input) {
		$violations = array();
		foreach ($this->alwaysExcluded as $name) {
			if (isset($input[$name])) {
				$violations[] = "Parameter {$name} should not have a value, but does";
			}
		}
		foreach ($this->alwaysRequired as $name) {
			if (!isset($input[$name])) {
				$violations[] = "A required parameter was not found: {$name}";
			}
		}

		foreach ($this->conditionalRequirers as $trigger => $requiredParams) {
			foreach ($requiredParams as $value => $paramNames) {
				if ($input[$trigger] == $value) {
					foreach ($paramNames as $paramName) {
						if (!isset($input[$paramName])) {
							$violations[] = "If parameter {$trigger} is set to {$value}, {$paramName} is required";
						}
					}
				}
			}
		}

		$usuallyExcluded = array_unique($this->usuallyExcluded);	
		foreach ($usuallyExcluded as $excludedParam) {
			if (isset($input[$excludedParam])) {
				$triggersAndValues = $this->getOptionallyAllowedParamTriggersAndValues($excludedParam);
				$allowed = false;
				$errorMessage = "The parameter {$excludedParam} can only be used under certain circumstances, when:<br/>";
				foreach ($triggersAndValues as $triggerValuePair) {
					$errorMessage .= "&nbsp;parameter {$triggerValuePair['trigger']} is set to {$triggerValuePair['value']}<br/>";
					if ($input[$triggerValuePair['trigger']] == $triggerValuePair['value']) {
						$allowed = true;
					}
				}
				if (!$allowed) {
					$violations[] = $errorMessage;
				}
			}
		}

		return array_merge($violations, $this->linkViolations);
	}

	public function renderFormCode(\Models\Scripts\ScriptI $script) {
		$htmlId = $script->getHtmlId();
		$formVariable = $htmlId . "_form";
		$formCode = "<script type=\"text/javascript\">\nvar {$formVariable} = $('div#form_{$htmlId} form');\n";

		foreach ($this->alwaysRequired as $requiredParamName) {
			$formCode .= "{$formVariable}.find(\"label[for='{$requiredParamName}']\").css('color', '#cc0000').css('font-weight', 'bold');";
		}
		$formCode .= "\n";
		foreach ($this->usuallyOptional as $requiredParamName) {
			$formCode .= "{$formVariable}.find(\"div[for='{$requiredParamName}']\").css('color', '#cc0000').css('font-weight', 'bold').css('display', 'none');";
		}
		$formCode .= "\n";

		foreach ($this->triggers as $trigger) {
			$triggerVariable = preg_replace("/--/", "", $trigger);
			$formCode .= "var {$triggerVariable} = {$formVariable}.find(\"[name='{$trigger}']\");";

			if (isset($this->conditionalRequirers[$trigger])) {
				$conditionalParams = $this->conditionalRequirers[$trigger];
				foreach ($conditionalParams as $value => $paramNames) {
					$quotedArray = array();
					foreach ($paramNames as $name) {
						$quotedArray[] = "{$formVariable}.find(\"div[for='{$name}']\")";
					}
					$formCode .= "{$triggerVariable}['{$value}_requires'] = [" . implode(",", $quotedArray) . "];";
				}
				$formCode .= "{$triggerVariable}.change(function() {
					{$formVariable}.find('div[for]').css('display', 'none');
					var requiredLabels = {$triggerVariable}[{$triggerVariable}.val() + '_requires'];
					if (requiredLabels) {
						jQuery.each(requiredLabels, function(index, value) {value.css('display', 'block')});
					}
					});";
			}
			if (isset($this->conditionalAllowers[$trigger])) {
				$allConditionalParams = array();
				foreach ($this->usuallyExcluded as $excludedParam) {
					$allConditionalParams[] = "'{$excludedParam}'";
				}
				$formCode .= "{$triggerVariable}['usually_excluded'] = [" . implode(",", $allConditionalParams) . "];";

				$conditionalParamsByValue = $this->conditionalAllowers[$trigger];
				foreach ($conditionalParamsByValue as $value => $paramNames) {
					$quotedArray = array();
					foreach ($paramNames as $name) {
						$quotedArray[] = "'{$name}'";
					}
					$formCode .= "{$triggerVariable}['{$value}_allows'] = [" . implode(",", $quotedArray) . "];";
				}
				$formCode .= "{$triggerVariable}.change(function() {
					jQuery.each({$triggerVariable}['usually_excluded'], function(index, value) {
						{$formVariable}.find('[name=\"' + value + '\"]').prop('disabled', true).parents('label').css('display', 'none');
					});
					var allowedParameters = {$triggerVariable}[{$triggerVariable}.val() + '_allows'];
					if (allowedParameters) {
						jQuery.each(allowedParameters, function(index, value) {
							{$formVariable}.find('[name=\"' + value + '\"]').prop('disabled', false).parents('label').css('display', 'block')
						});
					}
					});";
			}
			$formCode .= "{$triggerVariable}.change();";
		}
		$formCode .= "\n";

		foreach ($this->links as $link) {
			$linkVariable = preg_replace("/\-/", "", $link[0]->getName());
			$defaultValue = ($link[0]->getValue()) ? $link[0]->getValue() : $link[1];
			$formCode .= "var {$linkVariable} = {$formVariable}.find(\"input[name='{$link[0]->getName()}']\");\n";
			$formCode .= "{$linkVariable}.each(function(index, value) {
				$(this).eq(index-1).change(function() {
				$(this).parents(\"table\").find(\"td:nth-child(2) [name]\").prop('disabled', true);
				$(this).parents(\"tr\").find(\"td:nth-child(2) [name]\").prop('disabled', false);
			});});
				{$formVariable}.find(\"input[value='{$defaultValue}']\").click();
				";
		}

		$formCode .= "</script>";
		return $formCode;
	}

	private function getOptionallyAllowedParamTriggersAndValues($param) {
		$triggersAndValues = array();
		foreach ($this->conditionalAllowers as $trigger => $conditionalParams) {
			foreach ($conditionalParams as $value => $paramNames) {
				foreach ($paramNames as $paramName) {
					if ($paramName == $param) {
						$triggersAndValues[] = array("trigger" => $trigger, "value" => $value);
					}
				}
			}
		}
		return $triggersAndValues;
	}
}
