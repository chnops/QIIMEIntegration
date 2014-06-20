<?php

namespace Controllers;

class UploadController extends Controller {

	public function getSubTitle() {
		return "Upload Input Files";
	}
	private $fileType = NULL;
	private $url = "";

	private function getFileType() {
		if (!$this->fileType) {
			$project = ($this->project) ? $this->project : $this->workflow->getNewProject();
			if (isset($_POST['type'])) {
				$this->fileType = $project->getFileTypeFromHtmlId($_POST['type']);
			}
			else {
				$fileTypes = $project->getFileTypes();
				$this->fileType = $fileTypes[0];
			}
		}
		return $this->fileType;
	}

	public function retrievePastResults() {
		if (!$this->project) {
			return "";
		}
		$previousFiles = $this->project->retrieveAllUploadedFiles();
		if (empty($previousFiles)) {
			return "";
		}
		$output = "";

		$output .= "<h3>Previously Uploaded files:</h3><div class=\"accordion\">\n";
		$helper = \Utils\Helper::getHelper();
		$previousFilesFormatted = $helper->categorizeArray($previousFiles, 'type');

		foreach ($previousFilesFormatted as $fileType => $files) {
			$output .= "<h4 onclick=\"hideMe($(this).next())\">{$fileType} files</h4><div><ul>\n";
			foreach ($files as $file) {
				$output .= "<li>" . $helper->htmlentities($file['name']) . " ({$file['status']})</li>\n";
			}
			$output .= "</ul></div>\n";
		}
		return $output . "</div>";
	}

	public function parseInput() {
		if (!$this->username || !$this->project) {
			$this->disabled = " disabled";
			$this->isResultError = true;
			$this->result = "In order to upload files, you must be logged in and have a project selected.";
			return;
		}
		if (!isset($_POST['step']) ) {
			return;
		}

		if (!$this->getFileType()) {
			$this->isResultError = true;
			$this->result = "A the file you uploaded had an unrecognized type.<br/>";
			return;
		}
		else {
			$this->result = "";
		}
		
		// TODO if is valid form

		$isDownload = isset($_POST['url']);
		if ($isDownload) {
			$this->url = $_POST['url'];
			$urlParts = explode('/', $this->url);
			$fileName = "";
			while (!$fileName && $urlParts) {
				$fileName = array_pop($urlParts);
			}
			if (!$fileName) {
				$this->isResultError = true;
				$this->result = "Unable to determine file name from given url";
				return;
			}
		}
		else {
			$fileName = $_FILES['file']['name'];
		}
		$pastFiles = $this->project->retrieveAllUploadedFiles();
		foreach ($pastFiles as $extantFile) {
			if ($extantFile['name'] == $fileName) {
				$this->isResultError = true;
				$this->result .= "You have already uploaded a file with that file name. File names must be unique";
				return;
			}
		}
		if ($isDownload) {
			try {
				$this->result = $this->downloadFile($this->url, $fileName, $this->getFileType());
			}
			catch (\Exception $ex) {
				if ($ex instanceof \Models\OperatingSystemException) {
					error_log($ex->getConsoleOutput());
				}
				$this->isResultError = true;
				$this->result = $ex->getMessage();
			}
		}
		else {
			$this->uploadFile($_FILES['file'], $this->getFileType());
		}
	}

	private function downloadFile($url, $fileName, \Models\FileType $fileType) {
		$helper = \Utils\Helper::getHelper();
		$output = "File downloaded successfully.";
		$consoleOutput = $this->project->receiveDownloadedFile($url, $fileName, $fileType);
		if ($consoleOutput) {
			$output .= "<br/>The console returned the following output:<br/>" . $helper->htmlentities($consoleOutput);
		}
		return $output;
	}

	private function uploadFile(array $file, \Models\FileType $fileType) {
		$helper = \Utils\Helper::getHelper();
		if ($file['error'] > 0) {
			$this->isResultError = true;
			$fileUploadErrors = new FileUploadErrors();
			$this->result = "There was an error uploading your file: " . $fileUploadErrors->getErrorMessage($file['error']);
			return;
		}
		// TODO if size/type are valid

		$givenName = $file['name'];
		$tmpName = $file['tmp_name'];
		try {
			$this->project->receiveUploadedFile($givenName, $tmpName, $fileType);
			$this->result = "File " . $helper->htmlentities($givenName) . " successfully uploaded!";
		}
		catch (\Exception $ex) {
			$this->isResultError = true;
			$this->result = $ex->getMessage();
		}
	}

	public function renderInstructions() {
		return "";
	}
	public function renderForm() {
		$output = "
			<form method=\"POST\" action=\"index.php\" enctype=\"multipart/form-data\">
				<input type=\"hidden\" name=\"step\" value=\"{$this->step}\"/>
				<label for=\"file\">Select a file to upload:
				<input type=\"file\" name=\"file\"/{$this->disabled}></label>
				<label for=\"type\">File type:
				<select name=\"type\" onchange=\"displayHideables(this[this.selectedIndex].getAttribute('value'));\"{$this->disabled}>";

		$project = ($this->project) ? $this->project : $this->workflow->getNewProject();
		$fileTypes = $project->getFileTypes();
		foreach ($fileTypes as $fileType) {
			$selected = ($fileType->getHtmlId() == $this->getFileType()->getHtmlId()) ? " selected" : "";
			$output .= "<option value=\"{$fileType->getHtmlId()}\"{$selected}>{$fileType->getName()}</option>";
		}
	
		$output .= "</select></label>
			<button type=\"submit\"{$this->disabled}>Upload</button>
			</form>";

		$output .= "<br/><strong>-OR-</strong><br/>";
		$output .= "<form method=\"POST\" action\"index.php\">
			<input type=\"hidden\" name=\"step\" value=\"{$this->step}\"/>
			<label for=\"url\">Specify a url to download file from:
			<input type=\"text\" name=\"url\" value=\"{$this->url}\" placeholder=\"http://seq.center/file/path\"{$this->disabled}>
			<label for=\"type\">File type:
			<select name=\"type\" onchange=\"displayHideables(this[this.selectedIndex].getAttribute('value'));\"{$this->disabled}>";
		foreach ($fileTypes as $fileType) {
			$selected = ($fileType->getHtmlId() == $this->getFileType()->getHtmlId()) ? " selected" : "";
			$output .= "<option value=\"{$fileType->getHtmlId()}\"{$selected}>{$fileType->getName()}</option>";
		}
		$output .= "</select></label>
			<button type=\"submit\"{$this->disabled}>Download</button>
			</form>";
		return $output;
	}
	public function renderHelp() {
		$help = "<p>There are three types of files QIIME uses:
			<ol>
			<li>A map file</li>
			<li>A fasta formatted sequence file</li>
			<li>A sequence quality file</li>
			</ol></p>";
		$project = ($this->project) ? $this->project : $this->workflow->getNewProject();
		$fileTypes = $project->getFileTypes();
		foreach ($fileTypes as $fileType) {
			$help .= "<div class=\"hideable\" id=\"help_{$fileType->getHtmlId()}\">\n";
			$help .= $fileType->renderHelp();
			$help .= "</div>\n";
		}
		return $help;
	}
	public function renderSpecificStyle() {
		return "";
	}
	public function renderSpecificScript() {
		return "window.onload=function() {window.hideableFields = ['help'];displayHideables('{$this->getFileType()->getHtmlId()}');};";
	}
	public function getScriptLibraries() {
		return array();
	}
}
