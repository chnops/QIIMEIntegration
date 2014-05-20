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

class MakePhylogeny extends DefaultScript {

	public function initializeParameters() {
		$this->parameters['required'] = array(
		);
		$this->parameters['special'] = array(
		);
	}
	public function getScriptName() {
		return "make_phylogeny.py";
	}
	public function getScriptTitle() {
		return "Make phylogenetic tree";
	}
	public function getHtmlId() {
		return "make_phylogeny";
	}
	public function renderHelp() {
		return "<p>{$this->getScriptTitle()}</p><p>Once sequences have been aligned, QIIME will organize them for you into a phylogenetic tree</p>";
	}

}
