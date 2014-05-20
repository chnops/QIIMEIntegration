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

class AlignSeqs extends DefaultScript {

	public function initializeParameters() {
		$this->parameters['required'] = array(
		);
		$this->parameters['special'] = array(
		);
	}
	public function getScriptName() {
		return "align_seqs.py";
	}
	public function getScriptTitle() {
		return "Align sequences";
	}
	public function getHtmlId() {
		return "align_seqs";
	}
	public function renderHelp() {
		return "<p>{$this->getScriptTitle()}</p><p>The initial step in performing phylogeny analysis is aligning the sequences.</p>";
	}

}
