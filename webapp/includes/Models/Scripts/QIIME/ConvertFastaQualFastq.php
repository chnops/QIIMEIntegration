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

class ConvertFastaQualFastq extends DefaultScript {
	public function getScriptName() {
		return "convert_fastaqual_fastq.py";
	}
	public function getScriptTitle() {
		return "Convert between fasta/qual and fastq";
	}
	public function getHtmlId() {
		return "convert_fasta_qual_fastq";
	}

	public function initializeParameters() {
		parent::initializeParameters();

		$fastaFilePath = new OldFileParameter("--fasta_file_path", $this->project);
		$fastaFilePath->requireIf();

		$conversionType = new ChoiceParameter("--conversion_type", "fastaqual_to_fastq", 
			array("fastaqual_to_fastq", "fastq_to_fastaqual"));
		$qualFilePath = new OldFileParameter("--qual_file_path", $this->project);
		$fullFastq = new TrueFalseParameter("--full_fastq");

		$qualFilePath->excludeButAllowIf($conversionType, "fastaqual_to_fastq");
		$fullFastq->excludeButAllowIf($conversionType, "fastaqual_to_fastq");

		array_push($this->parameters,
			new Label("Required Parameters"),
			$fastaFilePath,
			new Label("Optional parameters"),
			$conversionType,
			$qualFilePath,
			new TextArgumentParameter("--ascii_increment", "33", TextArgumentParameter::PATTERN_DIGIT),
			new Label("Output options"),
			new TrueFalseParameter("--full_fasta_headers"),
			$fullFastq,
			new TrueFalseParameter("--multiple_output_files"),
			new NewFileParameter("--output_dir", ".", $isDir = true),
			new TrueFalseParameter("--verbose")
		);
	}
}
