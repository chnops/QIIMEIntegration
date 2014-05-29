<?php

namespace Models\Scripts\Parameters;

interface ParameterRelationshipsI {
	// output
	public function makeOptional(array $additionalParams);
	public function getSortedParameters();
	public function renderFormCode(\Models\Scripts\ScriptI $script);
}
