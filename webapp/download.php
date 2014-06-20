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
$actualPath = "./projects/u" . $database->getUserRoot($_SESSION['username']) . "/p" . $_SESSION['project_id'];
if ($_GET['run'] == -1) {
	$actualPath .= "/uploads/" . $_GET['file_name'];
}
else {
	$actualPath .= "/r" . $_GET['run'] . "/" . $_GET['file_name'];
}

if (isset($_GET['as_text']) && $_GET['as_text']) {
	try {
		$maxLen = 2000;
		$contents = file_get_contents($actualPath, $useIncludePath = false, $context = NULL, $offset = -1, $maxLen);
		if(strlen($contents) == 0) {
			echo "<div style=\"font-family:'Comic Sans MS', cursive, sans-serif\">	File is empty	</div>";
		}
		else {
			$helper = \Utils\Helper::getHelper();
			echo $helper->htmlentities($contents);

			if (strlen($contents) == $maxLen) {
				echo "...";
			}
		}
	}
	catch (Exception $ex) {
		error_log($ex->getMessage());
		echo "<div style=\"font-family: 'Comic Sans MS', cursive, sans-serif\">	Error accessing file: Please see the error log or system administrator</div>";
	}
}
else {
	ob_end_clean();
	// TODO reference database for file specific information (e.g. text type, size)
	header('Content-Type: application/octet-stream');
	header("Content-Transfer-Encoding: Binary"); 
	header("Content-disposition: attachment; filename=\"" . $_GET['file_name'] . "\""); 
	readfile($actualPath);
}
