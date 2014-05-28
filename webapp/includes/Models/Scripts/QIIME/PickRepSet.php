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

		$this->parameterRelationships->makeOptional(array(
			"1" => new Label("<p><strong>Required Parameters</strong></p>"),
			$inputFile->getName() => $inputFile,
			"2" => new Label("<p><strong>Optional Parameters</strong></p>"),
			"--verbose" => new TrueFalseParameter("--verbose"),
			"--fasta_file" => new OldFileParameter("--fasta_file", $this->project), // TODO REQUIRED if not picking against a reference set
			"--rep_set_picking_method" => new ChoiceParameter("--rep_set_picking_method", "first", 
				array("random", "longest", "most_abundant", "first")),
			"--result_fp" => new NewFileParameter("--result_fp", "_rep_set.fasta"), // TODO dynamic default 
			"--log_fp" => new NewFileParameter("--log_fp", ""), 
			"--sort_by" => new ChoiceParameter("--sort_by", "otu", array("otu", "seq_id")),
			"--reference_seqs_fp" => new OldFileParameter("--reference_seqs_fp", $this->project),
		));
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
