<?php

namespace Models\Scripts;

interface ParameterI {
	public function renderForOperatingSystem();
	public function renderForForm();
	public function isValueValid();
	public function setValue($value);
	public function getValue();
	public function setName($name);
	public function getName();
}
