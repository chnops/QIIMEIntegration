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

class FilterAlignment extends DefaultScript {
	public function getScriptName() {
		return "filter_alignment.py";
	}
	public function getScriptTitle() {
		return "Filter sequence alignment";
	}
	public function getHtmlId() {
		return "filter_alignment";
	}

	public function initializeParameters() {
		parent::initializeParameters();

		$inputFastaFile = new OldFileParameter("--input_fasta_file", $this->project);
		$inputFastaFile->requireIf();

		$removeOutliers = new TrueFalseParameter("--remove_outliers");
		$threshold = new TextArgumentParameter("--threshold", "3.0", TextArgumentParameter::PATTERN_NUMBER);
		$laneMaskFp = new OldFileParameter("--lane_mask_fp", $this->project, '/macqiime/greengenes/lanemask_in_1s_and_0s');
		$entropyThreshold = new TextArgumentParameter("--entropy_threshold", "", TextArgumentParameter::PATTERN_PROPORTION);
		$suppressLaneMaskFilter = new TrueFalseParameter("--suppress_lane_mask_filter");

		$threshold->excludeButAllowIf($removeOutliers);
		$laneMaskFp->excludeIf($entropyThreshold);
		$suppressLaneMaskFilter->excludeIf($entropyThreshold);
		$laneMaskFp->excludeIf($suppressLaneMaskFilter);

		array_push($this->parameters,
			new Label("Required Parameters"),
			$inputFastaFile,

			new Label("Optional Parameters"),
			$entropyThreshold,
			$suppressLaneMaskFilter,
			$laneMaskFp,
			new TextArgumentParameter("--allowed_gap_frac", "0.999999", TextArgumentParameter::PATTERN_PROPORTION),
			$removeOutliers,
			$threshold,

			new Label('Output Options'),
			new TrueFalseParameter("--verbose"),
			new NewFileParameter("--output_dir", ".", $isDir = true)
		);
	}
}
