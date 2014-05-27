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

class AssignTaxonomy extends DefaultScript {

	public function initializeParameters() {
		$assignmentMethod = new ChoiceParameter("--assignment_method", "uclust", 
			array("rdp", "blast", "rtax", "mothur", "tax2tree", "uclust"));
		$read1SeqsFp = new OldFileParameter("--read_1_seqs_fp", $this->project);
		$read2SeqsFp = new OldFileParameter("--read_2_seqs_fp", $this->project);
		$singleOk = new TrueFalseParameter("--single_ok");
		$noSingleOkGeneric = new TrueFalseParameter("--no_single_ok_generic");
		$readIdRegex = new TextArgumentParameter("--read_id_regex", "\\S+\\s+(\\S+)", "/.*/"); // TODO really complex regex
		$ampliconIdRegex = new TextArgumentParameter("--amplicon_id_regex", "(\\S+)\\s+(\\S+?)\\/", "/.*/"); // TODO really complex regex
		$headerIdRegex = new TextArgumentParameter("--header_id_regex", "\S+\s+(\S+?)\/", "/.*/"); // TODO really complex regex
		$confidence = new TextArgumentParameter("--confidence", "0.8", "/.*/"); // TODO decimal between 0 and 1
		$rdpMaxMemory = new TextArgumentParameter("--rdp_max_memory", "4000", "/\\d+/"); // TODO units = MB
		$uclustMinConsensusFraction = new TextArgumentParameter("--uclust_min_consensus_fraction", "0.51", "/.*/"); // TODO decimal between 0 and 1
		$uclustSimilarity = new TextArgumentParameter("--uclust_similarity", "0.9", "/.*/");  // TODO decimal between 0 and 1
		$uclustMaxAccepts = new TextArgumentParameter("--uclust_max_accepts", "3", "/\\d+/"); 

		$this->parameterRelationships->allowParamIf($read1SeqsFp, $assignmentMethod, "rtax");
		$this->parameterRelationships->allowParamIf($read2SeqsFp, $assignmentMethod, "rtax");
		$this->parameterRelationships->allowParamIf($singleOk, $assignmentMethod, "rtax");
		$this->parameterRelationships->allowParamIf($noSingleOkGeneric, $assignmentMethod, "rtax");
		$this->parameterRelationships->allowParamIf($readIdRegex, $assignmentMethod, "rtax");
		$this->parameterRelationships->allowParamIf($ampliconIdRegex, $assignmentMethod, "rtax");
		$this->parameterRelationships->allowParamIf($headerIdRegex, $assignmentMethod, "rtax");
		$this->parameterRelationships->allowParamIf($confidence, $assignmentMethod, "rdp");
		$this->parameterRelationships->allowParamIf($rdpMaxMemory, $assignmentMethod, "rdp");
		$this->parameterRelationships->allowParamIf($confidence, $assignmentMethod, "mothur");
		$this->parameterRelationships->allowParamIf($uclustMinConsensusFraction, $assignmentMethod, "uclust");
		$this->parameterRelationships->allowParamIf($uclustSimilarity, $assignmentMethod, "uclust");
		$this->parameterRelationships->allowParamIf($uclustMaxAccepts, $assignmentMethod, "uclust");

		$idToTaxonomyFp = new OldFileParameter("--id_to_taxonomy_fp", $this->project);
			// TODO built in files: default: /macqiime/greengenes/gg_13_8_otus/taxonomy/97_otu_taxonomy.txt; 
		$treeFp = new OldFileParameter("--tree_fp", $this->project);

		$referenceSeqsFp = new OldFileParameter("--reference_seqs_fp", $this->project);
				// TODO built in files default: /macqiime/greengenes/gg_13_8_otus/rep_set/97_otus.fasta; 
		$blastDb = new OldFileParameter("--blast_db", $this->project);
				// TODO build in files
		$blastDatabase = $this->parameterRelationships->linkParams($referenceSeqsFp, $blastDb);

		$this->parameterRelationships->requireParamIf($idToTaxonomyFp, $assignmentMethod, "blast");
		$this->parameterRelationships->requireParamIf($blastDatabase, $assignmentMethod, "blast");
		$this->parameterRelationships->requireParamIf($treeFp, $assignmentMethod, "tax2tree");

		$inputFastaFp = new OldFileParameter("--input_fasta_fp", $this->project);
		$this->parameterRelationships->requireParam($inputFastaFp);

		$this->parameterRelationships->makeOptional(array(
			"--verbose" => new TrueFalseParameter("--verbose"),
			"--output_dir" => new NewFileParameter("--output_dir", "_assigned_taxonomy"), // TODO dynamic default
			"--e_value" => new TextArgumentParameter("--e_value", "0.001", "/.*/"), // TODO potentially, but not necessarily, scientific notation
			"--training_data_properties_fp" => new OldFileParameter("--training_data_properties_fp", $this->project),
				// TODO This option is overridden by the -t and -r options.
			));
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
