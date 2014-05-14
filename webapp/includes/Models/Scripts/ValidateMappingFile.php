<?php

namespace Models\Scripts;

class ValidateMappingFile extends DefaultScript {

	public function getInitialParameters() {
		$required = true;
		return array(
			new DefaultParameter("--version", ""),
			new DefaultParameter("--help", ""),
			new DefaultParameter("-o", "mapping_output"),
			new DefaultParameter("-verbose", "True"),
			new DefaultParameter("--char_replace", "_"),
			new DefaultParameter("--not_barcoded", "False"),
			new DefaultParameter("--variable_len_barcodes", "False"),
			new DefaultParameter("--disable_primer_check", "False"),
			new DefaultParameter("-j", ""),
			new DefaultParameter("--suppress_html", "False"),
			new DefaultParameter("--mapping_fp", "", $required),
		);
	}
	public function getScriptName() {
		return "validate_mapping_file.py";
	}
	public function getScriptTitle() {
		return "<h4>Map validation parameters</h4>";
	}
	public function renderHelp() {
		return "The purpose of this script is to take your map file, and tell you if you did it right.";
	}

}
