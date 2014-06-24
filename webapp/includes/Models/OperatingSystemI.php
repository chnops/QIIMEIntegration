<?php

namespace Models;

interface OperatingSystemI {
	public function getHome();
	public function createDir($name);
	public function removeDirIfExists($name);
	public function getDirContents($name, $prependHome = true);
	public function executeArbitraryCommand($environmentSource, $projectDirectory, $script);
	public function combineCommands(array $commands);
	public function isValidFileName($name);

	// TODO refactor
	public function uploadFile(ProjectI $project, $givenName, $tmpName);
	public function downloadFile(ProjectI $project, $url, $outputName, \Database\DatabaseI $database);
	public function deleteFile(ProjectI $project, $fileName, $isUploaded, $runId);
	public function unzipFile(ProjectI $project, $fileName, $isUploaded, $runId);
	public function compressFile(ProjectI $project, $fileName, $isUploaded, $runId);
	public function decompressFile(ProjectI $project, $fileName, $isUploaded, $runId);
	public function runScript(ProjectI $project, $runId, \Models\Scripts\ScriptI $script, \Database\DatabaseI $databse);
}
