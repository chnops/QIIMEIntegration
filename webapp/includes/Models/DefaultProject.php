<?php

namespace Models;

abstract class DefaultProject implements ProjectI {
	protected $owner;
	protected $id;
	protected $name;

	protected $workflow;
	protected $database;
	protected $operatingSystem;

	protected $scripts = array();
	protected $scriptsFormatted = array();
	protected $fileTypes = array();

	// complex fields (may change during script execution)
	protected $projectDir = "";
	protected $uploadedFiles = array();
	protected $pastScriptRuns = array();
	protected $generatedFiles = array();

	public function __construct(\Database\DatabaseI $database, WorkflowI $workflow, OperatingSystemI $operatingSystem) {
		$this->workflow = $workflow;
		$this->database = $database;
		$this->operatingSystem = $operatingSystem;
	}

	public function getOwner() {
		return $this->owner;
	}
	public function setOwner($owner) {
		$this->projectDir = "";
		$this->owner = $owner;
	}
	public function getId() {
		return $this->id;
	}
	public function setId($id) {
		$this->projectDir = "";
		$this->id = $id;
	}
	public function getName() {
		return $this->name;
	}
	public function setName($name) {
		$this->name = $name;
	}
	public function getScripts() {
		if (empty($this->scripts)) {
			$this->initializeScripts();
		}
		return $this->scripts;
	}
	public function getFileTypes() {
		if (empty($this->fileTypes)) {
			$this->fileTypes = $this->getInitialFileTypes();
			$this->fileTypes[] = new ArbitraryTextFileType();
		}
		return $this->fileTypes;
	}
	public function getDatabase() {
		return $this->database;
	}
	public function getOperatingSystem() {
		return $this->operatingSystem;
	}
	public function getProjectDir() {
		if (!$this->projectDir) {
			$this->projectDir = "u" . $this->database->getUserRoot($this->owner) . "/p" . $this->id;
		}
		return $this->projectDir;
	}

	public function getFileTypeFromHtmlId($htmlId) {
		foreach ($this->getFileTypes() as $fileType) {
			if ($fileType->getHtmlId() == $htmlId) {
				return $fileType;
			}
		}
		return NULL;
	}
	public function receiveDownloadedFile($url, $fileName, FileType $fileType) {
		$this->database->startTakingRequests();
		$databaseSuccess = $this->database->createUploadedFile($this->owner, $this->id, $fileName, $fileType->getHtmlId());
		if (!$databaseSuccess) {
			$this->database->forgetAllRequests();
			throw new \Exception("There was a problem storing your new file in the database");
		}

		$scriptCommands = array("let exists=`curl -o /dev/null --silent --head --write-out '%{http_code}\n' {$url}`",
			"if [ \$exists -lt 200 ] || [ \$exists -ge 400 ]",
				"then echo 'The requested URL ({$url}) does not exist'",
				"exit 1",
			"fi",
			"wget '{$url}' --limit-rate=1M &>/dev/null &"
		);
		$consoleOutput = $this->operatingSystem->executeArbitraryCommand($this->workflow->getEnvironmentSource(),
			$directory = "{$this->getProjectDir()}/uploads/", 
			$this->operatingSystem->combineCommands($scriptCommands));

		$this->confirmUploadedFile();
		return $consoleOutput;
	}
	public function deleteGeneratedFile($fileName, $runId) {
		$this->operatingSystem->deleteFile($this, $fileName, $isUploaded = false, $runId);
	}
	public function receiveUploadedFile($fileName, FileType $fileType) {
		$this->database->startTakingRequests();
		$databaseSuccess = $this->database->createUploadedFile($this->owner, $this->id, $fileName, $fileType->getHtmlId());
		if (!$databaseSuccess) {
			$this->database->forgetAllRequests();
			return false;
		}
		$fullFileName = $this->operatingSystem->getHome() . $this->getProjectDir() . "/uploads/" . $fileName;
		return $fullFileName;
	}
	public function deleteUploadedFile($fileName) {
		$this->database->startTakingRequests();
		$dbResult = $this->database->removeUploadedFile($this->owner, $this->id, $fileName);
		if (!$dbResult) {
			$this->database->forgetAllRequests();
			throw new \Exception("Unable to remove record of file from the database");
		}
		
		try {
			$this->operatingSystem->deleteFile($this, $fileName, $isUploaded = true, $runId = -1);
			$this->database->executeAllRequests();
		}
		catch(OperatingSystemException $ex) {
			$this->database->forgetAllRequests();
			throw $ex;
		}
	}
	public function confirmUploadedFile() {
		$this->uploadedFiles = array();
		$this->database->executeAllRequests();
	}
	public function forgetUploadedFile() {
		$this->database->forgetAllRequests();
	}
	public function retrieveAllUploadedFiles() {
		if (empty($this->uploadedFiles)) {
			$rawFiles = $this->database->getAllUploadedFiles($this->owner, $this->id);
			foreach ($rawFiles as $fileArray) {
				$this->uploadedFiles[] = array("name" => $fileArray['name'], 
					"type" => $fileArray['file_type'], "uploaded" => "true");
			}
		}
		return $this->uploadedFiles;
	}
	
	public function getPastScriptRuns() {
		if (empty($this->pastScriptRuns)) {
			$pastRunsRaw = $this->database->getPastRuns($this->owner, $this->id);
			foreach ($pastRunsRaw as $run) {
				$runFileNames = $this->attemptGetDirContents($this->getProjectDir() . "/r" . $run['id']);
	
				$this->pastScriptRuns[] = array(
					"id" => $run['id'],
					"name" => $run['script_name'],
					"input" => $run['script_string'],
					"file_names" => $runFileNames,
					"output" => $run['output'],
					"version" => $run['version'],
				);
			}
		}
		return $this->pastScriptRuns;
	}

	public function retrieveAllGeneratedFiles() {
		if (empty($this->generatedFiles)) {
			$pastRuns = $this->database->getPastRuns($this->owner, $this->id);
			foreach ($pastRuns as $run) {
				$runId = $run['id'];
				$runFiles = $this->attemptGetDirContents($this->getProjectDir() . "/r" . $runId);

				foreach ($runFiles as $fileName) {
					$this->generatedFiles[] = array("name" => $fileName, "run_id" => $runId);
				}
			}
		}
		return $this->generatedFiles;
	}
	public function attemptGetDirContents($dirName) {
			try {
				$dirContents = $this->operatingSystem->getDirContents($dirName);
			}
			catch (OperatingSystemException $ex) {
				error_log("unable to list contents of directory: {$dirName}");
				$dirContents = array();
			}
			return $dirContents;
	}

	public abstract function beginProject();
	public abstract function initializeScripts();
	public abstract function getInitialFileTypes();
	public abstract function runScript(array $allInput);
	public abstract function renderOverview();
	public abstract function retrieveAllBuiltInFiles();
}
