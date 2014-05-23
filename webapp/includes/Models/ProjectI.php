<?php

namespace Models;

interface ProjectI {
	// Dumb getters and setters
	public function __construct(\Database\DatabaseI $database, WorkflowI $workflow, OperatingSystemI $operatingSystem);
	public function getOwner();
	public function setOwner($owner);
	public function getId();
	public function setId($id);
	public function getName();
	public function setName($name);
	public function getDatabase();
	public function getOperatingSystem();

	// Lazy load getters (and dependents)
	public function getScripts();
	public function initializeScripts();
	public function renderOverview();
	public function getFileTypes();
	public function getInitialFileTypes();
	public function getFileTypeFromHtmlId($htmlId);
	
	// complex accessors
	public function scriptExists($scriptName);
	public function getVersion($scriptName);
	public function getSystemNameForUploadedFile($userFileName); // TODO differentiate uploaded vs. generated
	// (which may change during the execution of the script)
	public function getProjectDir();
	public function retrieveAllUploadedFiles();
	public function getPastScriptRuns();
	public function retrieveAllGeneratedFiles();

	// complex mutators
	public function beginProject();
	public function receiveUploadedFile($fileName, FileType $fileType);
	public function confirmUploadedFile();
	public function forgetUploadedFile();
	public function runScript(array $allInput);
}
