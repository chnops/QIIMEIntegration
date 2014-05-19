<?php

namespace Models\Scripts;

class OldFileParameter extends DefaultParameter {
	private $files;
	public function __construct($name, \Models\Project $project) {
		$this->name = $name;
		$this->files  = $project->retrieveAllUploadedFiles();
	}
	public function renderForForm() {
		$output = "<label for=\"{$this->name}\">{$this->name}<select name=\"{$this->name}\">\n";
		foreach ($this->files as $category => $fileNames) {
			$output .= "<optgroup label=\"{$category}\">\n";
			foreach ($fileNames as $fileName) {
				$selected = ($this->value == $fileName) ? " selected" : "";
				$output .= "<option value=\"{$fileName}\"{$selected}>{$fileName}</option>\n";
			}
			$output .= "</optgroup>\n";
		}
		$output .= "</select></label>\n";
		return $output;
	}
	public function isValueValid() {
		if (!$this->value) {
			return true;
		}
		foreach ($this->files as $category => $nameArray) {
			foreach ($nameArray as $fileName) {
				if ($this->value == $fileName) {
					return true;
				}
			}
		}
		return false;
	}
}
