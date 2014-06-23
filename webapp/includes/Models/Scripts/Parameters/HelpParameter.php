<?php

namespace Models\Scripts\Parameters;
use \Models\Scripts\ScriptException;

class HelpParameter extends DefaultParameter {

	private $script;

	public function __construct(\Models\Scripts\ScriptI $script) {
		$this->name = "--help";
		$this->script = $script;
	}
	public function renderForOperatingSystem() {
		return "";
	}
	public function renderForForm($disabled, \Models\Scripts\ScriptI $script) {
		return "<a href=\"public/manual/{$this->script->getHtmlId()}.txt\" target=\"_blank\" class=\"button\">See manual page</a>";
	}

	public function acceptInput(array $input) {
		if (isset($input[$this->name])) {
			throw new ScriptException("Using the help parameter is not allowed");
		}
	}
}
