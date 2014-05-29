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

class SplitLibraries extends DefaultScript {

	public function initializeParameters() {
		$map = new OldFileParameter("--map", $this->project);
		$map->requireIf();
		$fasta = new OldFileParameter("--fasta", $this->project);
		$fasta->requireIf();

		$verboseParameter = new TrueFalseInvertedParameter("--verbose");

		$barcodeType = new ChoiceParameter("--barcode-type", "golay_12", array("hamming_8", "golay_12", "variable_length"));
		$b = new TextArgumentParameter("-b", "", "/\\d+\\/");
		$eitherBarcode = $barcodeType->linkTo($b);

		$maxAmbig = new TextArgumentParameter("--max-ambig", "6", "/\\d+/");
		$truncateAmbiBases = new TrueFalseParameter("--truncate_ambi_bases");
		$eitherAmbig = $maxAmbig->linkTo($truncateAmbiBases);

		$reversePrimers = new ChoiceParameter("--reverse_primers", "disable", array("disable", "truncate_only", "truncate_remove"));
		$reversePrimerMismatches = new TextArgumentParameter("--reverse_primer_mismatches", "0", "/\\d+/");
		$this->parameterRelationships->allowParamIf($reversePrimerMismatches, $reversePrimers, true);

		$qual = new OldFileParameter("--qual", $this->project);
		$minQualScore = new TextArgumentParameter("--min-qual-score", "25", "/\\d+/");
		$recordQualScores = new TrueFalseParameter("--record_qual_scores");
		$this->parameterRelationships->allowParamIf($minQualScore, $qual, true);
		$this->parameterRelationships->allowParamIf($recordQualScores, $qual, true);
		$qualScoreWindow = new TextArgumentParameter("--qual_score_window", "0", "/\\d+/");
		$discardBadWindows = new TrueFalseParameter("--discard_bad_windows");
		$this->parameterRelationships->allowParamIf($discardBadWindows, $qualScoreWindow, true);
//		$this->parameterRelationships->allowParamIf($qualScoreWindow, $qual, true);

		$this->parameterRelationships->makeOptional(array(
			"1" => new Label("<p><strong>Required Parameters</strong></p>"),
			$map->getName() => $map, 
			$fasta->getName() => $fasta, 
			"2" => new Label("<p><strong>Optional Parameters</strong></p>"),
			$verboseParameter->getName() => $verboseParameter,
			"--qual" => new OldFileParameter("--qual", $this->project),
			"--remove_unassigned" => new TrueFalseParameter("--remove_unassigned"),
			"--min-seq-length" => new TextArgumentParameter("--min-seq-length", "200", "/\\d+/"),
  			"--max-seq-length" => new TextArgumentParameter("--max-seq-length", "1000", "/\\d+/"),
  			"--trim-seq-length" => new TrueFalseParameter("--trim-seq-length"),
  			"--min-qual-score" => new TextArgumentParameter("--min-qual-score", "25", "/\\d+/"),
			$eitherBarcode->getName() => $eitherBarcode,
			$eitherAmbig->getName() => $eitherAmbig,
  			"--keep-primer" =>  new TrueFalseParameter("--keep-primer"),
			"--keep-barcode" => new TrueFalseParameter("--keep-barcode"),
  			"--max-homopolymer" => new TextArgumentParameter("--max-homopolymer", "6", "/\\d+/"),
			"--max-primer-mismatch" => new TextArgumentParameter("--max-primer-mismatch", "0", "/\\d+/"),
			"--max-barcode-errors" => new TextArgumentParameter("--max-barcode-errors", "1.5", "/.*/"),
			"--disable_bc_correction" => new TrueFalseParameter("--disable_bc_correction"), // Can improve performance
			"--disable_primers" => new TrueFalseParameter("--disable_primers"),
			"--added_demultiplex_field" => new TextArgumentParameter("--added_demultiplex_field", "", "/[^=]*/"), // TODO or run_header
			"--min-seq-length" => new TextArgumentParameter("--min-seq-length", "200", "/\\d+/"),
	  		"--max-seq-length" => new TextArgumentParameter("--max-seq-length", "1000", "/\\d+/"),
	  		"--trim-seq-length" => new TrueFalseParameter("--trim-seq-length"),
			"--median_length_filtering" => new TextArgumentParameter("--median_length_filtering", "", "/\\d+/"),


			"--dir-prefix" => new NewFileParameter("--dir-prefix", "."),
			"--start-numbering-at" => new TextArgumentParameter("--start-numbering-at", "1", "/\\d+/"),
			"--retain_unassigned_reads" => new TrueFalseParameter("--retain_unassigned_reads"),
		));
	}
	public function getScriptName() {
		return "split_libraries.py";
	}
	public function getScriptTitle() {
		return "De-multiplex libraries";
	}
	public function getHtmlId() {
		return "split_libraries";
	}
	public function renderHelp() {
		ob_start();
		echo "<p>{$this->getScriptTitle()}</p><p>The purpose of this script is to use the barcodes you provided in your map file to separate sequences 
			from a single run into their respective libraries.</p>";
		include "views/{$this->getHtmlId()}.html";
		return ob_get_clean();
	}

}
