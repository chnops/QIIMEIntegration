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

class MakeOtuTable extends DefaultScript {

	public function initializeParameters() {
		$this->parameters = array(
			"--version" => new VersionParameter(),
			"--help" => new HelpParameter(),
		);
	}
	public function getScriptName() {
		return "make_otu_table.py";
	}
	public function getScriptTitle() {
		return "Make OTU table";
	}
	public function getHtmlId() {
		return "make_otu_table";
	}
	public function renderHelp() {
		return "<p>{$this->getScriptTitle()}</p><p>An OTU table contains organized information about the abundance of different OTUs in a set of sequences.</p>";
	}

}
