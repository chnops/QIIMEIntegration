<?php

namespace Models\Scripts\Parameters;
use \Models\Scripts\ScriptException;

class HelpParameter extends DefaultParameter {

	private $script;

	public function __construct() {
		$this->name = "--help";
	}
	public function renderForOperatingSystem() {
		return "";
	}
	public function renderForForm($disabled, \Models\Scripts\ScriptI $script) {
		return "<a href=\"public/manual/{$script->getHtmlId()}.txt\" target=\"_blank\" class=\"button\">See manual page</a>";
	}
}
