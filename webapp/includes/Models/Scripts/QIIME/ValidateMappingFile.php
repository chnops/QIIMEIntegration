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

class ValidateMappingFile extends DefaultScript {
	public function getScriptName() {
		return "validate_mapping_file.py";
	}
	public function getScriptTitle() {
		return "Validate map file";
	}
	public function getHtmlId() {
		return "validate_mapping_file";
	}

	public function initializeParameters() {
		parent::initializeParameters();
		$mappingFp = new OldFileParameter("--mapping_fp", $this->project);
		$mappingFp->requireIf();

		array_push($this->parameters,
			 new Label("Required Parameters"),
			 $mappingFp,

			 new Label("Optional Parameters"),
			 new TrueFalseParameter("--not_barcoded"),
			 new TrueFalseParameter("--variable_len_barcodes"),
			 new TrueFalseParameter("--disable_primer_check"),
			 new TextArgumentParameter("--added_demultiplex_field", "", "/^[^=]+$/"),// TODO same as SplitLibraries.php, --added_demultiplex_field

			 new Label("Output Options"),
			 new TrueFalseInvertedParameter("--verbose"),
			 new NewFileParameter("--output_dir", "", $isDir = true),
			 new TrueFalseParameter("--suppress_html"),
			 new TextArgumentParameter("--char_replace", "_", "/^.$/")
		);
	}
}
