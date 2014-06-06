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

	public function initializeParameters() {
		$inputFastaFile = new OldFileParameter("--input_fasta_file", $this->project);
		$inputFastaFile->requireIf();

		$removeOutliers = new TrueFalseParameter("--remove_outliers");
		$threshold = new TextArgumentParameter("--threshold", "3.0", TextArgumentParameter::PATTERN_NUMBER);
			//TODO only used with remove_outliers

		$threshold->excludeButAllowIf($removeOutliers);

		array_push($this->parameters,
			new Label("Required Parameters"),
			$inputFastaFile,
			new Label("Optional Parameters"),
			new TrueFalseParameter("--suppress_lane_mask_filter"),
				// TODO supresses lane_mask_fp
			new OldFileParameter("--lane_mask_fp", $this->project, '/macqiime/greengenes/lanemask_in_1s_and_0s'),
			new TextArgumentParameter("--entropy_threshold", "", TextArgumentParameter::PATTERN_PROPORTION),
				// TODO If this value is used, any lane mask supplied will be ignored.
			new TextArgumentParameter("--allowed_gap_frac", "0.999999", TextArgumentParameter::PATTERN_PROPORTION),
			$removeOutliers,
			$threshold,
			new Label('Output Options'),
			new TrueFalseParameter("--verbose"),
			new NewFileParameter("--output_dir", ".")
		);
	}
	public function getScriptName() {
		return "filter_alignment.py";
	}
	public function getScriptTitle() {
		return "Filter sequence alignment";
	}
	public function getHtmlId() {
		return "filter_alignment";
	}
	public function renderHelp() {
		ob_start();
		echo "<p>{$this->getScriptTitle()}</p><p>Typically, aligned sequences have a lot of identical bases on both the 3' and 5' end.  It will drasticaly reduce processing time if you filter out all those identical bases.</p>";
		include 'views/filter_alignment.html';
		return ob_get_clean();
	}
}
