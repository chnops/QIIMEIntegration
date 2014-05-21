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

class AssignTaxonomy extends DefaultScript {

	public function initializeParameters() {
		$this->parameters['required'] = array(
			"--input_fasta_fp" => new OldFileParameter("--input_fasta_fp", $this->project),
		);
		$this->parameters['special'] = array(
			"--verbose" => new TrueFalseParameter("--verbose"),
			"--id_to_taxonomy_fp" => new OldFileParameter("--id_to_taxonomy_fp", $this->project),
				// TODO built in files: default: /macqiime/greengenes/gg_13_8_otus/taxonomy/97_otu_taxonomy.txt; 
				// TODO REQUIRED when method is blast
			"--reference_seqs_fp" => new OldFileParameter("--reference_seqs_fp", $this->project),
				// TODO built in files default: /macqiime/greengenes/gg_13_8_otus/rep_set/97_otus.fasta; 
				// TODO REQUIRED if -b is not provided when method is blast]
			"--training_data_properties_fp" => new OldFileParameter("--training_data_properties_fp", $this->project),
				// TODO This option is overridden by the -t and -r options.
			"--read_1_seqs_fp" => new OldFileParameter("--read_1_seqs_fp", $this->project),
				// TODO (used for RTAX only).
			"--read_2_seqs_fp" => new OldFileParameter("--read_2_seqs_fp", $this->project),
				// TODO (used for RTAX only).
			"--single_ok" => new TrueFalseParameter("--single_ok"),
				// TODO (used for RTAX only).
			"--no_single_ok_generic" => new TrueFalseParameter("--no_single_ok_generic"),
				// TODO (used for RTAX only).
			"--read_id_regex" => new TextArgumentParameter("--read_id_regex", "\\S+\\s+(\\S+)", "/.*/"), // TODO really complex regex
				// TODO (used for RTAX only). 
			"--amplicon_id_regex" => new TextArgumentParameter("--amplicon_id_regex", "(\\S+)\\s+(\\S+?)\\/", "/.*/"), // TODO really complex regex
				// TODO (used for RTAX only).
			"--header_id_regex" => new TextArgumentParameter("--header_id_regex", "\S+\s+(\S+?)\/", "/.*/"), // TODO really complex regex
				// TODO (used for RTAX only).
			"--assignment_method" => new ChoiceParameter("--assignment_method", "uclust", 
				array("rdp", "blast", "rtax", "mothur", "tax2tree", "uclust")),
			"--blast_db" => new OldFileParameter("--blast_db", $this->project),
				// TODO build in files
				// TODO Must provide either --blast_db or --reference_seqs_db for assignment with blast [default: none]
			"--confidence" => new TextArgumentParameter("--confidence", "0.8", "/.*/"), // TODO decimal between 0 and 1
				// TODO used only for rdp and mothur methods
			"--uclust_min_consensus_fraction" => new TextArgumentParameter("--uclust_min_consensus_fraction", "0.51", "/.*/"), // TODO decimal between 0 and 1
				// TODO only used for uclust method
			"--uclust_similarity" => new TextArgumentParameter("--uclust_similarity", "0.9", "/.*/"),  // TODO decimal between 0 and 1
				// TODO only used for uclust method
			"--uclust_max_accepts" => new TextArgumentParameter("--uclust_max_accepts", "3", "/\\d+/"), 
				// TODO only used for uclust method [default: 3]
			"--rdp_max_memory" => new TextArgumentParameter("--rdp_max_memory", "4000", "/\\d+/"), // TODO units = MB
				// TODO when using the rdp method.
			"--e_value" => new TextArgumentParameter("--e_value", "0.001", "/.*/"), // TODO potentially, but not necessarily, scientific notation
			"--tree_fp" => new OldFileParameter("--tree_fp", $this->project),
				// TODO Required for Tax2Tree assignment.
			"--output_dir" => new NewFileParameter("--output_dir", "_assigned_taxonomy"), // TODO dynamic default
		);
	}
	public function getScriptName() {
		return "assign_taxonomy.py";
	}
	public function getScriptTitle() {
		return "Assign taxonomies";
	}
	public function getHtmlId() {
		return "assign_taxonomy";
	}
	public function renderHelp() {
		return "<p>{$this->getScriptTitle()}</p><p>This script takes OTUs and assigns them to a real-world taxonomy.  To do this, it uses sequence databases.</p>";
	}

}
