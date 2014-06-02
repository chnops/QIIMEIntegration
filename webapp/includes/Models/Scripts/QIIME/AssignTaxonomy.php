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

class AssignTaxonomy extends DefaultScript {

	public function initializeParameters() {
		$assignmentMethod = new ChoiceParameter("--assignment_method", "rdp", 
			array("rdp", "blast", "rtax", "mothur", "tax2tree"));
		$read1SeqsFp = new OldFileParameter("--read_1_seqs_fp", $this->project);
		$read2SeqsFp = new OldFileParameter("--read_2_seqs_fp", $this->project);
		$singleOk = new TrueFalseParameter("--single_ok");
		$noSingleOkGeneric = new TrueFalseParameter("--no_single_ok_generic");
		$readIdRegex = new TextArgumentParameter("--read_id_regex", "\\S+\\s+(\\S+)", "/.*/"); // TODO really complex regex
		$ampliconIdRegex = new TextArgumentParameter("--amplicon_id_regex", "(\\S+)\\s+(\\S+?)\\/", "/.*/"); // TODO really complex regex
		$headerIdRegex = new TextArgumentParameter("--header_id_regex", "\S+\s+(\S+?)\/", "/.*/"); // TODO really complex regex
		$confidence = new TextArgumentParameter("--confidence", "0.8", "/.*/"); // TODO decimal between 0 and 1
		$rdpMaxMemory = new TextArgumentParameter("--rdp_max_memory", "4000", "/\\d+/"); // TODO units = MB
//		TODO not supported in all macqiime version
//		$uclustMinConsensusFraction = new TextArgumentParameter("--uclust_min_consensus_fraction", "0.51", "/.*/"); // TODO decimal between 0 and 1
//		$uclustSimilarity = new TextArgumentParameter("--uclust_similarity", "0.9", "/.*/");  // TODO decimal between 0 and 1
//		$uclustMaxAccepts = new TextArgumentParameter("--uclust_max_accepts", "3", "/\\d+/"); 

		$read1SeqsFp->excludeButAllowIf($assignmentMethod, "rtax");
		$read2SeqsFp->excludeButAllowIf($assignmentMethod, "rtax");
		$singleOk->excludeButAllowIf($assignmentMethod, "rtax");
		$noSingleOkGeneric->excludeButAllowIf($assignmentMethod, "rtax");
		$readIdRegex->excludeButAllowIf($assignmentMethod, "rtax");
		$ampliconIdRegex->excludeButAllowIf($assignmentMethod, "rtax");
		$headerIdRegex->excludeButAllowIf($assignmentMethod, "rtax");
		$confidence->excludeButAllowIf($assignmentMethod, "mothur");
		$confidence->excludeButAllowIf($assignmentMethod, "rdp");
		$rdpMaxMemory->excludeButAllowIf($assignmentMethod, "rdp");
//		$uclustMinConsensusFraction->excludeButAllowIf($assignmentMethod, "uclust");
//		$uclustSimilarity->excludeButAllowIf($assignmentMethod, "uclust");
//		$uclustMaxAccepts->excludeButAllowIf($assignmentMethod, "uclust");

		$idToTaxonomyFp = new OldFileParameter("--id_to_taxonomy_fp", $this->project);
			// TODO built in files: default: /macqiime/greengenes/gg_13_8_otus/taxonomy/97_otu_taxonomy.txt; 
		$treeFp = new OldFileParameter("--tree_fp", $this->project);

		$referenceSeqsFp = new OldFileParameter("--reference_seqs_fp", $this->project);
				// TODO built in files default: /macqiime/greengenes/gg_13_8_otus/rep_set/97_otus.fasta; 
		$blastDb = new OldFileParameter("--blast_db", $this->project);
				// TODO build in files
		$eitherBlastDatabase = $referenceSeqsFp->linkTo($blastDb);

		$idToTaxonomyFp->requireIf($assignmentMethod, "blast");
		$eitherBlastDatabase->requireIf($assignmentMethod, "blast");
		$treeFp->requireIf($assignmentMethod, "tax2tree");
		$treeFp->excludeButAllowIf($assignmentMethod, "tax2tree");

		$inputFastaFp = new OldFileParameter("--input_fasta_fp", $this->project);
		$inputFastaFp->requireIf();

		array_push($this->parameters,
			new Label("<p><strong>Required Parameters</strong></p>"),
			$inputFastaFp,
			new Label("<p><strong>Optional Parameters</strong></p>"),
			$assignmentMethod,
			$read1SeqsFp,
			$read2SeqsFp,
			$singleOk,
			$noSingleOkGeneric,
			$readIdRegex,
			$ampliconIdRegex,
			$headerIdRegex,
			$confidence,
			$rdpMaxMemory,
			$idToTaxonomyFp,
			$eitherBlastDatabase,
			$treeFp,
			new TextArgumentParameter("--e_value", "0.001", "/.*/"), // TODO potentially, but not necessarily, scientific notation
			new OldFileParameter("--training_data_properties_fp", $this->project),
				// TODO This option is overridden by the -t and -r options.
			new Label("<strong>Output Options</strong>"),
			new TrueFalseParameter("--verbose"),
			new NewFileParameter("--output_dir", "_assigned_taxonomy") // TODO dynamic default
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
		ob_start();
		echo "<p>{$this->getScriptTitle()}</p><p>This script takes OTUs and assigns them to a real-world taxonomy.  To do this, it uses sequence databases.</p>";
		include 'views/assign_taxonomy.html';
		return ob_get_clean();
	}

}
