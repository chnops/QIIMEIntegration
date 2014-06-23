<?php

namespace Models\Scripts\Parameters;
use \Models\Scripts\ScriptException;

class VersionParameter extends DefaultParameter {

	private $versionString = "";
	public function __construct(\Models\Scripts\ScriptI $script) {
		$this->name = "--version";
		ob_start();
		include "public/versions/{$script->getHtmlId()}.txt";
		$this->versionString = preg_replace("/\n+/", "\\n", trim(ob_get_clean()));
	}

	public function renderForOperatingSystem() {
		return "";
	}

	public function renderForForm($disabled, \Models\Scripts\ScriptI $script) {
		return "<a class=\"button\" onclick=\"alert('{$this->versionString}');\">Version</a>";
	}

	public function acceptInput(array $input) {
		if (isset($input[$this->name])) {
			throw new ScriptException("Using the version parameter is not allowed");
		}
	}
}
