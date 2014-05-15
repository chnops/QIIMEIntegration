<?php

namespace Models\Scripts;

class ValidateMappingFile extends DefaultScript {

	public function getInitialParameters() {
		$required = true;
		return array(
			new VersionParameter("--version", ""),
			new HelpParameter("--help", ""),
			new DefaultParameter("-o", "mapping_output"),
			new TrueFalseInvertedParameter("-verbose", "True"),
			new TextArgumentParameter("--char_replace", "_"),
			new TrueFalseParameter("--not_barcoded", "False"),
			new TrueFalseParameter("--variable_len_barcodes", "False"),
			new TrueFalseParameter("--disable_primer_check", "False"),
			new TextArgumentParameter("-j", ""),
			new TrueFalseParameter("--suppress_html", "False"),
			new DefaultParameter("--mapping_fp", "", $required),
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
