<?php

namespace Models\Scripts\Parameters;
use \Models\Scripts\ScriptException;

class VersionParameter extends DefaultParameter {

	private $versionString = "";
	public function __construct(\Models\Scripts\ScriptI $script) {
		$this->name = "--version";
		ob_start();
		include "versions/{$script->getHtmlId()}.txt";
		$this->versionString = preg_replace("/\n+/", "\\n", trim(ob_get_clean()));
	}

	public function renderForOperatingSystem() {
		return "";
	}

	public function renderForForm($disabled, \Models\Scripts\ScriptI $script) {
		return "<a class=\"button\" onclick=\"alert('{$this->versionString}');\">Version</a>";
	}

	public function getVersionString() {
		return $this->versionString;
	}
	public function setVersionString($versionString) {
		$this->versionString = $versionString;
	}
}
