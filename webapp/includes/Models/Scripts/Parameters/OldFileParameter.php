<?php

namespace Models\Scripts\Parameters;
use \Models\Scripts\ScriptException;

class OldFileParameter extends DefaultParameter {
	private $project = NULL;
	public function __construct($name, \Models\Project $project) {
		$this->name = $name;
		$this->project = $project;
	}
	public function renderForForm($disabled) {
		$disabledString = ($disabled) ? " disabled" : "";
		$helper = \Utils\Helper::getHelper();
		$output = "{$this->name}<select name=\"{$this->name}\" size=\"5\"{$disabledString}>\n";

		$uploadedFiles = $this->project->retrieveAllUploadedFiles();
		if (!empty($uploadedFiles)) {
			$output .= "<option value=\"\">None</option>";
			$output .= "<optgroup label=\"uploaded files\" class=\"big\">\n";

			$uploadedFilesFormatted = $helper->categorizeArray($uploadedFiles, 'type', 'name');
			foreach ($uploadedFilesFormatted as $type=> $fileNames) {
				if (empty($fileNames)) {
					continue;
				}
				$output .= "<optgroup label=\"{$type} files\">\n";
				foreach ($fileNames as $fileName) {
					$valueAttr = "../uploads/{$fileName}";
					$selected = ($this->value == $valueAttr) ? " selected" : "";
					$output .= "<option value=\"{$valueAttr}\"{$selected}>uploads/" . htmlentities($fileName) . "</option>\n";
				}
				$output .= "</optgroup>\n";
			}
		}

		$generatedFiles = $this->project->retrieveAllGeneratedFiles();
		if (!empty($generatedFiles)) {
			$output .= "{$this->name}<optgroup label=\"generated files\" class=\"big\">\n";
			
			$generatedFilesFormatted = $helper->categorizeArray($generatedFiles, 'run_id', 'name');

			foreach ($generatedFilesFormatted as $runId => $fileNames) {
				if (empty($fileNames)) {
					continue;
				}
				$output .= "<optgroup label=\"from run {$runId}\">\n";
				foreach ($fileNames as $fileName) {
					$valueAttr = "../r{$runId}/{$fileName}";
					$selected = ($this->value == $valueAttr) ? " selected" : "";
					$output .= "<option value=\"{$valueAttr}\"{$selected}>generated/" . htmlentities($fileName) . "</option>\n";
				}
				$output .= "</optgroup>\n";
			}
		}
		$output .= "</select>\n"; // the </label> is here because if there is an error (below), the label needs to be closed early

		if (empty($uploadedFiles) && empty($generatedFiles)) {
			$output = "<em>You must <a href=\"index.php?step=upload\">upload</a>
			at least one file in order to use {$this->name}<em>";
		}
		return "<label for=\"{$this->name}\">" . $output . "</label>";
	}
}
