<?php

namespace Models;

interface OperatingSystemI {
	public function getHome();
	public function isValidDirName($name);
	public function getFileParts($name);
	public function concatFileNames($name1, $name2);
	public function createDir($name);
	public function removeDirIfExists($name);
	public function getDirContents($name, $prependHome = true);

	public function uploadFile(ProjectI $project, $givenName, $tmpName);
	public function downloadFile(ProjectI $project, $url, $outputName, \Database\DatabaseI $database);
	public function deleteFile(ProjectI $project, $fileName, $runId);
	public function unzipFile(ProjectI $project, $fileName, $runId);
	public function compressFile(ProjectI $project, $fileName, $runId);
	public function decompressFile(ProjectI $project, $fileName, $runId);
	public function runScript(ProjectI $project, $runId, \Models\Scripts\ScriptI $script, \Database\DatabaseI $databse);
}
