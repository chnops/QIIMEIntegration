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
	public function getScriptName() {
		return "make_phylogeny.py";
	}
	public function getScriptTitle() {
		return "Make phylogenetic tree";
	}
	public function getHtmlId() {
		return "make_phylogeny";
	}

	public function getInitialParameters() {
		$parameters = parent::getInitialParameters();

		$inputFp = new OldFileParameter("--input_fp", $this->project);
		$inputFp->requireIf();

		array_push($parameters,
			new Label("Required Parameters"),
			$inputFp,

			new Label("Optional Parameters"),
			new ChoiceParameter("--tree_method", "fasttree", 
				array("clearcut", "clustalw", "fasttree_v1", "fasttree", "raxml_v730", "muscle")),
			new ChoiceParameter("--root_method", "tree_method_default",
				array("midpoint", "tree_method_default")),

			new Label("Output Options"),
			new TrueFalseParameter("--verbose"),
			new NewFileParameter("--result_fp", "_.tre"),
			new NewFileParameter("--log_fp", "")
		);
		return $parameters;
	}
}
