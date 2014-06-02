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

class PickRepSet extends DefaultScript {

	public function initializeParameters() {
		$inputFile = new OldFileParameter("--input_file", $this->project);
		$inputFile->requireIf();

		array_push($this->parameters,
			new Label("<p><strong>Required Parameters</strong></p>"),
			$inputFile,

			new Label("<p><strong>Optional Parameters</strong></p>"),
			new OldFileParameter("--reference_seqs_fp", $this->project),
			new OldFileParameter("--fasta_file", $this->project), // TODO REQUIRED if not picking against a reference set
			new ChoiceParameter("--rep_set_picking_method", "first", 
				array("first", "random", "longest", "most_abundant")),

			new Label("<strong>Output options</strong>"),
			new TrueFalseParameter("--verbose"),
			new NewFileParameter("--log_fp", ""), 
			new NewFileParameter("--result_fp", "_rep_set.fasta"), // TODO dynamic default 
			new ChoiceParameter("--sort_by", "otu", array("otu", "seq_id"))
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
		ob_start();
		echo "<p>{$this->getScriptTitle()}</p>";
		include 'views/pick_rep_sets.html';
		return ob_get_clean();
	}
}
