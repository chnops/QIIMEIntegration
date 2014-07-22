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

class JoinPairedEnds extends DefaultScript {
	public function getScriptName() {
		return "join_paired_ends.py";
	}
	public function getScriptTitle() {
		return "Join paired ends";
	}
	public function getHtmlId() {
		return "join_paired_ends";
	}

	public function initializeParameters() {
		parent::initializeParameters();

		$forwardReadsFp = new OldFileParameter("--forward_reads_fp", $this->project);
		$reverseReadsFp = new OldFileParameter("--reverse_reads_fp", $this->project);
		$outputDir = new NewFileParameter("--output_dir", "", $isDir = true);

		$forwardReadsFp->requireIf();
		$reverseReadsFp->requireIf();
		$outputDir->requireIf();

		$indexReadsFp = new OldFileParameter("--index_reads_fp", $this->project);
		$minOverlap = new TextArgumentParameter("--min_overlap", "", TextArgumentParameter::PATTERN_DIGIT);
		$peJoinMethod = new ChoiceParameter("--pe_join_method", "fastq-join",
			array("fastq-join", "SeqPrep"));
		$percMaxDiff = new TextArgumentParameter("--perc_max_diff", "", TextArgumentParameter::PATTERN_DIGIT);
		$maxAsciiScore = new TextArgumentParameter("--max_ascii_score", "J", "/^.$/");
		$minFracMatch = new TextArgumentParameter("--min_frac_match", "", TextArgumentParameter::PATTERN_PROPORTION);
		$maxGoodMismatch = new TextArgumentParameter("--max_good_mismatch", "", TextArgumentParameter::PATTERN_PROPORTION);
		$phred64 = new TrueFalseParameter("--phred_64");

		$percMaxDiff->excludeButAllowIf($peJoinMethod, "fastq-join");
		$maxAsciiScore->excludeButAllowIf($peJoinMethod, "SeqPrep");
		$minFracMatch->excludeButAllowIf($peJoinMethod, "SeqPrep");
		$maxGoodMismatch->excludeButAllowIf($peJoinMethod, "SeqPrep");
		$phred64->excludeButAllowIf($peJoinMethod, "SeqPrep");

		array_push($this->parameters,
			new Label("Required Parameters"),
			$forwardReadsFp,
			$reverseReadsFp,
			$outputDir,
			new Label("Optional Parameters"),
			$indexReadsFp,
			$minOverlap,
			$peJoinMethod,
			$percMaxDiff,
			$maxAsciiScore,
			$minFracMatch,
			$maxGoodMismatch,
			$phred64,
			new Label("Output Options"),
			new TrueFalseParameter("--verbose")
		);
			
	}
}
