<?php

namespace Models\Scripts;

class VersionParameter extends DefaultParameter {

	public function renderForOperatingSystem() {
		return "";
	}

	public function renderForForm() {
		// TODO get actual version info
		return "<a class=\"button\" onclick=\"alert('Version Info');\">Version</a>";
	}
}
