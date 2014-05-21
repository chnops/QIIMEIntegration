<?php

namespace Models;

class MacOperatingSystem implements OperatingSystemI {
	// since all scripts execute in webapp/index.php,	
	private $home = "./projects/";

	public function getHome() {
		return $this->home;
	}
	public function createDir($name) {
		// TODO kindly strip preceding slash
		$nameParts = explode("/", $name);
		foreach ($nameParts as $namePart) {
			if (!$this->isValidFileName($namePart)) {
				throw new OperatingSystemException("Invalid file name: {$name}");
			}
		}

		$returnCode = 0;
		system("mkdir " . $this->home . "/" . $name, $returnCode);

		if ($returnCode) {
			throw new OperatingSystemException("mkdir failed: {$returnCode}");
		}
	}

	public function executeArbitraryScript($environmentSource, $runDirectory, $script) {
		$code = "source {$environmentSource}; cd {$this->home}/{$runDirectory}; {$script}";

		$returnValue = 0;
		ob_start();
		system($code, $returnValue);
		if ($returnValue) {
			ob_end_clean();
			throw new OperatingSystemException("An error occurred while executing script. Check the error log, or contact your system administrator.");
		}
		return ob_get_clean();
	}
	public function isValidFileName($name) {
		// the database can only hold ints that are 11 digits long
		// TODO is that true?
		if (strlen($name) > 11) {
			return false;
		}
		// for now anyway, all file names will be integers greater than 0; that's safe
		$nameCopy = $name;
		$nameCopy++;
		$nameCopy--;
		if ($nameCopy != $name) {
			return false;
		}
		if ($name <= 0) {
			return false;
		}
		return true;
	}
}
