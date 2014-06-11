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

class ExtractBarcodes extends DefaultScript {

	public function initializeParameters() {
		parent::initializeParameters();

		$fastq1 = new OldFileParameter("--fastq1", $this->project);
		$fastq1->requireIf();

		$inputType = new ChoiceParameter("--input_type", "barcode_single_end",
			array("barcode_single_end", "barcode_paired_end",
			"barcode_paired_stitched", "barcode_in_label"));

		$fastq2 = new OldFileParameter("--fastq2", $this->project);
		$fastq2->excludeButAllowIf($inputType, "barcode_paired_end");
		$fastq2->excludeButAllowIf($inputType, "barcode_in_label");

		$bc2Len = new TextArgumentParameter("--bc2_len", "6", TextArgumentParameter::PATTERN_DIGIT);
		$bc2Len->excludeButAllowIf($fastq2);

		$charDelineator = new TextArgumentParameter("--char_delineator", ":", '/^\S$/');
		$charDelineator->excludeButAllowIf($inputType, "barcode_in_label");

		$switchBcOrder = new TrueFalseParameter("--switch_bc_order");
		$switchBcOrder->excludeButAllowIf($inputType, "barcode_paired_stitched");

		$mappingFp = new OldFileParameter("--mapping_fp", $this->project);
		$attemptReadReorientation = new TrueFalseParameter("--attempt_read_reorientation");
		$attemptReadReorientation->excludeButAllowIf($mappingFp);

		$disableHeaderMatch = new TrueFalseParameter("--disable_header_match");
		$disableHeaderMatch->excludeButAllowIf($fastq2);
		$revCompBc1 = new TrueFalseParameter("--rev_comp_bc2");
		$revCompBc1->excludeButAllowIf($fastq2);

		array_push($this->parameters,
			new Label("Required Parameters"),
			$fastq1,

			new Label("Optional Parameters"),
			$inputType,
			$fastq2,
			new TextArgumentParameter("--bc1_len", "6", TextArgumentParameter::PATTERN_DIGIT),
			$bc2Len,
			$charDelineator,
			$switchBcOrder,
			$mappingFp,
			$attemptReadReorientation,
			$disableHeaderMatch,

			new Label("Output options"),
			new TrueFalseParameter("--verbose"),
			new NewFileParameter("--output_dir", "."),
			new TrueFalseParameter("--rev_comp_bc1"),
			$revCompBc1
		);
	}
	public function getScriptName() {
		return "extract_barcodes.py";
	}
	public function getScriptTitle() {
		return "Extract barcodes";
	}
	public function getHtmlId() {
		return "extract_barcodes";
	}
}
