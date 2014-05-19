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
	protected $fileTypes;

	public function __construct(\Database\DatabaseI $database, WorkflowI $workflow, OperatingSystemI $operatingSystem) {
		$this->workflow = $workflow;
		$this->database = $database;
		$this->operatingSystem = $operatingSystem;
		$this->scripts = $this->getInitialScripts();
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
	public function renderForm() {
		$projectName = htmlentities($this->name);
		$owner = htmlentities($this->owner);
		$form = "<form method=\"POST\" enctype=\"multipart/form-data\">
			<h4>Name your project (<a onclick=\"displayHelp('script_help_name');\">help</a>)</h4>
			<label>Project name: <input type=\"text\" name=\"project_name\" value=\"{$projectName}\"/></label>
			<label>Project owner: <input type=\"text\" name=\"project_owner\" value=\"{$owner}\"/></label>
			<hr class=\"small\"/>
			<h4>Input files (<a onclick=\"displayHelp('script_help_upload');\">help</a>)</h4>
			<label>Map file: <input type=\"file\" name=\"project_input_file\"/></label>
			<hr class=\"small\"/>";

		foreach ($this->getScripts() as $script) {
			$form .= $script->renderAsForm();
			$form .= "<hr class=\"small\"/>";
		}

		$form .= "<button type=\"submit\">Run it!</button></form>";
		return $form;
	}
	public function renderHelp() {
		$help = "<div class=\"script_help\" id=\"script_help_name\">";
		$help .= "<p>Name your project</p>
			<p>Choose something short, yet descriptive.  Any name will do.  For owner, put your own netID, or the netID of whoever will own the project.</p>";
		$help .= "</div>";
		$help .= "<div class=\"script_help\" id=\"script_help_upload\">";
		$help .= "<p>Upload files</p>
			<p>Upload a map file.  </p>";
		$help .= "</div>";
		foreach ($this->getScripts() as $script) {	
			$help .= "<div class=\"script_help\" id=\"script_help_{$script->getScriptShortTitle()}\">";
			$help .= $script->renderHelp();
			$help .= "</div>";
		}

		return  $help;
	}

	public function getFileTypeFromShortName($shortName) {
		foreach ($this->fileTypes as $fileType) {
			if ($fileType->getShortName() == $shortName) {
				return $fileType;
			}
		}
		return NULL;
	}
	public function receiveUploadedFile($fileName, FileType $fileType) {
		$systemFileName = $this->database->createUploadedFile($this->owner, $this->id, $fileName, $fileType->getShortName());
		if (!$systemFileName) {
			return false;
		}
		$fullFileName = $this->operatingSystem->getHome() .
			$this->database->getUserRoot($this->owner) . "/" . $this->id . "/" . $systemFileName;
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

	public abstract function beginProject();
	public abstract function getInitialScripts();
	public abstract function getInitialFileTypes();
	public abstract function processInput(array $allInput);
	public abstract function renderOverview();
}
