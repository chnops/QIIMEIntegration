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
	private $conditionallyRequired = array();
	public function requireParamIf(ParameterI $required, ParameterI $requirer, $value) {
		$this->allParameters[$required->getName()] = $required;
		$this->allParameters[$requirer->getName()] = $requirer;

		$this->usuallyOptional[] = $required->getName();
		$this->conditionallyRequired[$requirer->getname()][$value][] = $required->getName();
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
		// required parameters next
		foreach ($this->alwaysRequired as $name) {
			$sortedParameters[$name] = $this->allParameters[$name];
			unset($this->allParameters[$name]);
		}

		// parameters that trigger a conditionally required parameter
		foreach ($this->conditionallyRequired as $trigger => $values) {
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

		foreach ($this->conditionallyRequired as $trigger => $requiredParams) {
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

	/* TODO this is actually more appropriate for condtionally allowed parameters
	private function getOptionallyRequiredParamTriggerAndValue($param) {
		foreach ($this->conditionallyRequired as $trigger => $conditionalParams) {
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
