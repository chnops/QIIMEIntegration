<?php

namespace Models;

class MacOperatingSystem implements OperatingSystemI {

	// since all scripts execute in webapp/index.php,	
	private $home = "./projects/";
	public function overwriteHome($newHome) {
		if ($newHome != './data/projects') {
			error_log("WARNING: This should only be used in testing contexts.  It could be disasrous to use it otherwise.");
		}
		$this->home = $newHome;
	}

	public function getHome() {
		return $this->home;
	}
	public function createDir($name) {
		if (!$this->isValidFileName($name)) {
			throw new OperatingSystemException("Invalid file name: {$name}");
		}

		$returnCode = 0;
		system("mkdir " . $this->home . "/" . $name, $returnCode);

		if ($returnCode) {
			throw new OperatingSystemException("mkdir failed: {$returnCode}");
		}
	}

	public function executeArbitraryScript($script) {
		system("source '/macqiime/configs/bash_profile.txt'; cd ./projects/1/; {$script}");		
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
