<?php

namespace Models\Scripts;

class ConditionallyRequiredParameter extends DefaultParameter {

	private $scriptId = "";
	private $actualParameter = NULL;
	public function __construct(ParameterI $parameter, $scriptId) {
		$this->actualParameter = $parameter;
		$this->scriptId = $scriptId;
	}

	public function renderForOperatingSystem() {
		return "";
	}
	public function renderForForm() {
		return "<p id=\"{$this->scriptId}_{$this->actualParameter->getName()}\" class=\"conditional_requirement\">" . 
			"<strong>{$this->actualParameter->getName()}</strong> is currently required.</p>";
	}
	public function getValue() {
		return "";
	}
}
