<?php

namespace Controllers;

class UploadController extends Controller {

	protected $subTitle = "Upload Input Files";

	private $fileType = NULL;

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
		
		// TODO if is valid form
		$this->fileType = $this->project->getFileTypeFromHtmlId($_POST['type']);
		$this->uploadFile($_FILES['file'], $this->fileType);
	}

	private function uploadFile(array $file, \Models\FileType $fileType) {
		if ($file['error'] > 0) {
			$this->isResultError = true;
			$fileUploadErrors = new FileUploadErrors();
			$this->result = "There was an error uploading your file: " . $fileUploadErrors->getErrorMessage($file['error']);
			return;
		}
		// TODO if size/type are valid

		$fileName = $file['name'];
		$systemFileName = $this->project->receiveUploadedFile($fileName, $fileType);	
		if (!$systemFileName) {
			$this->isResultError = true;
			$this->result = "There was an error adding your file to the project.";
		}
		else {
			$moveResult = move_uploaded_file($file['tmp_name'], $systemFileName);
			if ($moveResult) {
				$this->result = "File " . htmlentities($fileName) . " successfully uploaded!";
				$this->project->confirmUploadedFile();	
			}
			else {
				$this->isResultError = true;
				$this->result = "There was an error moving your file on to the system.";
				$this->project->forgetUploadedFile();
			}
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
			<script type=\"text/javascript\">
			window.onload=function() {window.hideableFields = ['help'];displayHideables('{$selectedFileType}');};</script>
			<button type=\"submit\"{$this->disabled}>Upload</button>
			</form>";
		return $output;
	}
}
