<?php

namespace Models\Scripts\Parameters;

class DefaultParameterRelationships implements ParameterRelationshipsI {
	
	private $allParameters = array();
	
	public function excludeParam(ParameterI $parameter) {
		$this->allParameters[] = $parameter;
	}
	public function requireParam(ParameterI $parameter) {
		$this->allParameters[] = $parameter;
	}

	public function addDefaultForParam(ParameterI $parameter, $default) {
		$this->allParameters[] = $parameter;
	}

	// TODO allow parameter or array
	public function allowParamIf(ParameterI $allowed, ParameterI $allower, $value) {
		$this->allParameters[] = $allowed;
	}
	public function requireParamIf(ParameterI $required, ParameterI $requirer, $value) {
		$this->allParameters[] = $required;
	}

	public function linkParams(ParameterI $default, ParameterI $alternative) {
		$this->allParameters[] = $default;
		$this->allParameters[] = $alternative;
	}

	public function makeOptional(array $parameters) {
		$this->allParameters = array_merge($this->allParameters, $parameters);
		return $this->allParameters;
	}

	public function getSortedParameters() {
		return $this->allParameters;
	}

	public function getViolations() {
		return array();
	}
}

