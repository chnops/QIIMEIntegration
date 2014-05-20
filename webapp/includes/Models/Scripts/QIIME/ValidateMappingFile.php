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

class ValidateMappingFile extends DefaultScript {

	public function initializeParameters() {
		$verboseParameter = new TrueFalseInvertedParameter("--verbose");
		$this->trueFalseParameters[$verboseParameter->getName()] = $verboseParameter;
		$this->parameters['special'] = array(
			"--output_dir" => new NewFileParameter("--output_dir", ""),
			$verboseParameter->getName() => $verboseParameter,
			"--char_replace" => new TextArgumentParameter("--char_replace", "_", "/./"),
			"--not_barcoded" => new TrueFalseParameter("--not_barcoded"),
			"--variable_len_barcodes" => new TrueFalseParameter("--variable_len_barcodes"),
			"--disable_primer_check" => new TrueFalseParameter("--disable_primer_check"),
			"-j" => new TextArgumentParameter("-j", "", "/.*/"),
			"--suppress_html" => new TrueFalseParameter("--suppress_html"),
		);
		$this->parameters['required'] = array(
			"--mapping_fp" => new OldFileParameter("--mapping_fp", $this->project)
		);
	}
	public function getScriptName() {
		return "validate_mapping_file.py";
	}
	public function getScriptTitle() {
		return "Validate map file";
	}
	public function getHtmlId() {
		return "validate_mapping_file";
	}
	public function renderHelp() {
		return "<p>{$this->getScriptTitle()}</p><p>The purpose of this script is to take your map file, and tell you if you did it right.</p>";
	}

}
