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

	public function scriptExists($scriptName) {
		try {
			$whichOutput = $this->operatingSystem->executeArbitraryScript($this->workflow->getEnvironmentSource(), "", "which {$scriptName}");
			return true;
		}
		catch (\Exception $ex) {
			return false;
		}
	}
	public function getVersion($scriptName) {
		try {
			$consoleOutput = $this->operatingSystem->executeArbitraryScript($this->workflow->getEnvironmentSource(), "", "{$scriptName} --version");
			return trim($consoleOutput);
		}
		catch (\Exception $ex) {
			return "Unable to obtain version information";
		}
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
