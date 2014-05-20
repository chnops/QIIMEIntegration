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
