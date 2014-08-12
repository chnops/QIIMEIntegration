<?php
/*
 * Copyright (C) 2014 Aaron Sharp
 * Released under GNU GENERAL PUBLIC LICENSE Version 3, 29 June 2007
 */

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

class SplitLibraries extends DefaultScript {
	public function getScriptName() {
		return "split_libraries.py";
	}
	public function getScriptTitle() {
		return "De-multiplex libraries";
	}
	public function getHtmlId() {
		return "split_libraries";
	}

	public function getInitialParameters() {
		$parameters = parent::getInitialParameters();

		$map = new OldFileParameter("--map", $this->project);
		$fasta = new OldFileParameter("--fasta", $this->project);

		$map->requireIf();
		$fasta->requireIf();

		$barcodeType = new ChoiceParameter("--barcode-type", "golay_12", array("hamming_8", "golay_12", "variable_length"));
		$b = new TextArgumentParameter("-b", "", TextArgumentParameter::PATTERN_DIGIT);
		$maxAmbig = new TextArgumentParameter("--max-ambig", "6", TextArgumentParameter::PATTERN_DIGIT);
		$truncateAmbiBases = new TrueFalseParameter("--truncate_ambi_bases");
		$reversePrimers = new ChoiceParameter("--reverse_primers", "disable", array("disable", "truncate_only", "truncate_remove"));
		$reversePrimerMismatches = new TextArgumentParameter("--reverse_primer_mismatches", "0", TextArgumentParameter::PATTERN_DIGIT);

		$eitherBarcode = $barcodeType->linkTo($b);
		$eitherBarcode->requireIf();
		$eitherAmbig = $maxAmbig->linkTo($truncateAmbiBases);
		$reversePrimerMismatches->excludeButAllowIf($reversePrimers, "truncate_only");
		$reversePrimerMismatches->excludeButAllowIf($reversePrimers, "truncate_remove");

		$qual = new OldFileParameter("--qual", $this->project);
		$minQualScore = new TextArgumentParameter("--min-qual-score", "25", TextArgumentParameter::PATTERN_DIGIT);
		$recordQualScores = new TrueFalseParameter("--record_qual_scores");
		$qualScoreWindow = new TextArgumentParameter("--qual_score_window", "0", TextArgumentParameter::PATTERN_DIGIT);
		$discardBadWindows = new TrueFalseParameter("--discard_bad_windows");

		$minQualScore->excludeButAllowIf($qual);
		$recordQualScores->excludeButAllowIf($qual);
		$qualScoreWindow->excludeButAllowIf($qual);
		$discardBadWindows->excludeIf($qualScoreWindow, 0);

		array_push($parameters,
			new Label("Required Parameters"),
			$map, 
			$fasta, 
			$eitherBarcode,

			new Label("Optional Parameters - Demultiplexing"),
			new TextArgumentParameter("--added_demultiplex_field", "", "/^[^=]+$/"),
			new TrueFalseParameter("--disable_bc_correction"),
			new TextArgumentParameter("--max-barcode-errors", "1.5", TextArgumentParameter::PATTERN_NUMBER),
			new TrueFalseParameter("--disable_primers"),
			new TextArgumentParameter("--max-primer-mismatch", "0", TextArgumentParameter::PATTERN_DIGIT),
			$reversePrimers,
			$reversePrimerMismatches,
			new Label("Optional Parameters - Filtering (non quality)"),
			$eitherAmbig,
  			new TextArgumentParameter("--max-homopolymer", "6", TextArgumentParameter::PATTERN_DIGIT),
			new TextArgumentParameter("--min-seq-length", "200", TextArgumentParameter::PATTERN_DIGIT),
  			new TextArgumentParameter("--max-seq-length", "1000", TextArgumentParameter::PATTERN_DIGIT),
  			new TrueFalseParameter("--trim-seq-length"),
			new TextArgumentParameter("--median_length_filtering", "", TextArgumentParameter::PATTERN_NUMBER),
			new Label("Optional Parameters - Filtering (quality)"),
			$qual,
			$minQualScore,
			$recordQualScores,
			$qualScoreWindow,
			$discardBadWindows,

			new Label("Output Options"),
			new TrueFalseInvertedParameter("--verbose"),
			new NewFileParameter("--dir-prefix", ".", $isDir = true),
			new TextArgumentParameter("--start-numbering-at", "1", TextArgumentParameter::PATTERN_DIGIT),
			new TrueFalseParameter("--retain_unassigned_reads"),
  			new TrueFalseParameter("--keep-primer"),
			new TrueFalseParameter("--keep-barcode")
		);
		return $parameters;
	}
}
