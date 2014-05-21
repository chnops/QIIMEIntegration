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

class PickRepSet extends DefaultScript {

	public function initializeParameters() {
		$this->parameters['required'] = array(
			"--input_file" => new OldFileParameter("--input_file", $this->project),
		);
		$this->parameters['special'] = array(
			"--verbose" => new TrueFalseParameter("--verbose"),
			"--fasta_file" => new OldFileParameter("--fasta_file", $this->project), // TODO REQUIRED if not picking against a reference set
			"--rep_set_picking_method" => new ChoiceParameter("--rep_set_picking_method", "first", 
				array("random", "longest", "most_abundant", "first")),
			"--result_fp" => new NewFileParameter("--result_fp", "_rep_set.fasta"), // TODO dynamic default 
			"--log_fp" => new NewFileParameter("--log_fp", ""), 
			"--sort_by" => new ChoiceParameter("--sort_by", "otu", array("otu", "seq_id")),
			"--reference_seqs_fp" => new OldFileParameter("--reference_seqs_fp", $this->project),
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
		return "<p>{$this->getScriptTitle()}</p><p>Once you have organized sequence reads into OTUs, it is efficient to select one read that represents the whole OTU.</p>";
	}
}
