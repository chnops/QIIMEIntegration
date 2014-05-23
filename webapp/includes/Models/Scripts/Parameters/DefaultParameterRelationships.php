<?php

namespace Models\Scripts\Parameters;

class DefaultParameterRelationships implements ParameterRelationshipsI {
	
	private $allParameters = array();
	private $defaults = array();
	private $alwaysExcluded = array();
	private $alwaysRequired = array();
	
	public function excludeParam(ParameterI $parameter) {
		$this->allParameters[$parameter->getName()] = $parameter;
		$this->alwaysExcluded[] = $parameter->getName();
	}
	public function requireParam(ParameterI $parameter) {
		$this->allParameters[$parameter->getName()] = $parameter;
		$this->alwaysRequired[] = $parameter->getName();
	}

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

	// TODO allow parameter or array
	public function allowParamIf(ParameterI $allowed, ParameterI $allower, $value) {
		$this->allParameters[$allowed->getName()] = $allowed;
		$this->allParameters[$allower->getName()] = $allower;
	}
	public function requireParamIf(ParameterI $required, ParameterI $requirer, $value) {
		$this->allParameters[$required->getName()] = $required;
		$this->allParameters[$requirer->getName()] = $requirer;
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
		return $violations;
	}
}
