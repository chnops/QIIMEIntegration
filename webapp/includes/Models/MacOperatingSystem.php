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
			if (!$namePart) {
				continue;
			}
			if (!$this->isValidFileName($namePart)) {
				$exception = new OperatingSystemException("Unable to create directory");
				$exception->setConsoleOutput("Invalid file name: " . htmlentities($name));
				throw $exception;
			}
		}

		$returnCode = 0;
		system("mkdir " . $this->home . "/" . $name, $returnCode);

		if ($returnCode) {
			throw new OperatingSystemException("mkdir failed: {$returnCode}");
		}
	}
	public function removeDirIfExists($name) {
		$nameParts = explode("/", $name);
		foreach ($nameParts as $namePart) {
			if (!$namePart) {
				continue;
			}
			if (!$this->isValidFileName($namePart)) {
				return false;
			}
		}

		$returnCode = 0;
		ob_start();
		system("rmdir " . $this->home . $name, $returnCode);
		ob_end_clean();
		if ($returnCode != 0) {
			return false;
		}
		return true;
	}

	public function getDirContents($name) {
		$result = 0;
		ob_start();
		$code = "ls " . escapeshellarg($this->home . "/" . $name);
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
		$whitelist = array("uploads");
		if (in_array($name, $whitelist)) {
			return true;
		}
		$matchesRegex = preg_match("/^[A-z]\\d+$/", $name);
		if ($matchesRegex === false) {
			throw new \Exception("unable to check file name");
		}
		return $matchesRegex;
	}

	public function flattenDirsWithin($dirPath) {
		$contents = $this->getDirContents($dirPath);
		foreach ($contents as $currentFileName) {
			ob_start();
			system("if [ -d '{$this->home}{$dirPath}/{$currentFileName}' ]; then printf '1'; else printf '0'; fi;");
			$isDir = ob_get_clean();
			if ($isDir) {
				$this->flattenDir($dirPath . "/" . $currentFileName);
			}
		}
	}

	public function flattenDir($dirPath) {
		$fs = "%FS%";
		$fullDirPath = $this->home . $dirPath;
		$dirName = explode("/", $dirPath);
		$dirName = $dirName[count($dirName) - 1];

		$contents = $this->getDirContents($dirPath);
		foreach ($contents as $currentFileName) {
			ob_start();
			system("if [ -d '{$fullDirPath}/{$currentFileName}' ]; then printf '1'; else printf '0'; fi;");
			$isDir = ob_get_clean();
			if ($isDir) {
				$this->flattenDir($dirPath . "/" . $currentFileName);
			}
		}

		system("cd {$fullDirPath};for file in `ls`; do if [ -f \$file ]; then mv \$file ../{$dirName}{$fs}\$file; fi; done;cd ..;rmdir {$dirName}");
	}
}
