<?php

namespace Models\Scripts;

class OldFileParameter extends DefaultParameter {
	private $project = NULL;
	public function __construct($name, \Models\Project $project) {
		$this->name = $name;
		$this->project = $project;
	}
	public function renderForOperatingSystem() {
		if (!$this->value) {
			return "";
		}

		$fileParts = explode("/", $this->value);
		$isUploadedFile = ($fileParts[0] == "uploaded");
		if ($isUploadedFile) {
			$systemFileName = $this->project->getSystemNameForUploadedFile($fileParts[1]);
			if (!$systemFileName) {
				throw new ScriptException("Unable to locate the given file: " . htmlentities($this->value));
			}
			$systemFileName = "../uploads/" . $systemFileName;
		}
		else {
			$systemFileName = "../" . $this->value;
		}

		$separator = (strlen($this->name) == 2) ? " " : "=";
		return $this->name . $separator . "'" . $systemFileName . "'";
	}
	public function renderForForm() {
		$output = "<label for=\"{$this->name}\">{$this->name}<select name=\"{$this->name}\" size=\"5\">\n";

		$uploadedFiles = $this->project->retrieveAllUploadedFiles();
		if (!empty($uploadedFiles)) {
			$output .= "<optgroup label=\"uploaded files\" class=\"big\">\n";

			$uploadedFilesFormatted = array();
			foreach ($uploadedFiles as $fileArray) {
				$fileType = $fileArray['type'];
				if (!isset($uploadedFilesFormatted[$fileType])) {
					$uploadedFilesFormatted[$fileType] = array();
				}
				$uploadedFilesFormatted[$fileType][] = $fileArray['name'];
			}

			foreach ($uploadedFilesFormatted as $type=> $fileNames) {
				if (empty($fileNames)) {
					continue;
				}
				$output .= "<optgroup label=\"{$type} files\">\n";
				foreach ($fileNames as $fileName) {
					$selected = ($this->value == "uploaded/{$fileName}") ? " selected" : "";
					$output .= "<option value=\"uploaded/{$fileName}\"{$selected}>" . htmlentities($fileName) . "</option>\n";
				}
				$output .= "</optgroup>\n";
			}
		}

		$generatedFiles = $this->project->retrieveAllGeneratedFiles();
		if (!empty($generatedFiles)) {
			$output .= "<optgroup label=\"generated files\" class=\"big\">\n";
			
			$generatedFilesFormatted = array();
			foreach ($generatedFiles as $fileArray) {
				$runId = $fileArray['run_id'];
				if (!isset($generatedFilesFormatted[$runId])) {
					$generatedFilesFormatted[$runId] = array();
				}
				$generatedFilesFormatted[$runId][] = $fileArray['name'];
			}

			foreach ($generatedFilesFormatted as $runId => $fileNames) {
				if (empty($fileNames)) {
					continue;
				}
				$output .= "<optgroup label=\"from run {$runId}\">\n";
				foreach ($fileNames as $fileName) {
					$selected = ($this->value == "r{$runId}/{$fileName}") ? " selected" : "";
					$output .= "<option value=\"r{$runId}/{$fileName}\"{$selected}>" . htmlentities($fileName) . "</option>\n";
				}
				$output .= "</optgroup>\n";
			}
		}

		$output .= "</select></label>\n";
		return $output;
	}
}
