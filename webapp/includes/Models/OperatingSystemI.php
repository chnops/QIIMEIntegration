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

	public function deleteFile(ProjectI $project, $fileName, $isUploaded, $runId);
}
