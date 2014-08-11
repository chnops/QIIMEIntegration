<?php

namespace Models;

class OperatingSystemException extends \Exception {
	private $consoleOutput;
	public function setConsoleOutput($output) {
		$this->consoleOutput = $output;
	}
	public function getConsoleOutput() {
		return $this->consoleOutput;
	}
}
