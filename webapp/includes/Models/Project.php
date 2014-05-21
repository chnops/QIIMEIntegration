<?php

namespace Models;

abstract class Project {
	protected $owner;
	protected $id;
	protected $name;

	protected $workflow;
	protected $database;
	protected $operatingSystem;

	protected $scripts;
	protected $scriptsFormatted;
	protected $fileTypes;

	public function __construct(\Database\DatabaseI $database, WorkflowI $workflow, OperatingSystemI $operatingSystem) {
		$this->workflow = $workflow;
		$this->database = $database;
		$this->operatingSystem = $operatingSystem;
		$this->initializeScripts();
		$this->fileTypes = $this->getInitialFileTypes();
		$this->fileTypes[] = new ArbitraryTextFileType();
	}

	public function getOwner() {
		return $this->owner;
	}
	public function setOwner($owner) {
		$this->owner = $owner;
	}
	public function getId() {
		return $this->id;
	}
	public function setId($id) {
		$this->id = $id;
	}
	public function getName() {
		return $this->name;
	}
	public function setName($name) {
		$this->name = $name;
	}
	public function getScripts() {
		return $this->scripts;
	}
	public function getFileTypes() {
		return $this->fileTypes;
	}
	public function getDatabase() {
		return $this->database;
	}
	public function getOperatingSystem() {
		return $this->operatingSystem;
	}

	public function getFileTypeFromHtmlId($htmlId) {
		foreach ($this->fileTypes as $fileType) {
			if ($fileType->getHtmlId() == $htmlId) {
				return $fileType;
			}
		}
		return NULL;
	}
	public function receiveUploadedFile($fileName, FileType $fileType) {
		$systemFileName = $this->database->createUploadedFile($this->owner, $this->id, $fileName, $fileType->getHtmlId());
		if (!$systemFileName) {
			return false;
		}
		$fullFileName = $this->operatingSystem->getHome() .
			$this->database->getUserRoot($this->owner) . "/" . $this->id . "/uploads/" . $systemFileName;
		return $fullFileName;
	}
	public function retrieveAllUploadedFiles() {
		$rawFiles = $this->database->getAllUploadedFiles($this->owner, $this->id);
		$formattedFiles = array();
		foreach ($rawFiles as $fileArray) {
			$formattedFiles[$fileArray['file_type']][] = $fileArray['given_name'];
		}
		return $formattedFiles;
	}
	
	public function getSystemFileName($userFileName) {
		return $this->database->getUploadedFileSystemName($this->owner, $this->id, $userFileName);
	}

	public function getPastScriptRuns() {
		$pastRuns = $this->database->getPastRuns($this->owner, $this->id);
		if (empty($pastRuns)) {
			return "";
		}

		$pastRunsFormatted = array();
		foreach ($pastRuns as $run) {
			$pastRunsFormatted[$run['script_name']][] = $run['script_string'];
		}
		
		$output = "";
		foreach ($this->scripts as $scriptName => $scriptObject) {
			$output .= "<div class=\"hideable\" id=\"past_results_{$scriptName}\"><ul>";
			if (isset($pastRunsFormatted[$scriptName])) {
				foreach ($pastRunsFormatted[$scriptName] as $run) {
					$output .= "<li>" . htmlentities($run) . "</li>";
				}
			}
			else {
				$output .= "This script has not been run yet.";
			}
			$output .= "</ul></div>\n";

		}
		return $output;
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

	public abstract function beginProject();
	public abstract function initializeScripts();
	public abstract function getInitialFileTypes();
	public abstract function runScript(array $allInput);
	public abstract function renderOverview();
}
