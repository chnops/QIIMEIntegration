<?php

namespace Controllers;

class UploadController extends Controller {

	protected $subTitle = "Upload Input Files";

	private $fileType = NULL;

	public function retrievePastResults() {
		$output = "";
		$previousFiles = $this->project->retrieveAllUploadedFiles();
		if ($previousFiles) {
			$output .= "<h3>Previously Uploaded files:</h3>\n";
			foreach ($previousFiles as $fileType => $arrayOfFiles) {
				$fileTypeTitle = $this->project->getFileTypeFromShortName($fileType)->getName();
				$output .= "<h4>{$fileTypeTitle} Files</h4><ul>\n";
				foreach ($arrayOfFiles as $file) {
					$output .= "<li>" . htmlentities($file) . "</li>\n";
				}
				$output .= "</ul><hr class=\"small\"/>\n";
			}
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
		$this->fileType = $this->project->getFileTypeFromShortName($_POST['type']);
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
			// TODO move_uploaded_file($systemFileName)
			$this->result = "File " . htmlentities($fileName) . " successfully uploaded!";
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
			$this->help .= $fileType->renderHelp();
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
				<select name=\"type\" onchange=\"displayHelp('script_help_'+this[this.selectedIndex].getAttribute('value'));\"{$this->disabled}>";

		$selectedFileType = ($this->fileType) ? $this->fileType->getShortName() : "";
		$project = ($this->project) ? $this->project : $this->workflow->getNewProject();
		foreach ($project->getFileTypes() as $fileType) {
			$selected = ($fileType->getShortName() == $selectedFileType) ? " SELECTED" : "";
			$output .= "<option value=\"{$fileType->getShortName()}\"{$selected}>{$fileType->getName()}</option>";
		}
	
		$output .= "</select></label>
			<script type=\"text/javascript\">displayHelp('script_help_{$selectedFileType}');</script>
			<button type=\"submit\"{$this->disabled}>Upload</button>
			</form>";
		return $output;
	}
}
