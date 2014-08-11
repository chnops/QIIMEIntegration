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

class SplitLibrariesFastq extends DefaultScript {
	public function getScriptName() {
		return "split_libraries_fastq.py";
	}
	public function getScriptTitle() {
		return "De-multiplex fastq";
	}
	public function getHtmlId() {
		return "split_libraries_fastq";
	}

	public function getInitialParameters() {
		$parameters = parent::getInitialParameters();

		$sequenceReadFps = new OldFileParameter("--sequence_read_fps", $this->project); 
		$mappingFps = new OldFileParameter("--mapping_fps", $this->project);
		$outputDir = new NewFileParameter("--output_dir", "", $isDir = true);

		$sequenceReadFps->requireIf();
		$mappingFps->requireIf();
		$outputDir->requireIf();

		array_push($parameters,
			new Label("Required Parameters"),
			$sequenceReadFps,
			$mappingFps,
			$outputDir,

			new Label("Optional Parameters - De-multiplexing"),
			new OldFileParameter("--barcode_read_fps", $this->project),
			new TextArgumentParameter("--samples_ids", "", TextArgumentParameter::PATTERN_NO_WHITE_SPACE),
			new TrueFalseParameter("--rev_comp_barcode"),
			new TrueFalseParameter("--rev_comp_mapping_barcodes"),
			new TextArgumentParameter("--barcode_type", "golay_12", TextArgumentParameter::PATTERN_ANYTHING_GOES),

			new Label("Optional Parameters - Filtering (not quality)"),
			new TextArgumentParameter("--sequence_max_n", "0", TextArgumentParameter::PATTERN_DIGIT),
			new TextArgumentParameter("--max_barcode_errors", "1.5", TextArgumentParameter::PATTERN_NUMBER),

			new Label("Optional Parameters - Filtering (quality)"),
			new TextArgumentParameter("--max_bad_run_length", "3", TextArgumentParameter::PATTERN_DIGIT),
			new TextArgumentParameter("--min_per_read_length_fraction", "0.75", TextArgumentParameter::PATTERN_PROPORTION),
			new TextArgumentParameter("--phred_quality_threshold", "3", TextArgumentParameter::PATTERN_DIGIT),
			new TextArgumentParameter("--phred_offset", "", TextArgumentParameter::PATTERN_NUMBER),

			new Label("Output options"),
			new TrueFalseParameter("--store_demultiplexed_fastq"),
			new TrueFalseParameter("--verbose"),
			new TrueFalseParameter("--store_qual_scores"),
			new TrueFalseParameter("--retain_unassigned_reads"),
			new TextArgumentParameter("--start_seq_id", "0", TextArgumentParameter::PATTERN_DIGIT),
			new TrueFalseParameter("--rev_comp")
		);
		return $parameters;
	}
}
