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
		$this->database->startTakingRequests();
		$systemFileName = $this->database->createUploadedFile($this->owner, $this->id, $fileName, $fileType->getHtmlId());
		if (!$systemFileName) {
			$this->database->forgetAllRequests();
			return false;
		}
		$fullFileName = $this->operatingSystem->getHome() .
			$this->database->getUserRoot($this->owner) . "/" . $this->id . "/uploads/" . $systemFileName;
		return $fullFileName;
	}
	public function confirmUploadedFile() {
		$this->database->executeAllRequests();
	}
	public function forgetUploadedFile() {
		$this->database->forgetAllRequests();
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
			if (!isset($pastRunsFormatted[$run['script_name']])) {
				$pastRunsFormatted[$run['script_name']] = array();
			}

			$projectDirectory = $this->database->getUserRoot($this->owner) . "/" . $this->id;
			$generatedFiles = $this->operatingSystem->getDirContents($projectDirectory . "/" . $run['id']);

			$pastRunsFormatted[$run['script_name']][] = array(
				"script_string" => $run['script_string'],
				"files" => $generatedFiles,
				"output" => $run['output'],
				"version" => $run['version'],
			);
		}
		
		$output = "";
		foreach ($this->scripts as $scriptName => $scriptObject) {
			$output .= "<div class=\"hideable\" id=\"past_results_{$scriptName}\"><ul>";
			if (isset($pastRunsFormatted[$scriptName])) {
				foreach ($pastRunsFormatted[$scriptName] as $run) {
					$output .= "<li title=\"{$run['script_string']}\">";
					$output .= "Result:<br/>{$run['version']}<br/>{$run['output']}";
					$output .= "<ul>";
					foreach ($run['files'] as $file) {
						$output .= "<li>{$file}</li>";
					}
					$output .= "</ul></li>\n";
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

	public function getAllGeneratedFiles() {
		$generatedFiles = array();
		$pastRuns = $this->database->getPastRuns($this->owner, $this->id);
		foreach ($pastRuns as $runArray) {
			$projectDirectory = $this->database->getUserRoot($this->owner) . "/" . $this->id;
			$runFiles = $this->operatingSystem->getDirContents($projectDirectory . "/" . $runArray['id']);
			$generatedFiles[$runArray['id']] = $runFiles;
		}
		return $generatedFiles;
	}

	public abstract function beginProject();
	public abstract function initializeScripts();
	public abstract function getInitialFileTypes();
	public abstract function runScript(array $allInput);
	public abstract function renderOverview();
}
