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
	public function getScriptName() {
		return "extract_barcodes.py";
	}
	public function getScriptTitle() {
		return "Extract barcodes";
	}
	public function getHtmlId() {
		return "extract_barcodes";
	}

	public function initializeParameters() {
		parent::initializeParameters();

		$fastq1 = new OldFileParameter("--fastq1", $this->project);
		$fastq1->requireIf();

		$inputType = new ChoiceParameter("--input_type", "barcode_single_end",
			array("barcode_single_end", "barcode_paired_end", "barcode_paired_stitched", "barcode_in_label"));
		$fastq2 = new OldFileParameter("--fastq2", $this->project);
		$bc2Len = new TextArgumentParameter("--bc2_len", "6", TextArgumentParameter::PATTERN_DIGIT);
		$charDelineator = new TextArgumentParameter("--char_delineator", ":", TextArgumentParameter::PATTERN_NO_WHITE_SPACE);
		$switchBcOrder = new TrueFalseParameter("--switch_bc_order");
		$mappingFp = new OldFileParameter("--mapping_fp", $this->project);
		$attemptReadReorientation = new TrueFalseParameter("--attempt_read_reorientation");
		$disableHeaderMatch = new TrueFalseParameter("--disable_header_match");
		$revCompBc2 = new TrueFalseParameter("--rev_comp_bc2");

		$fastq2->excludeButAllowIf($inputType, "barcode_paired_end");
		$fastq2->excludeButAllowIf($inputType, "barcode_in_label");
		$bc2Len->excludeButAllowIf($fastq2);
		$bc2Len->excludeButAllowIf($inputType, "barcode_paired_stitched");
		$revCompBc2->excludeButAllowIf($fastq2);
		$revCompBc2->excludeButAllowIf($inputType, "barcode_paired_stitched");
		$disableHeaderMatch->excludeButAllowIf($fastq2);

		$charDelineator->excludeButAllowIf($inputType, "barcode_in_label");
		$switchBcOrder->excludeButAllowIf($inputType, "barcode_paired_stitched");
		$attemptReadReorientation->excludeButAllowIf($mappingFp);

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
			new NewFileParameter("--output_dir", ".", $isDir = true),
			new TrueFalseParameter("--rev_comp_bc1"),
			$revCompBc2
		);
	}
}
