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
	public function getScriptName() {
		return "pick_rep_set.py";
	}
	public function getScriptTitle() {
		return "Pick representative sequences";
	}
	public function getHtmlId() {
		return "pick_rep_set";
	}

	public function initializeParameters() {
		parent::initializeParameters();

		$inputFile = new OldFileParameter("--input_file", $this->project);
		$referenceSeqsFp = new OldFileParameter("--reference_seqs_fp", $this->project, 
			'/macqiime/greengenes/gg_13_8_otus/rep_set/97_otus.fasta');
		$fastaFile = new OldFileParameter("--fasta_file", $this->project);

		$inputFile->requireIf();
		$fastaFile->requireIf($referenceSeqsFp, false);

		array_push($this->parameters,
			new Label("Required Parameters"),
			$inputFile,

			new Label("Optional Parameters"),
			$referenceSeqsFp,
			$fastaFile,
			new ChoiceParameter("--rep_set_picking_method", "first", 
				array("first", "random", "longest", "most_abundant")),

			new Label("Output options"),
			new TrueFalseParameter("--verbose"),
			new NewFileParameter("--log_fp", ""), 
			new NewFileParameter("--result_fp", "_rep_set.fasta"), // TODO dynamic default 
			new ChoiceParameter("--sort_by", "otu", array("otu", "seq_id"))
		);
	}
}
