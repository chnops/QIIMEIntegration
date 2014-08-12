<?php
/*
 * Copyright (C) 2014 Aaron Sharp
 * Released under GNU GENERAL PUBLIC LICENSE Version 3, 29 June 2007
 */

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
