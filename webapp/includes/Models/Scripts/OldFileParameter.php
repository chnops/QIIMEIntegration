<?php

namespace Models\Scripts;

class OldFileParameter extends DefaultParameter {
	private $project = NULL;
	private $files = array();
	public function __construct($name, \Models\Project $project) {
		$this->name = $name;
		$this->project = $project;
	}
	public function renderForOperatingSystem() {
		if (!$this->value) {
			return "";
		}
		$systemFileName = $this->project->getSystemFileName($this->value);
		if (!$systemFileName) {
			throw new ScriptException("Unable to locate the given file: " . htmlentities($this->value));
		}

		$separator = (strlen($this->name) == 2) ? " " : "=";
		return $this->name . $separator . "'" . $systemFileName . "'";
	}
	public function renderForForm() {
		if (empty($this->files)) {
			$this->files = $this->project->retrieveAllUploadedFiles();
		}
		$output = "<label for=\"{$this->name}\">{$this->name}<select name=\"{$this->name}\">\n";
		$output .= "<option value=\"\">--Selected a file--</option>\n";
		foreach ($this->files as $category => $fileNames) {
			$output .= "<optgroup label=\"{$category} files\">\n";
			foreach ($fileNames as $fileName) {
				$selected = ($this->value == $fileName) ? " selected" : "";
				$output .= "<option value=\"{$fileName}\"{$selected}>" . htmlentities($fileName) . "</option>\n";
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
		if (empty($this->files)) {
			$this->files = $this->project->retrieveAllUploadedFiles();
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
