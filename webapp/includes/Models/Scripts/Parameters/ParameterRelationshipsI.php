<?php

namespace Models\Scripts\Parameters;

interface ParameterRelationshipsI {
	// help or version
	public function excludeParam(ParameterI $parameter);
	public function requireParam(ParameterI $parameter);

	// i.e. not allowed otherwise	
	public function allowParamIf(ParameterI $allowed, ParameterI $allower, $value);
	public function requireParamIf(ParameterI $required, ParameterI $requirer, $value);

	public function linkParams(ParameterI $default, ParameterI $alternative);

	// output
	public function makeOptional(array $additionalParams);
	public function getSortedParameters();
	public function renderFormCode(\Models\Scripts\ScriptI $script);
	// input
	public function getViolations($input);
}
