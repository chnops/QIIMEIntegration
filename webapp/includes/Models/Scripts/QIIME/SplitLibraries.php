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
			"--version" => new VersionParameter("--version", ""),
			"--help" => new HelpParameter("--help", ""),
  			"--verbose" => new TrueFalseInvertedParameter("--verbose", "True"),
			"--qual" => new TextArgumentParameter("--qual", ""),
			"--remove_unassigned" => new TrueFalseParameter("--remove_unassigned", "False"),
			"--min-seq-length" => new TextArgumentParameter("--min-seq-length", "200"),
  			"--max-seq-length" => new TextArgumentParameter("--max-seq-length", "1000"),
  			"--trim-seq-length" => new TrueFalseParameter("--trim-seq-length", "False"),
  			"--min-qual-score" => new TextArgumentParameter("--min-qual-score", "25"),
  			"--keep-primer" =>  new TrueFalseParameter("--keep-primer", "False"),
			"--keep-barcode" => new TrueFalseParameter("--keep-barcode", "False"),
			"--max-ambig" => new TextArgumentParameter("--max-ambig", "6"),
  			"--max-homopolymer" => new TextArgumentParameter("--max-homopolymer", "6"),
			"--max-primer-mismatch" => new TextArgumentParameter("--max-primer-mismatch", "0"),
			"--barcode-type" => new ChoiceParameter("--barcode-type", "golay_12"),
                        /*a number representing the
                        length of the barcode, such as -b 4. */
  			"--dir-prefix" => new NewFileParameter("--dir-prefix", "."), // possibly a file parameter
			"--max-barcode-errors=" => new TextArgumentParameter("--max-barcode-error", "1.5"),
			"--start-numbering-at" => new TextArgumentParameter("--start-numbering-at", "1"),
			"--retain_unassigned_reads" => new TrueFalseParameter("--retain_unasigned_reads", "False"),
			"--disable_bc_correction" => new TrueFalseParameter("--disable_bc_correction", "False"), // Can improve performance
			"--qual_score_window" => new TextArgumentParameter("--qual_score_window", "0"), // depends other args, 0 means no checking
			"--discard_bad_windows" => new TrueFalseParameter("--discard_bad_windows", "False"), 
			"--disable_primers" => new TrueFalseParameter("--disable_primers", "False"),
			"--reverse_primers" => new ChoiceParameter("--reverse_primers", "disable"),
  			"--reverse_primer_mismatches" => new TextArgumentParameter("--reverse_primer_mismatches", "0"),
			"--record_qual_scores" => new TrueFalseParameter("--recored_qual_scores", "False"),
			"--median_length_filtering" => new TextArgumentParameter("--mediate_length_filtering", "none"),
			"--added_demultiplex_field" => new TextArgumentParameter("--added_demultiplex_field", "none"),
			"--truncate_ambi_bases" => new TrueFalseParameter("--truncate_ambi_bases", "False"),
			"--map" => new OldFileParameter("--map", "", $required),
			"--fasta" => new OldFileParameter("--fasta", "", $required),
		);
	}
	public function getScriptName() {
		return "split_libraries.py";
	}
	public function getScriptTitle() {
		return "De-multiplex libraries";
	}
	public function getScriptShortTitle() {
		return "split";
	}
	public function renderHelp() {
		return "<p>{$this->getScriptTitle()}</p>
			<p>The purpose of this script is to use the barcodes you provided in your map file to separate sequences from a single run into their respective libraries.</p>";
	}

}
