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

class AlignSeqs extends DefaultScript {

	public function initializeParameters() {
		$this->parameters['required'] = array(
			"--input_fasta_fp" => new OldFileParameter("--input_fasta_fp", $this->project),
		);
		$pairwiseAlignmentMethod = new ChoiceParameter("--pairwise_alignment_method", "uclust",
			array("muscle", "pair_hm", "clustal", "blast", "uclust", "mafft"));
		$alignmentMethod = new ChoiceParameter("--alignment_method", "pynast", 
			array("pynast", "infernal", "clustalw", "muscle", "mafft"));
		$blastDb = new OldFileParameter("--blast_db", $this->project);
		$this->parameterDependencyRelationships[$alignmentMethod->getName()] = array(
			"pynast" => array($pairwiseAlignmentMethod->getName(), $blastDb->getName())			
		);
		$this->parameters['special'] = array(
			"--verbose" => new TrueFalseParameter("--verbose"),
			$alignmentMethod->getName() => $alignmentMethod,
			$pairwiseAlignmentMethod->getName() => $pairwiseAlignmentMethod,
			"--template_fp" => new OldFileParameter("--template_fp", $this->project),
				// TODO [default: /macqiime/greengenes/core_set_aligned.fasta.imputed]
			"--min_length" => new TextArgumentParameter("--min_length", "", "/.*/"),
				// TODO [default: 75% of the median input sequence length]
			"--min_percent_id" => new TextArgumentParameter("--min_percent_id", "0.75", "/.*/"),
			$blastDb->getName() => $blastDb,
				// TODO [default: created on-the-fly from template_alignment
			"--muscle_max_memory" => new TextArgumentParameter("--muscle_max_memory", "", "/.*/"),
			"--output_dir" => new NewFileParameter("--output_dir", "_aligned"), // TODO dynamic default
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
		return "<p>{$this->getScriptTitle()}</p><p>The initial step in performing phylogeny analysis is aligning the sequences.</p>";
	}
}
