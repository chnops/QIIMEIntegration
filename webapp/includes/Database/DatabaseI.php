<?php 

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

	public function saveRun($username, $projectId, $sriptName, $scriptText);
	public function addRunResults($runId, $consoleOutput, $version);
	public function getPastRuns($username, $projectId);

	public function renderCommandUploadSuccess($username, $projectId, $fileName);
	public function renderCommandUploadFailure($username, $projectId, $fileName);
	public function uploadExists($username, $projectId, $fileName);
}
