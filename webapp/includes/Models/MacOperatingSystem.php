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
				throw new OperatingSystemException("Invalid file name: " . htmlentities($name));
			}
		}

		$returnCode = 0;
		system("mkdir " . $this->home . "/" . $name, $returnCode);

		if ($returnCode) {
			throw new OperatingSystemException("mkdir failed: {$returnCode}");
		}
	}

	public function getDirContents($name) {
		if (!$this->isValidFileName($name)) {
			throw new OperatingSystemException("tried to list contents of invalid file");
		}

		$result = 0;
		ob_start();
		$code = "ls {$this->home}/{$name}";
		system($code, $result);

		if ($result) {
			ob_end_clean();
			throw new OperatingSystemException("ls failed.  See error log");
		}
		$output = ob_get_clean();
		$output = trim($output);
		if ($output) {
			$output = explode("\n", $output);
		}
		else {
			$output = array();
		}
		return $output;
	}

	public function executeArbitraryScript($environmentSource, $runDirectory, $script) {
		$code = "source {$environmentSource}; 
			if [ $? -ne 0 ]; then echo 'Unable to source environment: {$environmentSource}'; exit 1; fi;
			cd {$this->home}/{$runDirectory};
			if [ $? -ne 0 ]; then echo 'Requested directory cannot be found: {$this->home}{$runDirectory}'; exit 1; fi;
		   	{$script};";

		$returnValue = 0;
		ob_start();
		system($code, $returnValue);
		if ($returnValue) {
			$exception = new OperatingSystemException("An error occurred while executing script. Check the error log, or contact your system administrator.");
			$exception->setConsoleOutput(ob_get_clean());
			throw $exception;
		}
		return ob_get_clean();
	}
	public function isValidFileName($name) {
		// TODO implement this
		return true;
		// the database can only hold ints that are 11 digits long
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
