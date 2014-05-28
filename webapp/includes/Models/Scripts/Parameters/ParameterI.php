<?php

namespace Models\Scripts\Parameters;

interface ParameterI {
	public function renderForOperatingSystem();
	public function renderForForm($disabled);
	public function isValueValid($value);
	public function setValue($value);
	public function getValue();
	public function setName($name);
	public function getName();
}
