<?php

namespace Models\Scripts;

class VersionParameter extends DefaultParameter {

	private $versionString = "";
	public function __construct(\Models\Project $project, $scriptName) {
		$this->versionString = $project->getVersion($scriptName);
	}

	public function renderForOperatingSystem() {
		return "";
	}

	public function renderForForm() {
		// TODO get actual version info
		return "<a class=\"button\" onclick=\"alert('{$this->versionString}');\">Version</a>";
	}
}
