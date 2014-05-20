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

class PickOtus extends DefaultScript {

	public function initializeParameters() {
		$this->parameters['required'] = array(
		);
		$this->parameters['special'] = array(
		);
	}
	public function getScriptName() {
		return "pick_otus.py";
	}
	public function getScriptTitle() {
		return "Pick OTUs";
	}
	public function getHtmlId() {
		return "pick_otus";
	}
	public function renderHelp() {
		return "<p>{$this->getScriptTitle()}</p><p>OTU stands for Operational Taxonomic Unit</p>";
	}

}
