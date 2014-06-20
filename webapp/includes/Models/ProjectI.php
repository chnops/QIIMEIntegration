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
	public function getEnvironmentSource();

	// Lazy load getters (and dependents)
	public function getScripts();
	public function initializeScripts();
	public function renderOverview();
	public function getFileTypes();
	public function getInitialFileTypes();
	public function getFileTypeFromHtmlId($htmlId);
	
	// complex accessors
	// (which may change during the execution of the script)
	public function getProjectDir();
	public function retrieveAllUploadedFiles();
	public function getPastScriptRuns();
	public function retrieveAllGeneratedFiles();
	public function retrieveAllBuiltInFiles();

	// complex mutators
	public function beginProject();
	public function receiveDownloadedFile($url, $fileName, FileType $fileType);
	public function receiveUploadedFile($givenName, $tmpName, FileType $fileType);
	public function runScript(array $allInput);
	public function deleteUploadedFile($fileName);
	public function deleteGeneratedFile($fileName, $runId);
	public function unzipUploadedFile($fileName);
	public function unzipGeneratedFile($fileName, $runId);
	public function compressUploadedFile($fileName);
	public function compressGeneratedFile($fileName, $runId);
	public function decompressUploadedFile($fileName);
	public function decompressGeneratedFile($fileName, $runId);
}
