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

class FilterAlignment extends DefaultScript {

	public function initializeParameters() {
		$this->parameters['required'] = array(
		);
		$this->parameters['special'] = array(
		);
	}
	public function getScriptName() {
		return "filter_alignment.py";
	}
	public function getScriptTitle() {
		return "Filter sequence alignment";
	}
	public function getHtmlId() {
		return "filter_alignment";
	}
	public function renderHelp() {
		return "<p>{$this->getScriptTitle()}</p><p>Typically, aligned sequences have a lot of identical bases on both the 3' and 5' end.  It will drasticaly reduce processing time if you filter out identical bases.</p>";
	}

}
