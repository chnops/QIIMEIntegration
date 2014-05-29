<?php

namespace Models\Scripts\Parameters;

interface ParameterRelationshipsI {
	public function linkParams(ParameterI $default, ParameterI $alternative);

	// output
	public function makeOptional(array $additionalParams);
	public function getSortedParameters();
	public function renderFormCode(\Models\Scripts\ScriptI $script);
}
