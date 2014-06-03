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

	public function initializeParameters() {
		$inputFp = new OldFileParameter("--input_fasta_fp", $this->project);
		$inputFp->requireIf();

		$alignmentMethod = new ChoiceParameter("--alignment_method", "pynast", 
//			array("pynast", "infernal", "clustalw", "muscle", "mafft")); // TODO not supported in all versions
			array("pynast", "muscle", "infernal"));

		$blastDb = new OldFileParameter("--blast_db", $this->project);
			// TODO [default: created on-the-fly from template_alignment
		$pairwiseAlignmentMethod = new ChoiceParameter("--pairwise_alignment_method", "uclust",
			array("muscle", "pair_hmm", "clustal", "blast", "uclust", "mafft"));
		$minPercentId = new TextArgumentParameter("--min_percent_id", "0.75", "/.*/");
		$blastDb->excludeButAllowIf($alignmentMethod, "pynast");
		$pairwiseAlignmentMethod->excludeButAllowIf($alignmentMethod, "pynast");
		$minPercentId->excludeButAllowIf($alignmentMethod, "pynast");

		$muscleMaxMemory = new TextArgumentParameter("--muscle_max_memory", "", "/.*/");
		$muscleMaxMemory->excludeButAllowIf($alignmentMethod, "muscle");

		array_push($this->parameters,
			new Label("<p><strong>Required Parameters</strong></p>"),
			$inputFp,
			new Label("<p><strong>Optional Parameters</strong></p>"),
			new OldFileParameter("--template_fp", $this->project),
			// TODO [default: /macqiime/greengenes/core_set_aligned.fasta.imputed]
			new TextArgumentParameter("--min_length", "", "/.*/"),
			// TODO [default: 75% of the median input sequence length]
			$alignmentMethod,
			$pairwiseAlignmentMethod,
			$blastDb,
			$minPercentId,
			$muscleMaxMemory,
			new Label("<p><strong>Output Options</strong></p>"),
			new TrueFalseParameter("--verbose"),
			new NewFileParameter("--output_dir", "_aligned") // TODO dynamic default
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
		ob_start();
		echo "<p>{$this->getScriptTitle()}</p><p>The initial step in performing phylogeny analysis is aligning the sequences.</p>";
		include 'views/align_seqs.html';
		return ob_get_clean();
	}
}
