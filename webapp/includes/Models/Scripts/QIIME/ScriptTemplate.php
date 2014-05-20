<?php

namespace Models\Scripts\QIIME;
use Models\Scripts\DefaultScript;
use Models\Scripts\VersionParameter;
use Models\Scripts\HelpParameter;
use Models\Scripts\TextArgumentParameter;
use Models\Scripts\TrueFalseParameter;
use Models\Scripts\TrueFalseInvertedParameter;
use Models\Scripts\NewFileParameter;
use Models\Scripts\OldFileParameter;
use Models\Scripts\ChoiceParameter;

class  extends DefaultScript {

	public function getInitialParameters() {
		return array(
			"--version" => new VersionParameter(),
			"--help" => new HelpParameter(),
		);
	}
	public function getScriptName() {
		return "Implement me!";
	}
	public function getScriptTitle() {
		return "dummy_script.py";
	}
	public function getHtmlId() {
		return "";
	}
	public function renderHelp() {
		return "Help for this script has not yet been implemented";
	}

}
