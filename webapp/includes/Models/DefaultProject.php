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
		$databaseSuccess = $this->database->createUploadedFile(
			$this->owner, $this->id, $fileName, $fileType->getHtmlId(), $isDownload = true, $size = -1);
		if (!$databaseSuccess) {
			$this->database->forgetAllRequests();
			throw new \Exception("There was a problem storing your new file in the database");
		}

		try {	
			$consoleOutput = $this->operatingSystem->downloadFile($this, $url, $fileName, $this->database);
			$this->database->executeAllRequests();
			$this->uploadedFiles = array();
			return $consoleOutput;
		}
		catch (OperatingSystemException $ex) {
			$this->database->forgetAllRequests();
			throw $ex;
		}
	}
	public function deleteGeneratedFile($fileName, $runId) {
		$this->operatingSystem->deleteFile($this, $fileName, $isUploaded = false, $runId);
	}
	public function unzipGeneratedFile($fileName, $runId) {
		$this->operatingSystem->unzipFile($this, $fileName, $isUploaded = false, $runId);
	}
	public function compressGeneratedFile($fileName, $runId) {
		$this->operatingSystem->compressFile($this, $fileName, $isUploaded = false, $runId);
	}
	public function decompressGeneratedFile($fileName, $runId) {
		$this->operatingSystem->decompressFile($this, $fileName, $isUploaded = false, $runId);
	}
	public function receiveUploadedFile($givenName, $tmpName, $size, FileType $fileType) {
		$this->database->startTakingRequests();
		$databaseSuccess = $this->database->createUploadedFile($this->owner, $this->id, $givenName, $fileType->getHtmlId(), $isDownloaded = false, $size);
		if (!$databaseSuccess) {
			$this->database->forgetAllRequests();
			throw new \Exception("Unable to create file in database");
		}
		try {
			$this->operatingSystem->uploadFile($this, $givenName, $tmpName);
			$this->database->executeAllRequests();
			$this->uploadedFiles = array();
			return true;
		}
		catch(OperatingSystemException $ex) {
			$this->database->forgetAllRequests();
			throw $ex;
		}
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
	public function unzipUploadedFile($fileName) {
		$this->database->startTakingRequests();
		$removeResult = $this->database->removeUploadedFile($this->owner, $this->id, $fileName);
		if (!$removeResult) {
			$this->database->forgetAllReqeusts();
			throw new \Exception("Unable to find/remove .zip file from the database");
		}

		try {	
			// TODO this one will be difficult to do parallel
			$newFileNames = $this->operatingSystem->unzipFile($this, $fileName, $isUploaded = true, $runId = -1);
			foreach ($newFileNames as $newFileName) {
				if(!$this->database->createuploadedFile($this->owner, $this->id, $newFileName, 'arbitrary_text')) {
					throw new \Exception("Unable to add unzipped file to database");
				}
			}
			$this->database->executeAllRequests();
			return true;
		}
		catch (OperatingSystemException $ex) {
			$this->database->forgetAllRequests();
			throw $ex;
		}
	}
	public function compressUploadedFile($fileName) {
		$newFileName = $this->operatingSystem->compressFile($this, $fileName, $isUploaded = true, $runId = -1);
		$nameChangeResult = $this->database->changeFileName($this->owner, $this->id, $fileName, $newFileName);
		if (!$nameChangeResult) {
			throw new \Exception("Unable to change file name, compression failed.");
		}
	}
	public function decompressUploadedFile($fileName) {
		$newFileName = $this->operatingSystem->decompressFile($this, $fileName, $isUploaded = true, $runId = -1);
		$nameChangeResult = $this->database->changeFileName($this->owner, $this->id, $fileName, $newFileName);
		if (!$nameChangeResult) {
			throw new \Exception("Unable to change file name, decompression failed.");
		}
	}
	public function retrieveAllUploadedFiles() {
		if (empty($this->uploadedFiles)) {
			$rawFiles = $this->database->getAllUploadedFiles($this->owner, $this->id);
			foreach ($rawFiles as $fileArray) {
				$this->uploadedFiles[] = array("name" => $fileArray['name'], 
					"type" => $fileArray['file_type'], "uploaded" => "true",
					"status" => $fileArray['description'], "size" => $fileArray['approx_size']);
			}
		}
		return $this->uploadedFiles;
	}
	
	public function getPastScriptRuns() {
		if (empty($this->pastScriptRuns)) {
			$helper = \Utils\Helper::getHelper();
			$pastRunsRaw = $this->database->getPastRuns($this->owner, $this->id);
			foreach ($pastRunsRaw as $run) {
				$runFileNames = $this->attemptGetDirContents($this->getProjectDir() . "/r" . $run['id']);
	
				$this->pastScriptRuns[] = array(
					"id" => $run['id'],
					"name" => $run['script_name'],
					"input" => $run['script_string'],
					"file_names" => $runFileNames,
					"output" => $helper->htmlentities($run['output']),
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
	public abstract function getEnvironmentSource();
}
