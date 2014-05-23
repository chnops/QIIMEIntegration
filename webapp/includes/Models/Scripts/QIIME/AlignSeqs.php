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

class AlignSeqs extends DefaultScript {

	public function initializeParameters() {
		$inputFp = new OldFileParameter("--input_fasta_fp", $this->project);
		$this->parameterRelationships->requireParam($inputFp);

		$pairwiseAlignmentMethod = new ChoiceParameter("--pairwise_alignment_method", "uclust",
			array("muscle", "pair_hm", "clustal", "blast", "uclust", "mafft"));
		$alignmentMethod = new ChoiceParameter("--alignment_method", "pynast", 
			array("pynast", "infernal", "clustalw", "muscle", "mafft"));
		$blastDb = new OldFileParameter("--blast_db", $this->project);
			// TODO [default: created on-the-fly from template_alignment
		
		$this->parameterRelationships->allowParamIf($pairwiseAlignmentMethod, $alignmentMethod, "pynast");
		$this->parameterRelationships->allowParamIf($blastDb, $alignmentMethod, "pynast");

		$this->parameterRelationships->makeOptional(array(
			"--verbose" => new TrueFalseParameter("--verbose"),
			"--template_fp" => new OldFileParameter("--template_fp", $this->project),
				// TODO [default: /macqiime/greengenes/core_set_aligned.fasta.imputed]
			"--min_length" => new TextArgumentParameter("--min_length", "", "/.*/"),
				// TODO [default: 75% of the median input sequence length]
			"--min_percent_id" => new TextArgumentParameter("--min_percent_id", "0.75", "/.*/"),
			"--muscle_max_memory" => new TextArgumentParameter("--muscle_max_memory", "", "/.*/"),
			"--output_dir" => new NewFileParameter("--output_dir", "_aligned"), // TODO dynamic default
		));
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
