<?php

namespace Models\Scripts\Parameters;

class NewFileParameter extends DefaultParameter { 
	private $isDir = false;
	public function __construct($name, $value, $isDir = false) {
		$this->name = $name;
		$this->value = $value;
		$this->isDir = $isDir;
	}
	public function setIsDir($isDir) {
		if($isDir) {
			$this->isDir = true;
		}
		else {
			$this->isDir = false;
		}
	}
	public function isDir() {
		return $this->isDir;
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
		}
		return parent::renderForOperatingSystem();
	}
}
