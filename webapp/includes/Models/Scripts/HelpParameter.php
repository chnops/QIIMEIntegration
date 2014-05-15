<?php

namespace Models\Scripts;

class HelpParameter extends DefaultParameter {
	public function renderForOperatingSystem() {
		return "";
	}
	public function renderForForm() {
		// TODO get actual help doc page
		return "<a href=\"http://wikipedia.org\" target=\"_blank\" class=\"button\">See man page</a>";
	}
}
