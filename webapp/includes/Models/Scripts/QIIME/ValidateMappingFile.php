<?php

namespace Models\Scripts\QIIME;

class ValidateMappingFile extends DefaultScript {

	public function getInitialParameters() {
		$required = true;
		return array(
			"--version" => new VersionParameter("--version", ""),
			"--help" => new HelpParameter("--help", ""),
			"--output_dir" => new NewFileParameter("--output_dir", "mapping_output"),
			"--verbose" => new TrueFalseInvertedParameter("--verbose", "True"),
			"--char_replace" => new TextArgumentParameter("--char_replace", "_"),
			"--not_barcoded" => new TrueFalseParameter("--not_barcoded", "False"),
			"--variable_len_barcodes" => new TrueFalseParameter("--variable_len_barcodes", "False"),
			"--disable_primer_check" => new TrueFalseParameter("--disable_primer_check", "False"),
			"-j" => new TextArgumentParameter("-j", ""),
			"--suppress_html" => new TrueFalseParameter("--suppress_html", "False"),
			"--mapping_fp" => new OldFileParameter("--mapping_fp", "", $required),
		);
	}
	public function getScriptName() {
		return "validate_mapping_file.py";
	}
	public function getScriptTitle() {
		return "Map validation parameters";
	}
	public function getScriptShortTitle() {
		return "validate";
	}
	public function renderHelp() {
		return "<p>{$this->getScriptTitle()}</p><p>The purpose of this script is to take your map file, and tell you if you did it right.</p>";
	}

}
