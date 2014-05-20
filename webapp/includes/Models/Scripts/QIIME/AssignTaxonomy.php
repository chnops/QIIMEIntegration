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

class AssignTaxonomy extends DefaultScript {

	public function initializeParameters() {
		$this->parameters['required'] = array(
		);
		$this->parameters['special'] = array(
		);
	}
	public function getScriptName() {
		return "assign_taxonomy.py";
	}
	public function getScriptTitle() {
		return "Assign taxonomies";
	}
	public function getHtmlId() {
		return "assign_taxonomy";
	}
	public function renderHelp() {
		return "<p>{$this->getScriptTitle()}</p><p>This script takes OTUs and assigns them to a real-world taxonomy.  To do this, it uses sequence databases.</p>";
	}

}
