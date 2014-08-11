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

class AlignSeqs extends DefaultScript {
	public function getScriptName() {
		return "align_seqs.py";
	}
	public function getScriptTitle() {
		return "Align sequences";
	}
	public function getHtmlId() {
		return "align_seqs";
	}

	public function getInitialParameters() {
		$parameters = parent::getInitialParameters();

		$inputFp = new OldFileParameter("--input_fasta_fp", $this->project);
		$alignmentMethod = new ChoiceParameter("--alignment_method", "pynast", 
			array("pynast", "infernal", "clustalw", "muscle", "mafft"));
		$blastDb = new OldFileParameter("--blast_db", $this->project);
			// TODO [default: created on-the-fly from template_alignment
		$pairwiseAlignmentMethod = new ChoiceParameter("--pairwise_alignment_method", "uclust",
			array("muscle", "pair_hmm", "clustal", "blast", "uclust", "mafft"));
		$minPercentId = new TextArgumentParameter("--min_percent_id", "0.75", TextArgumentParameter::PATTERN_PROPORTION);
		$muscleMaxMemory = new TextArgumentParameter("--muscle_max_memory", "", TextArgumentParameter::PATTERN_PROPORTION);

		$inputFp->requireIf();
		$pairwiseAlignmentMethod->excludeButAllowIf($alignmentMethod, "pynast");
		$blastDb->excludeButAllowIf($alignmentMethod, "pynast");
		$minPercentId->excludeButAllowIf($alignmentMethod, "pynast");
		$muscleMaxMemory->excludeButAllowIf($alignmentMethod, "muscle");

		array_push($parameters,
			new Label("Required Parameters"),
			$inputFp,

			new Label("Optional Parameters"),
			new OldFileParameter("--template_fp", $this->project, '/macqiime/greengenes/core_set_aligned.fasta.imputed'),
			new TextArgumentParameter("--min_length", "", TextArgumentParameter::PATTERN_PROPORTION),
			// TODO [default: 75% of the median input sequence length] (is the pattern correct?)
			$alignmentMethod,
			$pairwiseAlignmentMethod,
			$blastDb,
			$minPercentId,
			$muscleMaxMemory,

			new Label("Output Options"),
			new TrueFalseParameter("--verbose"),
			new NewFileParameter("--output_dir", "_aligned", $isDir = true) // TODO dynamic default
		);

		return $parameters;
	}
}
