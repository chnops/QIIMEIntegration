<?php 
/*
 * Copyright (C) 2014 Aaron Sharp
 * Released under GNU GENERAL PUBLIC LICENSE Version 3, 29 June 2007
 */

namespace Database;

interface DatabaseI {

	public function __construct();

	public function userExists($username);
	public function createUser($username);
	public function getUserRoot($username);

	public function getAllProjects($username);
	public function createProject($username, $projectName);
	public function getProjectName($username, $projectId);

	public function createUploadedFile($username, $projectId, $fileName, $fileType, $isDownloaded = false, $size = -1);
	public function getAllUploadedFiles($username, $projectId);
	public function removeUploadedFile($username, $projectId, $fileName);
	public function changeFileName($username, $projectId, $fileName, $newFilename);
	public function uploadExists($username, $projectId, $fileName);
	public function renderCommandUploadSuccess($username, $projectId, $fileName, $size);
	public function renderCommandUploadFailure($username, $projectId, $fileName, $size);

	public function createRun($username, $projectId, $scriptName, $paramText);
	public function giveRunPid($runId, $pid);
	public function getAllRuns($username, $projectId);
	public function renderCommandRunComplete($runId);
}
