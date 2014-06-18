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

	public function getDirContents($name, $prependHome = true) {
		$result = 0;
		ob_start();
		if ($prependHome) {
			$code = "ls " . escapeshellarg($this->home . "/" . $name);
		}
		else {
			$code = "ls " . escapeshellarg($name);
		}
		system($code, $result);

		if ($result) {
			ob_end_clean();
			throw new OperatingSystemException("ls failed.  See error log");
		}

		$immediateOutput = ob_get_clean();
		if (!$immediateOutput) {
			return array();
		}
		$immediateOutput = explode("\n", trim($immediateOutput));
		$output = array();

		foreach ($immediateOutput as $currentFileName) {
			ob_start();
			if ($prependHome) {
				$potentialDirectory = escapeshellarg($this->home . $name . "/" . $currentFileName);
			}
			else {
				$potentialDirectory = escapeshellarg($name . "/" . $currentFileName);
			}
			system("if [ -d {$potentialDirectory} ]; then printf '1'; else printf '0'; fi;");
			$isDir = ob_get_clean();
			if ($isDir) {
				$childOutput = $this->getDirContents($name . "/" . $currentFileName, $prependHome);
				if (!empty($childOutput)) {
					foreach ($childOutput as $file) {
						$output[] = $currentFileName . "/" . $file;					
					}
				}
			}
			else {
				$output[] = $currentFileName;
			}
		}

		return $output;
	}

	public function executeArbitraryCommand($environmentSource, $runDirectory, $script) {
		if ($environmentSource) {
			$code = "source {$environmentSource}; 
				if [ $? -ne 0 ]; then echo 'Unable to source environment: {$environmentSource}'; exit 1; fi;";
		}
		else {
			$code = "";
		}
		$code .= "cd {$this->home}/{$runDirectory};
			if [ $? -ne 0 ]; then echo 'Requested directory cannot be found: {$this->home}{$runDirectory}'; exit 1; fi;
		   	{$script} 2>> error_log.txt;";

		$returnValue = 0;
		ob_start();
		system($code, $returnValue);
		if ($returnValue) {
			$exception = new OperatingSystemException("An error occurred while executing script." .
				" An error_log.txt file should have been created, which you can acces on the View Results page.");
			$exception->setConsoleOutput(ob_get_clean());
			throw $exception;
		}
		return ob_get_clean();
	}
	public function combineCommands(array $commands) {
		$output = "";
		foreach ($commands as $command) {
			$output .= $command . " 2>> error_log.txt;";
		}
		return $output;
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


	public function uploadFile(ProjectI $project, $givenName, $tmpName) {
			$targetName = $this->home . $project->getProjectDir() . "/uploads/" . $givenName;
			$result = move_uploaded_file($tmpName, $targetName);
			if (!$result) {
				throw new OperatingSystemException("Unable to move file from temporary upload to operating system");
			}
			return true;
	}
	public function downloadFile(ProjectI $project, $url, $onSuccess, $onFail) {
		ob_start();
		$scriptCommand = "source " . escapeshellarg($project->getEnvironmentSource()) . ";
			if [ $? != 0 ]; then echo 'Unable to source environment variables'; exit 1; fi;
			cd {$this->home}{$project->getProjectDir()}/uploads;
			let exists=`curl -o /dev/null --silent --head --write-out '%{http_code}\n' " . escapeshellarg($url) . "`;
			if [ \$exists -lt 200 ] || [ \$exists -ge 400 ];
				then echo 'The requested URL does not exist';
				exit 1;
			fi;
			which wget &> /dev/null;
			if [ $? != 0 ]; then echo 'wget not found'; exit 1; fi;
			(wget " . escapeshellarg($url) . " --limit-rate=1M --quiet;
				let wget_success=$?;
				cd \$OLDPWD;
				if [ \$wget_success -eq 0 ]; then {$onSuccess}; else {$onFail}; fi;) &> /dev/null &";
		$returnCode = 0;
		system($scriptCommand, $returnCode);
		if ($returnCode) {
			$ex = new OperatingSystemException("Unable to download file");
			$ex->setConsoleOutput(ob_get_clean());
			throw $ex;
		}
		return ob_get_clean();
	}
	public function deleteFile(ProjectI $project, $fileName, $isUploaded, $runId) {
		$dir = $this->home . $project->getProjectDir();
		if ($isUploaded) {
			$dir .= "/uploads/";
		}
		else {
			$dir .= "/r{$runId}/";
		}
		$code = "cd " . escapeshellarg($dir) . ";
			touch " . escapeshellarg($fileName) . ";
			rm " . escapeshellarg($fileName) . ";";

		$exitStatus = 0;
		ob_start();
		system($code, $exitStatus);
		if ($exitStatus) {
			$ex = new OperatingSystemException("Unable to remove file");
			$ex->setConsoleOutput(ob_get_clean());
			throw $ex;
		}
		else {
			return ob_get_clean();
		}
	}
}
