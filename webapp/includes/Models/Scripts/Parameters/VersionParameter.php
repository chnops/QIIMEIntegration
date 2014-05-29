<?php

namespace Models\Scripts\Parameters;
use \Models\Scripts\ScriptException;

class VersionParameter extends DefaultParameter {

	private $versionString = "";
	public function __construct(\Models\Project $project, $scriptName) {
		$this->name = "--version";
		$this->versionString = $project->getVersion($scriptName);
	}

	public function renderForOperatingSystem() {
		return "";
	}

	public function renderForForm($disabled) {
		// TODO get actual version info
		return "<a class=\"button\" onclick=\"alert('{$this->versionString}');\">Version</a>";
	}

	public function acceptInput(array $input) {
		if (isset($input[$this->name])) {
			throw new ScriptException("Using the version parameter is not allowed");
		}
	}
}
