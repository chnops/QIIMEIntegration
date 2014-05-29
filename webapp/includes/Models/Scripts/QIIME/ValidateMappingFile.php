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

	private $scriptName;
	public function __construct(\Models\Project $project) {
		if ($project->scriptExists("validate_mapping_file.py")) {
			$this->scriptName = "validate_mapping_file.py";
		}
		else {
			$this->scriptName = "check_id_map.py";
		}
		parent::__construct($project);
	}

	public function initializeParameters() {
		$mappingFp = new OldFileParameter("--mapping_fp", $this->project);
		$mappingFp->requireIf();

		$verboseParameter = new TrueFalseInvertedParameter("--verbose");

		array_push($this->parameters,
			 new Label("<p><strong>Required Parameters</strong></p>"),
			 $mappingFp,
			 new Label("<p><strong>Optional Parameters</strong></p>"),
			 $verboseParameter,
			 new NewFileParameter("--output_dir", ""),
			 new TextArgumentParameter("--char_replace", "_", "/^.$/"),
			 new TrueFalseParameter("--not_barcoded"),
			 new TrueFalseParameter("--variable_len_barcodes"),
			 new TrueFalseParameter("--disable_primer_check"),
			 new TextArgumentParameter("--added_demultiplex_field", "", "/.*/"),
			 new TrueFalseParameter("--suppress_html")
		);
	}
	public function getScriptName() {
		return $this->scriptName;
	}
	public function getScriptTitle() {
		return "Validate map file";
	}
	public function getHtmlId() {
		return "validate_mapping_file";
	}
	public function renderHelp() {
		ob_start();
		echo "<p>{$this->getScriptTitle()}</p><p>The purpose of this script is to take your map file, and tell you if you did it right.</p>";
		include "views/{$this->getHtmlId()}.html";
		return ob_get_clean();
	}

}
