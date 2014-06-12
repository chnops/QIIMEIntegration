<?php

namespace Models\Scripts\Parameters;

class NewFileParameter extends DefaultParameter { 
	private $isDir = false;
	public function __construct($name, $value, $isDir = false) {
		$this->name = $name;
		$this->value = $value;
		$this->isDir = $isDir;
	}
	public function isValueValid($value) {
		return !preg_match("/\"/", $value);
	}
	public function renderForOperatingSystem() {
		if ($this->isDir) {
			if ($this->value === false) {
				return "";
			}
			else if (!$this->value) {
				return ".";
			}
			$separator = (strlen($this->name) == 2) ? " " : "=";
			return $this->name . $separator . escapeshellarg($this->value);
		}
		else {
			return parent::renderForOperatingSystem();
		}
	}
}
