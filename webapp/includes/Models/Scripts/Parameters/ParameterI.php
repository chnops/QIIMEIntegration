<?php

namespace Models\Scripts\Parameters;

interface ParameterI {
	public function renderForOperatingSystem();
	public function renderForForm($disabled, \Models\Scripts\ScriptI $script);
	public function renderFormScript($formJsVar, $disabled);
	public function getJsVar($formJsVar);

	public function isValueValid($value);
	public function setValue($value);
	public function getValue();
	public function setName($name);
	public function getName();

	public function acceptInput(array $input);

	public function requireIf(ParameterI $trigger = NULL, $value = "");
	public function dismissIf(ParameterI $trigger, $value = "");
	public function excludeButAllowIf(ParameterI $trigger = NULL, $value = "");
	public function excludeIf(ParameterI $trigger, $value = "");
	public function linkTo(ParameterI $parameter);
	public function isATrigger($isIt = -1);
}
