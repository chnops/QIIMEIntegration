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

	public function createUploadedFile($username, $projectId, $fileName, $fileType);
	public function getAllUploadedFiles($username, $projectId);

	public function saveRun($username, $projectId, $sriptName, $scriptText);
	public function addRunResults($runId, $consoleOutput, $version);
	public function getPastRuns($username, $projectId);
}
