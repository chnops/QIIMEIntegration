<?php
/*
 * Copyright (C) 2014 Aaron Sharp
 * Released under GNU GENERAL PUBLIC LICENSE Version 3, 29 June 2007
 */

namespace Models\Scripts\Parameters;
use \Models\Scripts\ScriptException;

class OldFileParameter extends DefaultParameter {

	private $project = NULL;
	public function __construct($name, \Models\ProjectI $project, $default = "") {
		$this->name = $name;
		$this->project = $project;
		$this->value = $default;
	}

	public function getProject() {
		return $this->project;
	}
	public function setProject(\Models\ProjectI $project) {
		$this->project = $project;
	}

	public function renderForForm($disabled, \Models\Scripts\ScriptI $script) {
		$helper = \Utils\Helper::getHelper();
		$disabledString = ($disabled) ? " disabled" : "";
		$output = "{$this->name} <a class=\"param_help\" id=\"{$this->getJsVar($script->getJsVar())}\">&amp;</a><select name=\"{$this->name}\" size=\"5\"{$disabledString}>\n";

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
				asort($fileNames);
				foreach ($fileNames as $fileName) {
					$valueAttr = "../uploads/{$fileName}";
					$selected = ($this->value == $valueAttr) ? " selected" : "";
					$output .= "<option value=\"{$valueAttr}\"{$selected}>uploads/" . $helper->htmlentities($fileName) . "</option>\n";
				}
				$output .= "</optgroup>\n";
			}
		}

		$generatedFiles = $this->project->retrieveAllGeneratedFiles();
		if (!empty($generatedFiles)) {
			$output .= "<optgroup label=\"generated files\" class=\"big\">\n";
			
			$generatedFilesFormatted = $helper->categorizeArray($generatedFiles, 'run_id', 'name');

			foreach ($generatedFilesFormatted as $runId => $fileNames) {
				if (empty($fileNames)) {
					continue;
				}
				$output .= "<optgroup label=\"from run {$runId}\">\n";
				asort($fileNames);
				foreach ($fileNames as $fileName) {
					$valueAttr = "../r{$runId}/{$fileName}";
					$selected = ($this->value == $valueAttr) ? " selected" : "";
					$output .= "<option value=\"{$valueAttr}\"{$selected}>generated/" . $helper->htmlentities($fileName) . "</option>\n";
				}
				$output .= "</optgroup>\n";
			}
		}

		$builtInFiles = $this->project->retrieveAllBuiltInFiles();
		if (!empty($builtInFiles)) {
			$output .= "<optgroup label=\"built in files\" class=\"big\">\n";
			$output .= "<optgroup>\n";

			foreach ($builtInFiles as $fileName) {
				$selected = ($this->value == $fileName) ? " selected" : "";
				$output .= "<option value=\"{$fileName}\"{$selected}>" . $helper->htmlentities($fileName) . "</option>\n";
			}
			$output .= "</optgroup>\n";
		}

		$output .= "</select>\n";

		if (empty($uploadedFiles) && empty($generatedFiles) && empty($builtInFiles)) {
			$output = "<em>You must <a href=\"index.php?step=upload\">upload</a>
			at least one file in order to use {$this->name}<em>";
		}
		return "<label for=\"{$this->name}\">" . $output . "</label>";
	}
}
