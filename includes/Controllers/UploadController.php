<?php
/*
 * Copyright (C) 2014 Aaron Sharp
 * Released under GNU GENERAL PUBLIC LICENSE Version 3, 29 June 2007
 */

namespace Controllers;

class UploadController extends Controller {
	private $fileType = NULL;
	private $url = "";

	public function getSubTitle() {
		return "Upload Input Files";
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
		$previousFilesFormatted = $this->helper->categorizeArray($previousFiles, 'type');

		foreach ($previousFilesFormatted as $fileType => $files) {
			$output .= "<h4 onclick=\"hideMe($(this).next())\">{$fileType} files</h4><div><ul>\n";
			foreach ($files as $file) {
				$output .= "<li>" . $this->helper->htmlentities($file['name']) . " ({$file['status']})</li>\n";
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
			$this->result = "The file you uploaded had an unrecognized type.";
			return;
		}
		else {
			$this->result = "";
		}

		$isDownload = isset($_POST['url']);
		$fileName = $this->getFileName($isDownload);
		if (!$fileName) {
			$this->isResultError = true;
			$this->result = "Unable to determine file name";
			return;
		}

		if ($this->fileNameExists($fileName)) {
			$this->isResultError = true;
			$this->result .= "You have already uploaded a file with that file name. File names must be unique";
			return;
		}
		
		if ($isDownload) {
			try {
				$this->result = $this->downloadFile($this->url, $fileName);
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
			$this->uploadFile($_FILES['file']);
		}
	}

	public function getFileName($isDownload) {
		$fileName = "";
		if ($isDownload && isset($_POST['url'])) {
			$this->url = $_POST['url'];
			$urlParts = explode('/', $this->url);
			while (!$fileName && $urlParts) {
				$fileName = array_pop($urlParts);
			}
		}
		else {
			if (isset($_FILES['file']) && isset($_FILES['file']['name'])) {
				$fileName = $_FILES['file']['name'];
			}
		}
		return $fileName;
	}

	public function fileNameExists($fileName) {
		if (!$this->project) {
			return false;
		}
		$pastFiles = $this->project->retrieveAllUploadedFiles();
		foreach ($pastFiles as $extantFile) {
			if ($extantFile['name'] == $fileName) {
				return true;
			}
		}
		return false;
	}

	public function setFileType(\Models\FileType $fileType) {
		$this->fileType = $fileType;
	}
	public function getFileType() {
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

	public function downloadFile($url, $fileName) {
		$output = "File downloaded has started.";
		$consoleOutput = $this->project->receiveDownloadedFile($url, $fileName, $this->getFileType());
		if ($consoleOutput) {
			$output .= "<br/>The console returned the following output:<br/>" . $this->helper->htmlentities($consoleOutput);
		}
		return $output;
	}

	public function uploadFile(array $file) {
		if ($file['error'] > 0) {
			$this->isResultError = true;
			$fileUploadErrors = new FileUploadErrors();
			$this->result = "There was an error uploading your file: " . $fileUploadErrors->getErrorMessage($file['error']);
			return;
		}

		$givenName = $file['name'];
		try {
			$this->project->receiveUploadedFile($givenName, $file['tmp_name'],  $file['size'], $this->getFileType());
			$this->result = "File " . $this->helper->htmlentities($givenName) . " successfully uploaded!";
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
				<input type=\"file\" name=\"file\"{$this->disabled}/></label>
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
		$help = "<p>There are four types of files QIIME uses:
			<ol>
			<li>A map file</li>
			<li>A fasta formatted sequence file</li>
			<li>A sequence quality file</li>
			<li>A fastq sequence-quality file</li>
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
