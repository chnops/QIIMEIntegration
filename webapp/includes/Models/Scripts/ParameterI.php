<?php

namespace Models\Scripts;

interface ParameterI {

	public function __construct($name, $value, $isRequired = false);
	public function renderForOperatingSystem();
	public function renderForForm();
	public function setValue($value);
	public function getValue();
	public function setName($name);
	public function getName();
	public function setIsRequired($isRequired);
	public function isRequired();
}
