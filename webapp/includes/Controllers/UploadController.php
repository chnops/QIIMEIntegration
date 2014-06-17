<?php

namespace Controllers;

class UploadController extends Controller {

	protected $subTitle = "Upload Input Files";

	private $fileType = NULL;
	private $url = "";

	public function retrievePastResults() {
		$output = "";
		$previousFiles = $this->project->retrieveAllUploadedFiles();
		if (empty($previousFiles)) {
			return $output;
		}

		$output .= "<h3>Previously Uploaded files:</h3>\n";
		$helper = \Utils\Helper::getHelper();
		$previousFilesFormatted = $helper->categorizeArray($previousFiles, 'type', 'name');

		foreach ($previousFilesFormatted as $fileType => $fileNames) {
			$output .= "<h4>{$fileType} files</h4><ul>\n";
			foreach ($fileNames as $fileName) {
				$output .= "<li>" . htmlentities($fileName) . "</li>\n";
			}
			$output .= "</ul><hr class=\"small\"/>\n";
		}
		return $output;
	}

	public function parseInput() {
		if (!$this->username || !$this->project) {
			$this->disabled = " disabled";
			$this->isResultError = true;
			$this->hasResult = true;
			$this->result = "In order to upload files, you must be logged in and have a project selected.";
			return;
		}
		if (!isset($_POST['step']) ) {
			return;
		}
		$this->hasResult = true;

		$this->fileType = $this->project->getFileTypeFromHtmlId($_POST['type']);
		if (!$this->fileType) {
			$this->isResultError = true;
			$this->result = "A the file you uploaded had an unrecognized type.<br/>";
		}
		else {
			$this->result = "";
		}
		
		// TODO if is valid form

		$isDownload = isset($_POST['url']);
		if ($isDownload) {
			$this->url = $_POST['url'];
			$fileName = explode('/', $this->url);
			$fileName = array_pop($fileName);
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
				$this->result = $this->downloadFile($this->url, $fileName, $this->fileType);
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
			$this->uploadFile($_FILES['file'], $this->fileType);
		}
	}

	private function downloadFile($url, $fileName, \Models\FileType $fileType) {
		$output = "File downloaded successfully.";
		$consoleOutput = $this->project->receiveDownloadedFile($url, $fileName, $fileType);
		if ($consoleOutput) {
			$output .= "<br/>The console returned the following output:<br/>" . htmlentities($consoleOutput);
		}
		return $output;
	}

	private function uploadFile(array $file, \Models\FileType $fileType) {
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
			$this->result = "File " . htmlentities($givenName) . " successfully uploaded!";
		}
		catch (\Exception $ex) {
			$this->isResultError = true;
			$this->result = $ex->getMessage();
		}
	}

	public function getInstructions() {
		$this->help .= "<p>There are three types of files QIIME uses:
			<ol>
			<li>A map file</li>
			<li>A fasta formatted sequence file</li>
			<li>A sequence quality file</li>
			</ol></p>";
		$project = ($this->project) ? $this->project : $this->workflow->getNewProject();
		$fileTypes = $project->getFileTypes();
		if (!$this->fileType) {
			$this->fileType = $fileTypes[0];
		}
		foreach ($fileTypes as $fileType) {
			$this->help .= "<div class=\"hideable\" id=\"help_{$fileType->getHtmlId()}\">\n";
			$this->help .= $fileType->renderHelp();
			$this->help .= "</div>\n";
		}
		return "<p>The first step to any project is to upload your files.</p>";
	}
	public function getForm() {
		$output = "
			<form method=\"POST\" action=\"index.php\" enctype=\"multipart/form-data\">
				<input type=\"hidden\" name=\"step\" value=\"{$this->step}\"/>
				<label for=\"file\">Select a file to upload:
				<input type=\"file\" name=\"file\"/{$this->disabled}></label>
				<label for=\"type\">File type:
				<select name=\"type\" onchange=\"displayHideables(this[this.selectedIndex].getAttribute('value'));\"{$this->disabled}>";

		$project = ($this->project) ? $this->project : $this->workflow->getNewProject();
		$fileTypes = $project->getFileTypes();
		$defaultFileType = $fileTypes[0];
		$selectedFileType = ($this->fileType) ? $this->fileType->getHtmlId() : $defaultFiletypes->getHtmlId();
		foreach ($fileTypes as $fileType) {
			$selected = ($fileType->getHtmlId() == $selectedFileType) ? " selected" : "";
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
			$selected = ($fileType->getHtmlId() == $selectedFileType) ? " selected" : "";
			$output .= "<option value=\"{$fileType->getHtmlId()}\"{$selected}>{$fileType->getName()}</option>";
		}
		$output .= "</select></label>
			<script type=\"text/javascript\">
			window.onload=function() {window.hideableFields = ['help'];displayHideables('{$selectedFileType}');};</script>
			<button type=\"submit\"{$this->disabled}>Download</button>
			</form>";
		return $output;
	}
}
