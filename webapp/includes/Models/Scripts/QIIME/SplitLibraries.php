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

class SplitLibraries extends DefaultScript {

	public function getInitialParameters() {
		$required = true;
		return array(
			"--version" => new VersionParameter(),
			"--help" => new HelpParameter(),
  			"--verbose" => new TrueFalseInvertedParameter("--verbose"),
			"--qual" => new OldFileParameter("--qual", $this->project),
			"--remove_unassigned" => new TrueFalseParameter("--remove_unassigned"),
			"--min-seq-length" => new TextArgumentParameter("--min-seq-length", "200", "/\\d+/"),
  			"--max-seq-length" => new TextArgumentParameter("--max-seq-length", "1000", "/\\d+/"),
  			"--trim-seq-length" => new TrueFalseParameter("--trim-seq-length"),
  			"--min-qual-score" => new TextArgumentParameter("--min-qual-score", "25", "/\\d+/"),
  			"--keep-primer" =>  new TrueFalseParameter("--keep-primer"),
			"--keep-barcode" => new TrueFalseParameter("--keep-barcode"),
			"--max-ambig" => new TextArgumentParameter("--max-ambig", "6", "/\\d+/"),
  			"--max-homopolymer" => new TextArgumentParameter("--max-homopolymer", "6", "/\\d+/"),
			"--max-primer-mismatch" => new TextArgumentParameter("--max-primer-mismatch", "0", "/\\d+/"),
			"--barcode-type" => new ChoiceParameter("--barcode-type", "golay_12", array("hamming_8", "golay_12", "variable_length")), // TODO create annonymous class that overloads isInputValid
                        /*a number representing the
                        length of the barcode, such as -b 4. */
  			"--dir-prefix" => new NewFileParameter("--dir-prefix", "."), // TODO possibly a file parameter
			"--max-barcode-errors=" => new TextArgumentParameter("--max-barcode-error", "1.5", "/.*/"),
			"--start-numbering-at" => new TextArgumentParameter("--start-numbering-at", "1", "/\\d+/"),
			"--retain_unassigned_reads" => new TrueFalseParameter("--retain_unasigned_reads"),
			"--disable_bc_correction" => new TrueFalseParameter("--disable_bc_correction"), // Can improve performance
			"--qual_score_window" => new TextArgumentParameter("--qual_score_window", "0", "/\\d+/"), // depends other args, 0 means no checking
			"--discard_bad_windows" => new TrueFalseParameter("--discard_bad_windows"), 
			"--disable_primers" => new TrueFalseParameter("--disable_primers"),
			"--reverse_primers" => new ChoiceParameter("--reverse_primers", "disable", array("disable", "truncate_only", "truncate_remove")),
  			"--reverse_primer_mismatches" => new TextArgumentParameter("--reverse_primer_mismatches", "0", "/\\d+/"),
			"--record_qual_scores" => new TrueFalseParameter("--recored_qual_scores"),
			"--median_length_filtering" => new TextArgumentParameter("--median_length_filtering", "", "/\\d+/"),
			"--added_demultiplex_field" => new TextArgumentParameter("--added_demultiplex_field", "", "/[^=]*/"), // TODO or run_header
			"--truncate_ambi_bases" => new TrueFalseParameter("--truncate_ambi_bases"),
			"--map" => new OldFileParameter("--map", $this->project),
			"--fasta" => new OldFileParameter("--fasta", $this->project),
		);
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
		return "<p>{$this->getScriptTitle()}</p>" .
			"<p>The purpose of this script is to use the barcodes you provided in your map file to separate sequences from a single run into their respective libraries.</p>";
	}

}
