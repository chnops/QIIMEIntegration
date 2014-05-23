<?php
require_once './includes/setup.php';

// Download the file

if (!isset($_SESSION['username']) || !isset($_SESSION['project_id'])) {
	header('HTTP/1.0 403 Forbidden');
	echo "<p>You must <a href=\"index.php\">login</a> and select a project</p>";
	exit;
}
if (!isset($_GET['run']) || !isset($_GET['file_name'])) {
	header('HTTP/1.1 400 Bad request');
	echo "<p>You must provide a run id and file name to download (url)</p>";
	exit;
}
$operatingSystem = new \Models\MacOperatingSystem();
$database = new \Database\PDODatabase($operatingSystem);
$actualPath = "./projects/u" . $database->getUserRoot($_SESSION['username']) . "/p" . $_SESSION['project_id'] . "/r" . $_GET['run'] . "/" . $_GET['file_name'];

if ($_GET['as_text']) {
	$maxLen = 2000;
	$contents = file_get_contents($actualPath, $useIncludePath = false, $context = NULL, $offset = -1, $maxLen);
	if (strlen($contents) == $maxLen) {
		echo htmlentities($contents) . "...";
	}
	else {
		echo $contents;
	}
}
else {
	// TODO reference database for file specific information (e.g. text type, size)
	header('Content-Type: application/octet-stream');
	header("Content-Transfer-Encoding: Binary"); 
	header("Content-disposition: attachment; filename=\"" . $_GET['file_name'] . "\""); 
	readfile($actualPath);
}
