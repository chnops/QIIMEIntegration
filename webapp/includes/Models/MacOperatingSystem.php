<?php

namespace Models;

class MacOperatingSystem implements OperatingSystemI {
	private $home = "./projects/";

	public function getHome() {
		return $this->home;
	}

	public function isValidDirName($name) {
		$nameParts = $this->getFileParts($name);
		foreach ($nameParts as $namePart) {
			if (!preg_match('/^[a-z0-9_]+$/', $namePart)) {
				return false;
			}
		}
		return true;
	}

	public function getFileParts($name) {
		$name = preg_replace('/\/+/', '/', $name);
		$name = trim($name, '/');
		return explode('/', $name);
	}

	public function concatFileNames($name1, $name2) {
		$name1 = preg_replace('/\/+/', '/', $name1);
		$name1 = rtrim($name1, '/');
		$name2 = preg_replace('/\/+/', '/', $name2);
		$name2 = ltrim($name2, '/');
		return $name1 . '/' . $name2;
	}

	public function createDir($name) {
		if (!$this->isValidDirName($name)) {
			$exception = new OperatingSystemException("Unable to create directory");
			$helper = \Utils\Helper::getHelper();
			$exception->setConsoleOutput("Invalid file name: " . $helper->htmlentities($name));
			throw $exception;
		}

		$returnCode = 0;
		$dirName = $this->concatFileNames($this->home, $name);
		system("mkdir {$dirName}", $returnCode);

		if ($returnCode) {
			$ex = new OperatingSystemException("Unable to create directory");
			$ex->setConsoleOutput("mkdir returned error code: {$returnCode}");
			throw $ex;
		}
	}
	public function removeDirIfExists($name) {
		if (!$this->isValidDirName($name)) {
			return false;
		}

		$returnCode = 0;
		ob_start();
		$dirName = $this->concatFileNames($this->home, $name);
		system("rm -r {$dirName}", $returnCode);
		ob_end_clean();
		if ($returnCode != 0) {
			return false;
		}
		return true;
	}

	public function getDirContents($name, $prependHome = true) {
		if ($prependHome) {
			$name = $this->concatFileNames($this->home, $name);
		}
		$name = rtrim($name, "/");
		$code = "find " . escapeshellarg($name) . " -type f;
			find " . escapeshellarg($name) . " -type d -empty";

		$result = 0;
		ob_start();
		system($code, $result);
		if ($result) {
			ob_end_clean();
			$ex = new OperatingSystemException("Unable to get dir contents");
			$ex->setConsoleOutput("'find' failed with error code: {$result}");
			throw $ex;
		}

		$rawOutput = explode("\n", trim(ob_get_clean()));
		sort($rawOutput);
		$output = array();
		// plus one is for preceding '/' that was removed from $name
		$subStrStart = strlen($name) + 1;
		foreach($rawOutput as $file) {
			// Ignore directory name itself
			if (strlen($file) > $subStrStart) {
				$output[] = substr($file, $subStrStart);
			}
		}
		return $output;
	}

	public function uploadFile(ProjectI $project, $givenName, $tmpName) {
			$targetName = $this->home . $project->getProjectDir() . "/uploads/" . $givenName;
			$result = $this->moveUploadedFile($tmpName, $targetName);
			if (!$result) {
				throw new OperatingSystemException("Unable to move file from temporary upload to operating system");
			}
			return true;
	}
	public function moveUploadedFile($tmpName, $targetName) {
		return move_uploaded_file($tmpName, $targetName);
	}
	public function downloadFile(ProjectI $project, $url, $outputName, \Database\DatabaseI $database) {
		ob_start();

		$urlEsc = escapeshellarg($url);
		$outputNameEsc = escapeshellarg($outputName);
		$onSuccess = $database->renderCommandUploadSuccess($project->getOwner(), $project->getId(), $outputName, $size = "\$size");
		$onFail = $database->renderCommandUploadFailure($project->getOwner(), $project->getId(), $outputName, $size = "\$size");

		$scriptCommand = "source " . escapeshellarg($project->getEnvironmentSource()) . " 2>&1;
			cd {$this->home}{$project->getProjectDir()}/uploads 2>/dev/null;
			if [ $? -ne 0 ]; then printf 'Unable to find project directory'; exit 1; fi;
			let exists=`curl -o /dev/null --silent --head --write-out '%{http_code}\n' {$urlEsc}`;
			if [ \$exists -lt 200 ] || [ \$exists -ge 400 ];
				then printf 'The requested URL does not exist';
				exit 1;
			fi;
			which wget &>/dev/null;
			if [ $? != 0 ]; then printf 'wget not found'; exit 1; fi;
			(wget {$urlEsc} --limit-rate=1M --quiet --output-document={$outputNameEsc};
				let wget_success=$?;
				size=`wc -c {$outputNameEsc} | awk '{print $1}'`;
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

	public function findFileName(ProjectI $project, $fileName, $runId) {
		$projectDir = $this->concatFileNames($this->home, $project->getProjectDir());
		if ($runId == -1) {
			$dir = "uploads/";
		}
		else {
			$dir = "r{$runId}/";
		}
		$dir = $this->concatFileNames($projectDir, $dir);

		return $this->concatFileNames($dir, $fileName);
	}

	public function getFileExistsCode($fileName) {
		return "if [ ! -e " . escapeshellarg($fileName) . " ]; then printf 'The requested file does not exist.'; exit 1; fi;";
	}

	public function deleteFile(ProjectI $project, $fileName, $runId) {
		$fileName = $this->findFileName($project, $fileName, $runId);

		$fileNameEsc = escapeshellarg($fileName);
		$code = $this->getFileExistsCode($fileName) .
			"if [ -d {$fileNameEsc} ]; then rmdir {$fileNameEsc} 2>&1; else rm {$fileNameEsc} 2>&1; fi;";

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
	public function unzipFile(ProjectI $project, $fileName, $runId) {
		$fileNamePathway = $this->findFileName($project, $fileName, $runId);

		ob_start();
		$returnCode = 0;

		$fileNameEsc = escapeshellarg($fileNamePathway);
		$code = $this->getFileExistsCode($fileNamePathway) .
			//zipinfo prints out file names contained in zip
			"zipinfo -1 {$fileNameEsc} 2> /dev/null;
			unzip -qq {$fileNameEsc} &> /dev/null;
			if [ $? -eq 0 ]; then rm {$fileNameEsc};
			else echo 'Unable to unzip file'; exit 1; fi;";
		system($code, $returnCode);

		if ($returnCode) {
			$ex = new OperatingSystemException("Unable to unzip file");
			$ex->setConsoleOutput(ob_get_clean());
			throw $ex;
		}
		$files = explode("\n", trim(ob_get_clean()));
		return $files;
	}
	public function compressFile(ProjectI $project, $fileName, $runId) {
		$fileNamePath = $this->findFileName($project, $fileName, $runId);

		ob_start();
		$exitCode = 0;
		$code = $this->getFileExistsCode($fileNamePath) .
			"output=`echo 'n' | gzip " . escapeshellarg($fileNamePath) . " 2>&1`;" . 
			"if [ -n \"\$output\" ]; then printf \"\$output\"; exit 1; fi;";
		system($code, $exitCode);

		if ($exitCode) {
			$ex = new OperatingSystemException("Unable to compress file");
			$ex->setConsoleOutput(ob_get_clean());
			throw $ex;
		}
		return "{$fileName}.gz";
	}
	public function decompressFile(ProjectI $project, $fileName, $runId) {
		$fileNamePath = $this->findFileName($project, $fileName, $runId);

		ob_start();
		$exitCode = 0;
		$code = $this->getFileExistsCode($fileNamePath) .
			"output=`echo 'n' | gunzip " . escapeshellarg($fileNamePath) . " 2>&1`;" . 
			"if [ -n \"\$output\" ]; then printf \"\$output\"; exit 1; fi;";
		system($code, $exitCode);

		if ($exitCode) {
			$ex = new OperatingSystemException("Unable to decompress file");
			$ex->setConsoleOutput(ob_get_clean());
			throw $ex;
		}
		return preg_replace("/\.gz$/", "", $fileName);
	}

	public function runScript(ProjectI $project, $runId, \Models\Scripts\ScriptI $script, \Database\DatabaseI $database) {
		ob_start();
		$projectDir = $this->concatFileNames($this->home, $project->getProjectDir());
		$runDir = $this->concatFileNames($projectDir, "r" . $runId);

		$bashCode = $this->getFileExistsCode($projectDir) .
			"mkdir {$runDir};
			if [ $? -ne 0 ]; then printf 'Unable to create run dir'; exit 1; fi;
			cd {$runDir};
			source " . escapeshellarg($project->getEnvironmentSource()) . " 2>&1;
			printenv > env.txt;
			{$script->renderVersionCommand()} >> env.txt;
			if [ $? -ne 0 ]; then echo 'There was a problem getting this script'\''s version' >> error_log.txt; fi;
			jobs &> /dev/null;
			({$script->renderCommand()};cd \$OLDPWD;{$database->renderCommandRunComplete($runId)})  > output.txt 2>> error_log.txt &
			job_id=`jobs -n`;
			if [ ! -n \$job_id ]; then printf 'Unable to start the script'; exit 1; fi;
			printf \$!;";

		system($bashCode, $codeReturn);
		if ($codeReturn) {
			$ex = new OperatingSystemException("There was a problem initializing your script");
			$ex->setConsoleOutput(ob_get_clean());
			throw $ex;
		}
		return ob_get_clean();
	}
}
