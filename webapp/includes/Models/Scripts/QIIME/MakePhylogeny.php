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
			"--input_fp" => new OldFileParameter("--input_fp", $this->project),
		);
		$this->parameters['special'] = array(
			"--verbose" => new TrueFalseParameter("--verbose"),
			"--tree_method" => new ChoiceParameter("--tree_method", "fasttree", 
				array("clearcut", "clustalw", "fasttree_v1", "fasttree", "raxml_v730", "muscle")),
			"--result_fp" => new NewFileParameter("--result_fp", "_.tre"), // TODO dynamic default
			"--log_fp" => new NewFileParameter("--log_fp", ""),
			"--root_method" => new ChoiceParameter("--root_method", "tree_method_default",
				array("midpoint", "tree_method_default")),
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
