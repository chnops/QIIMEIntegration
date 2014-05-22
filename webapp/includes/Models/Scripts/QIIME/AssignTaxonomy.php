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
		$this->parameterDependencyRelationships[$assignmentMethod->getName()] = array(
			"rtax" => array($read1SeqsFp->getName(),$read2SeqsFp->getName(),$singleOk->getName(),$noSingleOkGeneric->getName(),$readIdRegex->getName(),$ampliconIdRegex->getName(),$headerIdRegex->getName()),
			"rdp" => array($confidence->getName(),$rdpMaxMemory->getName()),
			"mothur" => array($confidence->getName()),
			"uclust" => array($uclustMinConsensusFraction->getName(),$uclustSimilarity->getName(),$uclustMaxAccepts->getName())
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
			$read1SeqsFp->getName() => $read1SeqsFp,
			$read2SeqsFp->getName() => $read2SeqsFp,
			$singleOk->getName() => $singleOk,
			$noSingleOkGeneric->getName() => $noSingleOkGeneric,
			$readIdRegex->getName() => $readIdRegex,
			$ampliconIdRegex->getName() => $ampliconIdRegex,
			$headerIdRegex->getName() => $headerIdRegex,
			$assignmentMethod->getName() => $assignmentMethod,
			"--blast_db" => new OldFileParameter("--blast_db", $this->project),
				// TODO build in files
				// TODO Must provide either --blast_db or --reference_seqs_db for assignment with blast [default: none]
			$confidence->getName() => $confidence,
			$uclustMinConsensusFraction->getName() => $uclustMinConsensusFraction,
			$uclustSimilarity->getName() => $uclustSimilarity,
			$uclustMaxAccepts->getName() => $uclustMaxAccepts,
			$rdpMaxMemory->getName() => $rdpMaxMemory,
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
