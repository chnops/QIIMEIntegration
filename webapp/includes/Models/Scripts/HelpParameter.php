<?php

namespace Models\Scripts;

class HelpParameter extends DefaultParameter {

	private $script;

	public function __construct(ScriptI $script) {
		$this->script = $script;
	}
	public function renderForOperatingSystem() {
		return "";
	}
	public function renderForForm() {
		return "<a href=\"public/manual/{$this->script->getHtmlId()}.txt\" target=\"_blank\" class=\"button\">See manual page</a>";
	}
}
