<?php

namespace Models\Scripts\QIIME;
use Models\Scripts\DefaultScript;
use Models\Scripts\Parameters\VersionParameter;
use Models\Scripts\Parameters\HelpParameter;
use Models\Scripts\Parameters\TextArgumentParameter;
use Models\Scripts\Parameters\TrueFalseParameter;
use Models\Scripts\Parameters\TrueFalseInvertedParameter;
use Models\Scripts\Parameters\NewFileParameter;
use Models\Scripts\Parameters\OldFileParameter;
use Models\Scripts\Parameters\ChoiceParameter;

class  extends DefaultScript {

	public function initializeParameters() {
//		$this->parameterRelationships->requireParam();
//		$this->parameterRelationships->makeOptional();
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
