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

class PickRepSet extends DefaultScript {

	public function initializeParameters() {
		$this->parameters = array(
			"--version" => new VersionParameter(),
			"--help" => new HelpParameter(),
		);
	}
	public function getScriptName() {
		return "pick_rep_set.py";
	}
	public function getScriptTitle() {
		return "Pick Representative OTU sequence";
	}
	public function getHtmlId() {
		return "pick_rep_set";
	}
	public function renderHelp() {
		return "<p>{$this->getScriptTitle()}</p><p>Once you have organized sequence reads into OTUs, it is efficient to select one read that represents the whole OTU.</p>";
	}
}
