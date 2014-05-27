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
		return $values;
	}

	public function allowParamIf(ParameterI $allowed, ParameterI $allower, $value) {
		$this->allParameters[$allowed->getName()] = $allowed;
		$this->allParameters[$allower->getName()] = $allower;
	}

	private $usuallyOptional = array();
	private $conditionalRequirers = array();
	public function requireParamIf(ParameterI $required, ParameterI $requirer, $value) {
		$this->allParameters[$required->getName()] = $required;
		$this->allParameters[$requirer->getName()] = $requirer;

		$this->usuallyOptional[] = $required->getName();
		$this->conditionalRequirers[$requirer->getname()][$value][] = $required->getName();
	}

	public function linkParams(ParameterI $default, ParameterI $alternative) {
		$this->allParameters[$default->getName()] = $default;
		$this->allParameters[$alternative->getName()] = $alternative;
		// TODO return the linked parameter object, in case the person wants to require it
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
		foreach ($this->conditionalRequirers as $trigger => $values) {
			$sortedParameters[$trigger] = $this->allParameters[$trigger];
			unset($this->allParameters[$trigger]);
			foreach ($values as $value => $associatedParameterNames) {
				foreach ($associatedParameterNames as $parameterName) {
					$sortedParameters[$parameterName] = $this->allParameters[$parameterName];
					unset($this->allParameters[$parameterName]);
				}
			}
		}

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
		/* TODO this is actually more appropriate for conditionally allowed parameters
		foreach ($this->usuallyOptional as $optionalParam) {
			if (isset($input[$optionalParam])) {
				$triggerAndValue = $this->getOptionallyRequiredParamTriggerAndValue($optionalParam);
				if ($input[$triggerAndValue['trigger']] != $triggerAndValue['value']) {
					$violations[] = "The parameter {$optionalParam} can only be used if the parameter {$triggerAndValue['trigger']}
				 		is set equal to {$triggerAndValue['value']}";
				}
			}
		}*/

		return $violations;
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

		foreach ($this->conditionalRequirers as $trigger => $conditionalParams) {
			$triggerVariable = preg_replace("/--/", "", $trigger);
			$formCode .= "var {$triggerVariable} = {$formVariable}.find(\"[name='{$trigger}']\");";

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
			});
			{$triggerVariable}.change();";
		}
		$formCode .= "\n";

		$formCode .= "</script>";
		return $formCode;
	}

	/* TODO this is actually more appropriate for condtionally allowed parameters
	private function getOptionallyRequiredParamTriggerAndValue($param) {
		foreach ($this->conditionalRequirers as $trigger => $conditionalParams) {
			foreach ($conditionalParams as $value => $paramNames) {
				foreach ($paramNames as $paramName) {
					if ($paramName == $param) {
						return array("value" => $value, "trigger" => $trigger);
					}
				}
			}
		}
		return array();
	}*/
}
