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
use Models\Scripts\Parameters\Label;

class MakePhylogeny extends DefaultScript {

	public function initializeParameters() {
		$inputFp = new OldFileParameter("--input_fp", $this->project);
		$inputFp->requireIf();

		$this->parameterRelationships->makeOptional(array(
			"1" => new Label("<p><strong>Required Parameters</strong></p>"),
			$inputFp->getName() => $inputFp,
			"2" => new Label("<p><strong>Optional Parameters</strong></p>"),
			"--verbose" => new TrueFalseParameter("--verbose"),
			"--tree_method" => new ChoiceParameter("--tree_method", "fasttree", 
				array("clearcut", "clustalw", "fasttree_v1", "fasttree", "raxml_v730", "muscle")),
			"--result_fp" => new NewFileParameter("--result_fp", "_.tre"), // TODO dynamic default
			"--log_fp" => new NewFileParameter("--log_fp", ""),
			"--root_method" => new ChoiceParameter("--root_method", "tree_method_default",
				array("midpoint", "tree_method_default")),
		));
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
