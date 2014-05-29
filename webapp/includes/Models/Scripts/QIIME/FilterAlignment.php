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

		array_push($this->parameters,
			 new Label("<p><strong>Required Parameters</strong></p>"),
			 $inputFastaFile,
			 new Label("<p><strong>Optional Parameters</strong></p>"),
			 new TrueFalseParameter("--verbose"),
			 new NewFileParameter("--output_dir", "."),
			 new OldFileParameter("--lane_mask_fp", $this->project),
				// TODO [default:/macqiime/greengenes/lanemask_in_1s_and_0s]
			 new TrueFalseParameter("--suppress_lane_mask_filter"),
				// TODO supresses lane_mask_fp
			 new TextArgumentParameter("--allowed_gap_frac", "0.999999", "/.*/"), // TODO 0 < fraction < 1
			 new TrueFalseParameter("--remove_outliers"),
			 new TextArgumentParameter("--threshold", "3.0", "/.*/"), // TODO arbitrary float
				//TODO only used with remove_outliers
			 new TextArgumentParameter("--entropy_threshold", "", "/.*/") // TODO 0 < fraction < 1
				// TODO If this value is used, any lane mask supplied will be ignored.
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
		return "<p>{$this->getScriptTitle()}</p><p>Typically, aligned sequences have a lot of identical bases on both the 3' and 5' end.  It will drasticaly reduce processing time if you filter out identical bases.</p>";
	}

}
