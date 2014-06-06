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

	public function initializeParameters() {
		$forwardReadsFp = new OldFileParameter("--forward_reads_fp", $this->project);
		$forwardReadsFp->requireIf();
		$reverseReadsFp = new OldFileParameter("--reverse_reads_fp", $this->project);
		$reverseReadsFp->requireIf();
		$outputDir = new NewFileParameter("--output_dir", "");
		$outputDir->requireIf();

		$indexReadsFp = new OldFileParameter("--index_reads_fp", $this->project);
		$minOverlap = new TextArgumentParameter("--min_overlap", "", TextArgumentParameter::PATTERN_DIGIT);

		$peJoinMethod = new ChoiceParameter("--pe_join_method", "fastq-join",
			array("fastq-join", "SeqPrep"));
		$percMaxDiff = new TextArgumentParameter("--perc_max_diff", "", TextArgumentParameter::PATTERN_DIGIT);// TODO why on earth this is an integer, we may never know
		$percMaxDiff->excludeButAllowIf($peJoinMethod, "fastq-join");
		$maxAsciiScore = new TextArgumentParameter("--max_ascii_score", "J", "/.*/"); // TODO regex
		$maxAsciiScore->excludeButAllowIf($peJoinMethod, "SeqPrep");
		$minFracMatch = new TextArgumentParameter("--min_frac_match", "", TextArgumentParameter::PATTERN_PROPORTION);
		$minFracMatch->excludeButAllowIf($peJoinMethod, "SeqPrep");
		$maxGoodMismatch = new TextArgumentParameter("--max_good_mismatch", "", TextArgumentParameter::PATTERN_PROPORTION);
		$maxGoodMismatch->excludeButAllowIf($peJoinMethod, "SeqPrep");
		$phred64 = new TrueFalseParameter("--phred_64");
		$phred64->excludeButAllowIf($peJoinMethod, "SeqPrep");

		$verbose = new TrueFalseParameter("--verbose");

		array_push($this->parameters,
			new Label("Required Parameters"),
			$forwardReadsFp,
			$reverseReadsFp,
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
			$verbose,
			$outputDir
		);
			
	}
	public function getScriptName() {
		return "join_paired_ends.py";
	}
	public function getScriptTitle() {
		return "Join Paired Ends";
	}
	public function getHtmlId() {
		return "join_paired_ends";
	}
	public function renderHelp() {
		ob_start();
		echo "<p>{$this->getScriptName()}</p><p>The purpose of this script is to join two sequence files, which contain corresponding paired end reads.</p>";
		include 'views/' . $this->getHtmlId() . '.html';
		return ob_get_clean();
	}

}
