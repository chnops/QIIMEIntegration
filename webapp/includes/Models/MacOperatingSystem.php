<?php

namespace Models;

class MacOperatingSystem implements OperatingSystemI {
	
	private $relativeReference = "";

	public function getRelativeReference() {
		return $this->relativeReference;
	}
	public function setRelativeReference($reference) {
		$this->relativeReference = $reference;
	}
	public function createDir($name) {
		system("mkdir " . $this->relativeReference . $name);
	}
	public function getDirContents($name) {
		system("ls " . $name);
	}
}
